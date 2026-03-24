<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UtilisateurController extends AbstractController
{
    #[Route('/utilisateurs', name: 'utilisateur_list')]
    public function listAction(): Response
    {
        return $this->render('Utilisateur/list.html.twig');
    }

    #[Route('/profil', name: 'profil_index')]
    public function profilAction(): Response
    {
        return new Response('Page profil à venir');
    }

    #[Route('/admin/ajout', name: 'admin_add')]
    public function addAdminAction(): Response
    {
        return new Response('Formulaire ajout administrateur à venir');
    }
}