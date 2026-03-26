<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ContenuPanierRepository;
use App\Service\CurrentUserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    /**
     * renvoie le vrai user connecte si Security est deja branchee
     * sinon retombe sur le faux user courant de la V1
     */
    private function getConnectedUserOrFallback(CurrentUserProvider $currentUserProvider): ?User
    {
        $user = $this->getUser();

        if ($user instanceof User) {
            return $user;
        }

        return $currentUserProvider->getCurrentUser();
    }

    /**
     * affiche la page d'accueil du site
     * envoie a la vue l'user courant reel si disponible
     * sinon l'user courant fictif de la V1
     */
    #[Route('/', name: 'accueil_index')]
    public function indexAction(CurrentUserProvider $currentUserProvider): Response
    {
        $currentUser = $this->getConnectedUserOrFallback($currentUserProvider);

        return $this->render('Site/accueil.html.twig', [
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * construit le menu du site
     * si l'user courant n'est pas super-admin
     * calcule aussi le nombre total d'articles dans son panier
     */
    public function menuAction(
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository
    ): Response {
        $currentUser = $this->getConnectedUserOrFallback($currentUserProvider);
        $nbArticlesPanier = null;

        if ($currentUser !== null && !$currentUser->isSuperAdmin()) {
            $contenusPanier = $contenuPanierRepository->findBy([
                'user' => $currentUser,
            ]);

            $nbArticlesPanier = 0;
            foreach ($contenusPanier as $contenuPanier) {
                $nbArticlesPanier += $contenuPanier->getQuantite();
            }
        }

        return $this->render('Layouts/_menu.html.twig', [
            'currentUser' => $currentUser,
            'nbArticlesPanier' => $nbArticlesPanier,
        ]);
    }

    /**
     * affiche l'entete du site
     * envoie l'user courant au header pour afficher
     * login et role
     */
    public function headerAction(CurrentUserProvider $currentUserProvider): Response
    {
        $currentUser = $this->getConnectedUserOrFallback($currentUserProvider);

        return $this->render('Layouts/_header.html.twig', [
            'currentUser' => $currentUser,
        ]);
    }
}