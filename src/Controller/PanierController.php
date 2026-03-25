<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Repository\ContenuPanierRepository;
use App\Service\CurrentUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    /**
     * Affiche le panier de l'utilisateur courant.
     * Récupère ses lignes de panier et calcule le total.
     */
    #[Route('/panier', name: 'panier_index', methods: ['GET'])]
    public function indexAction(
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        if ($currentUser === null) {
            $this->addFlash('info', 'Aucun utilisateur courant.');
            return $this->redirectToRoute('accueil_index');
        }

        if ($currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        $contenusPanier = $contenuPanierRepository->findBy([
            'user' => $currentUser,
        ]);

        $total = 0.0;
        foreach ($contenusPanier as $contenuPanier) {
            $total += $contenuPanier->getQuantite()
                * (float) $contenuPanier->getProduit()->getPrixUnitaire();
        }

        return $this->render('Panier/index.html.twig', [
            'contenusPanier' => $contenusPanier,
            'total' => $total,
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * Supprime une ligne du panier.
     * Vérifie que la ligne appartient bien à l'utilisateur courant.
     * Remet aussi la quantité dans le stock du produit.
     */
    #[Route('/panier/supprimer/{id}', name: 'panier_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function deleteAction(
        ContenuPanier $contenuPanier,
        CurrentUserProvider $currentUserProvider,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        if ($currentUser === null) {
            $this->addFlash('info', 'Aucun utilisateur courant.');
            return $this->redirectToRoute('accueil_index');
        }

        if ($currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        if ($contenuPanier->getUser()?->getId() !== $currentUser->getId()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('panier_index');
        }

        $produit = $contenuPanier->getProduit();
        $produit->setQuantiteStock(
            $produit->getQuantiteStock() + $contenuPanier->getQuantite()
        );

        $entityManager->remove($contenuPanier);
        $entityManager->flush();

        $this->addFlash('info', 'Ligne supprimée du panier.');

        return $this->redirectToRoute('panier_index');
    }

    /**
     * Vide tout le panier de l'utilisateur courant.
     * Remet chaque quantité dans le stock puis supprime toutes les lignes.
     */
    #[Route('/panier/vider', name: 'panier_clear', methods: ['GET'])]
    public function clearAction(
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        if ($currentUser === null) {
            $this->addFlash('info', 'Aucun utilisateur courant.');
            return $this->redirectToRoute('accueil_index');
        }

        if ($currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        $contenusPanier = $contenuPanierRepository->findBy([
            'user' => $currentUser,
        ]);

        foreach ($contenusPanier as $contenuPanier) {
            $produit = $contenuPanier->getProduit();
            $produit->setQuantiteStock(
                $produit->getQuantiteStock() + $contenuPanier->getQuantite()
            );
            $entityManager->remove($contenuPanier);
        }

        $entityManager->flush();

        $this->addFlash('info', 'Panier vidé avec succès.');

        return $this->redirectToRoute('panier_index');
    }

    /**
     * Simule une commande.
     * Vérifie que le panier n'est pas vide puis supprime les lignes du panier.
     */
    #[Route('/panier/commander', name: 'panier_order', methods: ['GET'])]
    public function orderAction(
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        if ($currentUser === null) {
            $this->addFlash('info', 'Aucun utilisateur courant.');
            return $this->redirectToRoute('accueil_index');
        }

        if ($currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        $contenusPanier = $contenuPanierRepository->findBy([
            'user' => $currentUser,
        ]);

        if (count($contenusPanier) === 0) {
            $this->addFlash('info', 'Le panier est vide.');
            return $this->redirectToRoute('panier_index');
        }

        foreach ($contenusPanier as $contenuPanier) {
            $entityManager->remove($contenuPanier);
        }

        $entityManager->flush();

        $this->addFlash('info', 'Commande simulée enregistrée avec succès.');

        return $this->redirectToRoute('panier_index');
    }
}