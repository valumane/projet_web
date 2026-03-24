<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ContenuPanierRepository;
use App\Repository\ProduitRepository;
use App\Service\CurrentUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'produit_list', methods: ['GET'])]
    public function listAction(
        ProduitRepository $produitRepository,
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();
        $contenusPanier = [];

        if ($currentUser !== null) {
            $lignesPanier = $contenuPanierRepository->findBy(['user' => $currentUser]);

            foreach ($lignesPanier as $ligne) {
                $contenusPanier[$ligne->getProduit()->getId()] = $ligne;
            }
        }

        return $this->render('Produit/list.html.twig', [
            'produits' => $produitRepository->findAll(),
            'currentUser' => $currentUser,
            'contenusPanier' => $contenusPanier,
        ]);
    }

    #[Route('/produits/panier/{id}', name: 'produit_panier_update', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function updatePanierAction(
        Produit $produit,
        Request $request,
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        if ($currentUser === null) {
            $this->addFlash('info', 'Aucun utilisateur courant.');
            return $this->redirectToRoute('produit_list');
        }

        $delta = (int) $request->request->get('quantite', 0);

        $contenuPanier = $contenuPanierRepository->findOneBy([
            'user' => $currentUser,
            'produit' => $produit,
        ]);

        $quantiteDansPanier = $contenuPanier?->getQuantite() ?? 0;
        $stockDisponible = $produit->getQuantiteStock();

        if ($delta === 0) {
            $this->addFlash('info', 'Aucune modification effectuée.');
            return $this->redirectToRoute('produit_list');
        }

        if ($delta > $stockDisponible) {
            $this->addFlash('info', 'Quantité demandée supérieure au stock disponible.');
            return $this->redirectToRoute('produit_list');
        }

        if ($delta < 0 && abs($delta) > $quantiteDansPanier) {
            $this->addFlash('info', 'Impossible de retirer plus que la quantité présente dans le panier.');
            return $this->redirectToRoute('produit_list');
        }

        if ($delta > 0) {
            if ($contenuPanier === null) {
                $contenuPanier = new ContenuPanier();
                $contenuPanier->setUser($currentUser);
                $contenuPanier->setProduit($produit);
                $contenuPanier->setQuantite(0);
                $entityManager->persist($contenuPanier);
            }

            $contenuPanier->setQuantite($quantiteDansPanier + $delta);
            $produit->setQuantiteStock($stockDisponible - $delta);
        } else {
            $retrait = abs($delta);
            $nouvelleQuantite = $quantiteDansPanier - $retrait;

            $produit->setQuantiteStock($stockDisponible + $retrait);

            if ($nouvelleQuantite <= 0) {
                if ($contenuPanier !== null) {
                    $entityManager->remove($contenuPanier);
                }
            } else {
                $contenuPanier->setQuantite($nouvelleQuantite);
            }
        }

        $entityManager->flush();

        $this->addFlash('info', 'Panier mis à jour avec succès.');

        return $this->redirectToRoute('produit_list');
    }

    #[Route('/produit/ajout', name: 'produit_add')]
    public function addAction(
        Request $request,
        CurrentUserProvider $currentUserProvider,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        if ($currentUser === null || !$currentUser->isAdmin() || $currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('info', 'Produit ajouté avec succès.');

            return $this->redirectToRoute('produit_list');
        }

        return $this->render('Produit/ajout.html.twig', [
            'produitForm' => $form->createView(),
            'currentUser' => $currentUser,
        ]);
    }
}