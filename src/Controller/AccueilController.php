<?php

namespace App\Controller;

use App\Repository\ContenuPanierRepository;
use App\Service\CurrentUserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{



/** 
 * affiche la page d'accuieil du site
 * recupere l'user courant via CurrentUserProvider 
 * et l'envoie a la vue pour afficher ses infos
*/
    #[Route('/', name: 'accueil_index')]
    public function indexAction(CurrentUserProvider $currentUserProvider): Response
    {
        return $this->render('Site/accueil.html.twig', [
            'currentUser' => $currentUserProvider->getCurrentUser(),
        ]);
    }


/**
 * action qui construit le menu du site
 * si l'utilisateur courant est un client cacule aussi
 *  le nombre total d'articles dans son panier
 */
    public function menuAction(
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();
        $nbArticlesPanier = null;

        //compteur affiché que pour un client connecté non admin et non sadmin
        if (
            $currentUser !== null
            && !$currentUser->isAdmin()
            && !$currentUser->isSuperAdmin()
        ) {
            $contenusPanier = $contenuPanierRepository->findBy(['user' => $currentUser]);

            //somme des quantité 
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
 * nom prenom role
*/
    public function headerAction(CurrentUserProvider $currentUserProvider): Response
    {
        return $this->render('Layouts/_header.html.twig', [
            'currentUser' => $currentUserProvider->getCurrentUser(),
        ]);
    }
}