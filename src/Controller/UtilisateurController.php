<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Service\CurrentUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UtilisateurController extends AbstractController
{
    #[Route('/utilisateurs', name: 'utilisateur_list')]
    public function listAction(): Response
    {
        return $this->render('Utilisateur/list.html.twig');
    }

    #[Route('/profil', name: 'profil_index')]
    public function profilAction(
        Request $request,
        CurrentUserProvider $currentUserProvider,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $currentUserProvider->getCurrentUser();

        if ($user === null) {
            throw $this->createNotFoundException('Aucun utilisateur courant.');
        }

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('info', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('profil_index');
        }

        return $this->render('Utilisateur/profil.html.twig', [
            'profilForm' => $form->createView(),
            'currentUser' => $user,
        ]);
    }

    #[Route('/admin/ajout', name: 'admin_add')]
    public function addAdminAction(): Response
    {
        return new Response('Formulaire ajout administrateur à venir');
    }
}