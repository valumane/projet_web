<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Service\CurrentUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'produit_list')]
    public function listAction(ProduitRepository $produitRepository): Response
    {
        return $this->render('Produit/list.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/produit/ajout', name: 'produit_add')]
    public function addAction(
        Request $request,
        CurrentUserProvider $currentUserProvider,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $currentUserProvider->getCurrentUser();

        if ($currentUser === null || !$currentUser->isAdmin() || $currentUser->isSuperAdmin()) {
            $this->addFlash('info', 'Accès refusé.');
            return $this->redirectToRoute('accueil_index');
        }

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('info', 'Produit ajouté avec succès.');

            return $this->redirectToRoute('produit_list');
        }

        return $this->render('Produit/ajout.html.twig', [
            'produitForm' => $form->createView(),
            'currentUser' => $currentUser,
        ]);
    }
}