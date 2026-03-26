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
use App\Form\AdminType;

final class UtilisateurController extends AbstractController
{
    /**
     * affiche la liste des users
     * acces reservé a un admin simple
     * envoie aussi l'user courant a la vue
     */
    #[Route('/utilisateurs', name: 'utilisateur_list')]
    public function listAction(
        UserRepository $userRepository,
        CurrentUserProvider $currentUserProvider
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        // refuse si pas admin simple
        if ($currentUser === null || !$currentUser->isAdmin() || $currentUser->isSuperAdmin()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        return $this->render('Utilisateur/list.html.twig', [
            'users' => $userRepository->findAll(),
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * affiche et traite le form de profil
     * permet a l'user courant de modifier
     * ses infos et son mdp
     */
    #[Route('/profil', name: 'profil_index')]
    public function profilAction(
        Request $request,
        CurrentUserProvider $currentUserProvider,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $currentUserProvider->getCurrentUser();

        if ($user === null) {
            throw new \LogicException('Utilisateur authentifié introuvable.');
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

    /**
     * supprime un user
     * acces reservé a un admin simple
     * interdit de supprimer un sadmin
     * ou l'user courant
     */
    #[Route('/utilisateurs/supprimer/{id}', name: 'utilisateur_delete', requirements: ['id' => '\d+'])]
    public function deleteAction(
        User $userToDelete,
        CurrentUserProvider $currentUserProvider,
        ContenuPanierRepository $contenuPanierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        // refuse si pas admin simple
        if ($currentUser === null || !$currentUser->isAdmin() || $currentUser->isSuperAdmin()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        // interdit de supprimer un sadmin
        if ($userToDelete->isSuperAdmin()) {
            $this->addFlash('info', 'Impossible de supprimer un super-admin.');
            return $this->redirectToRoute('utilisateur_list');
        }

        // interdit de se supprimer soi meme
        if ($currentUser->getId() === $userToDelete->getId()) {
            $this->addFlash('info', 'Impossible de supprimer l’utilisateur courant.');
            return $this->redirectToRoute('utilisateur_list');
        }

        // vider proprement le panier du user en remettant le stock
        $contenusPanier = $contenuPanierRepository->findBy([
            'user' => $userToDelete,
        ]);

        foreach ($contenusPanier as $contenuPanier) {
            $produit = $contenuPanier->getProduit();
            $produit->setQuantiteStock(
                $produit->getQuantiteStock() + $contenuPanier->getQuantite()
            );

            $entityManager->remove($contenuPanier);
        }

        $entityManager->remove($userToDelete);
        $entityManager->flush();

        $this->addFlash('info', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('utilisateur_list');
    }

    /**
     * affiche et traite le form d'ajout d'admin
     * acces reservé au sadmin
     * cree un nv user avec role admin
     */
    #[Route('/admin/ajout', name: 'admin_add')]
    public function addAdminAction(
        Request $request,
        CurrentUserProvider $currentUserProvider,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        // refuse si pas sadmin
        if ($currentUser === null || !$currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        $admin = new User();
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        // si le form est valide on cree le nv admin
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            $admin->setPassword($passwordHasher->hashPassword($admin, $plainPassword));
            $admin->setIsAdmin(true);
            $admin->setIsSuperAdmin(false);
            $admin->setRoles(['ROLE_ADMIN']);

            $entityManager->persist($admin);
            $entityManager->flush();

            $this->addFlash('info', 'Administrateur ajouté avec succès.');

            return $this->redirectToRoute('admin_add');
        }

        return $this->render('Utilisateur/ajout_admin.html.twig', [
            'adminForm' => $form->createView(),
            'currentUser' => $currentUser,
        ]);
    }
}