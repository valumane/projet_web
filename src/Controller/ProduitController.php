<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'produit_list')]
    public function listAction(): Response
    {
        return $this->render('Produit/list.html.twig');
    }

    #[Route('/produit/ajout', name: 'produit_add')]
    public function addAction(): Response
    {
        return new Response('Formulaire ajout produit à venir');
    }
}