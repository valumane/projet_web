<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilType;
use App\Repository\ContenuPanierRepository;
use App\Repository\UserRepository;
use App\Service\CurrentUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UtilisateurController extends AbstractController
{
    #[Route('/utilisateurs', name: 'utilisateur_list')]
    public function listAction(
        UserRepository $userRepository,
        CurrentUserProvider $currentUserProvider
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        if ($currentUser === null || !$currentUser->isAdmin() || $currentUser->isSuperAdmin()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        return $this->render('Utilisateur/list.html.twig', [
            'users' => $userRepository->findAll(),
            'currentUser' => $currentUser,
        ]);
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

    #[Route('/utilisateurs/supprimer/{id}', name: 'utilisateur_delete', requirements: ['id' => '\d+'])]
    public function deleteAction(
        User $userToDelete,
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        if ($currentUser === null || !$currentUser->isAdmin() || $currentUser->isSuperAdmin()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        if ($userToDelete->isSuperAdmin()) {
            $this->addFlash('info', 'Impossible de supprimer un super-admin.');
            return $this->redirectToRoute('utilisateur_list');
        }

        if ($currentUser->getId() === $userToDelete->getId()) {
            $this->addFlash('info', 'Impossible de supprimer l’utilisateur courant.');
            return $this->redirectToRoute('utilisateur_list');
        }

        $contenusPanier = $contenuPanierRepository->findBy(['user' => $userToDelete]);

        foreach ($contenusPanier as $contenuPanier) {
            $entityManager->remove($contenuPanier);
        }

        $entityManager->remove($userToDelete);
        $entityManager->flush();

        $this->addFlash('info', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('utilisateur_list');
    }

    #[Route('/admin/ajout', name: 'admin_add')]
    public function addAdminAction(): Response
    {
        return new Response('Formulaire ajout administrateur à venir');
    }
}