<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil_index')]
    public function indexAction(): Response
    {
        return $this->render('site/accueil.html.twig');
    }

    public function menuAction(): Response
    {
        return $this->render('Layouts/_menu.html.twig');
    }

}
