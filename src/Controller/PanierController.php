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
     * affiche le panier de l'user courant
     * recupere ses lignes de panier
     * calcule aussi le total a payer
     */
    #[Route('/panier', name: 'panier_index', methods: ['GET'])]
    public function indexAction(
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        // si aucun user courant on redirige vers l'accueil
        if ($currentUser === null) {
            $this->addFlash('info', 'Aucun utilisateur courant.');
            return $this->redirectToRoute('accueil_index');
        }

        // securité : on refuse si l'user est super admin
        if ($currentUser === null || $currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        // recupere toutes les lignes du panier du user
        $contenusPanier = $contenuPanierRepository->findBy(['user' => $currentUser]);

        // calcule le total du panier
        $total = 0.0;
        foreach ($contenusPanier as $contenuPanier) {
            $total += $contenuPanier->getQuantite() * (float) $contenuPanier->getProduit()->getPrixUnitaire();
        }

        return $this->render('Panier/index.html.twig', [
            'contenusPanier' => $contenusPanier,
            'total' => $total,
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * supprime une ligne du panier
     * verifie que la ligne appartient bien a l'user courant
     * remet aussi la quantite dans le stock du produit
     */
    #[Route('/panier/supprimer/{id}', name: 'panier_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function deleteAction(
        ContenuPanier $contenuPanier,
        CurrentUserProvider $currentUserProvider,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        // securite : on refuse si la ligne n'appartient pas a l'user courant
        if (
            $currentUser === null
            || $currentUser->isSuperAdmin()
            || $contenuPanier->getUser()?->getId() !== $currentUser->getId()
        ) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('panier_index');
        }

        // securité : on refuse si l'user est super admin
        if ($currentUser === null || $currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        // remet la quantite du panier dans le stock
        $produit = $contenuPanier->getProduit();
        $produit->setQuantiteStock($produit->getQuantiteStock() + $contenuPanier->getQuantite());

        // supprime la ligne
        $entityManager->remove($contenuPanier);
        $entityManager->flush();

        $this->addFlash('info', 'Ligne supprimée du panier.');

        return $this->redirectToRoute('panier_index');
    }

    /**
     * vide tout le panier de l'user courant
     * remet chaque quantite dans le stock
     * puis supprime toutes les lignes
     */
    #[Route('/panier/vider', name: 'panier_clear', methods: ['GET'])]
    public function clearAction(
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        // si aucun user courant on redirige
        if ($currentUser === null) {
            $this->addFlash('info', 'Aucun utilisateur courant.');
            return $this->redirectToRoute('accueil_index');
        }

        // securité : on refuse si l'user est super admin
        if ($currentUser === null || $currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        // recupere toutes les lignes du panier
        $contenusPanier = $contenuPanierRepository->findBy(['user' => $currentUser]);

        // remet les quantites dans le stock puis supprime les lignes
        foreach ($contenusPanier as $contenuPanier) {
            $produit = $contenuPanier->getProduit();
            $produit->setQuantiteStock($produit->getQuantiteStock() + $contenuPanier->getQuantite());
            $entityManager->remove($contenuPanier);
        }

        $entityManager->flush();

        $this->addFlash('info', 'Panier vidé avec succès.');

        return $this->redirectToRoute('panier_index');
    }

    /**
     * simule une commande
     * verifie que le panier n'est pas vide
     * puis supprime les lignes du panier
     */
    #[Route('/panier/commander', name: 'panier_order', methods: ['GET'])]
    public function orderAction(
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        // si aucun user courant on redirige
        if ($currentUser === null) {
            $this->addFlash('info', 'Aucun utilisateur courant.');
            return $this->redirectToRoute('accueil_index');
        }


        // securité : on refuse si l'user est super admin
        if ($currentUser === null || $currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }


        // recupere le panier du user
        $contenusPanier = $contenuPanierRepository->findBy(['user' => $currentUser]);

        // refuse si le panier est vide
        if (count($contenusPanier) === 0) {
            $this->addFlash('info', 'Le panier est vide.');
            return $this->redirectToRoute('panier_index');
        }

        // supprime les lignes pour simuler une commande validee
        foreach ($contenusPanier as $contenuPanier) {
            $entityManager->remove($contenuPanier);
        }

        $entityManager->flush();

        $this->addFlash('info', 'Commande simulée enregistrée avec succès.');

        return $this->redirectToRoute('panier_index');
    }
}