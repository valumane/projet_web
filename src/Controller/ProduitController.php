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
    /**
     * affiche la liste des produits
     * recupere aussi l'user courant
     * et ses lignes de panier pour savoir
     * quelle quantité il a deja par produit
     */
    #[Route('/produits', name: 'produit_list', methods: ['GET'])]
    public function listAction(
        ProduitRepository $produitRepository,
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();
        $contenusPanier = [];

        // si un user est connecté on recupere ses lignes de panier
        if ($currentUser !== null) {
            $lignesPanier = $contenuPanierRepository->findBy(['user' => $currentUser]);

            // tableau indexé par id produit pour acces rapide dans la vue
            foreach ($lignesPanier as $ligne) {
                $contenusPanier[$ligne->getProduit()->getId()] = $ligne;
            }
        }

        // bloque l'acces a /produits via l'url pour le super-admin
        if ($currentUser !== null && $currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        return $this->render('Produit/list.html.twig', [
            'produits' => $produitRepository->findAll(),
            'currentUser' => $currentUser,
            'contenusPanier' => $contenusPanier,
        ]);
    }

    /**
     * met a jour le panier pour un produit
     * ajoute ou retire une quantité selon la valeur envoyée
     * met aussi a jour le stock du produit
     */
    #[Route('/produits/panier/{id}', name: 'produit_panier_update', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function updatePanierAction(
        Produit $produit,
        Request $request,
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        // refuse si aucun user courant ou si user est super admin
        if ($currentUser === null || $currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('produit_list');
        }

        // quantité demandée dans le formulaire
        $delta = (int) $request->request->get('quantite', 0);

        // cherche si une ligne existe deja pour ce produit dans le panier
        $contenuPanier = $contenuPanierRepository->findOneBy([
            'user' => $currentUser,
            'produit' => $produit,
        ]);

        $quantiteDansPanier = $contenuPanier?->getQuantite() ?? 0;
        $stockDisponible = $produit->getQuantiteStock();

        // refuse si aucune modif demandée
        if ($delta === 0) {
            $this->addFlash('info', 'Aucune modification effectuée.');
            return $this->redirectToRoute('produit_list');
        }

        // refuse si on veut ajouter plus que le stock
        if ($delta > $stockDisponible) {
            $this->addFlash('info', 'Quantité demandée supérieure au stock disponible.');
            return $this->redirectToRoute('produit_list');
        }

        // refuse si on veut retirer plus que ce qu'il y a deja dans le panier
        if ($delta < 0 && abs($delta) > $quantiteDansPanier) {
            $this->addFlash('info', 'Impossible de retirer plus que la quantité présente dans le panier.');
            return $this->redirectToRoute('produit_list');
        }

        // cas ajout au panier
        if ($delta > 0) {
            // cree une ligne si elle n'existe pas encore
            if ($contenuPanier === null) {
                $contenuPanier = new ContenuPanier();
                $contenuPanier->setUser($currentUser);
                $contenuPanier->setProduit($produit);
                $contenuPanier->setQuantite(0);
                $entityManager->persist($contenuPanier);
            }

            // augmente la quantité dans le panier
            // et diminue le stock
            $contenuPanier->setQuantite($quantiteDansPanier + $delta);
            $produit->setQuantiteStock($stockDisponible - $delta);
        } else {
            // cas retrait du panier
            $retrait = abs($delta);
            $nouvelleQuantite = $quantiteDansPanier - $retrait;

            // remet la quantité retirée dans le stock
            $produit->setQuantiteStock($stockDisponible + $retrait);

            // si la quantité tombe a 0 on supprime la ligne
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

    /**
     * affiche le formulaire d'ajout de produit
     * reserve l'acces aux admins simples
     * enregistre le produit si le formulaire est valide
     */
    #[Route('/produit/ajout', name: 'produit_add')]
    public function addAction(
        Request $request,
        CurrentUserProvider $currentUserProvider,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();


        // refuse si super admin
        if ($currentUser !== null && $currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        $contenusPanier = [];


        // refuse si pas admin simple
        if ($currentUser === null || !$currentUser->isAdmin() || $currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        // si le form est valide on ajoute le produit en bdd
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