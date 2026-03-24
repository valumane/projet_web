<?php

namespace App\Controller;

use App\Service\CurrentUserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil_index')]
    public function indexAction(CurrentUserProvider $currentUserProvider): Response
    {
        return $this->render('Site/accueil.html.twig', [
            'currentUser' => $currentUserProvider->getCurrentUser(),
        ]);
    }

    public function menuAction(CurrentUserProvider $currentUserProvider): Response
    {
        return $this->render('Layouts/_menu.html.twig', [
            'currentUser' => $currentUserProvider->getCurrentUser(),
        ]);
    }

    public function headerAction(CurrentUserProvider $currentUserProvider): Response
    {
        return $this->render('Layouts/_header.html.twig', [
            'currentUser' => $currentUserProvider->getCurrentUser(),
        ]);
    }
}