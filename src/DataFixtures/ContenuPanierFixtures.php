<?php

namespace App\DataFixtures;

use App\Entity\ContenuPanier;
use App\Entity\User;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContenuPanierFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $cp1 = new ContenuPanier();
        $cp1->setUser($this->getReference(UserFixtures::USER_RITA,User::class));
        $cp1->setProduit($this->getReference(ProduitFixtures::PIECE_2E_ES_GAUDI,Produit::class));
        $cp1->setQuantite(1);
        $manager->persist($cp1);

        $cp2 = new ContenuPanier();
        $cp2->setUser($this->getReference(UserFixtures::USER_RITA,User::class));
        $cp2->setProduit($this->getReference(ProduitFixtures::PIECE_2E_BE_ATOMIUM,Produit::class));
        $cp2->setQuantite(2);
        $manager->persist($cp2);

        $cp3 = new ContenuPanier();
        $cp3->setUser($this->getReference(UserFixtures::USER_MATHIEU,User::class));
        $cp3->setProduit($this->getReference(ProduitFixtures::PIECE_2E_DE_MUR,Produit::class));
        $cp3->setQuantite(1);
        $manager->persist($cp3);

        $cp4 = new ContenuPanier();
        $cp4->setUser($this->getReference(UserFixtures::USER_MATHIEU,User::class));
        $cp4->setProduit($this->getReference(ProduitFixtures::PIECE_2E_IT_DANTE,Produit::class));
        $cp4->setQuantite(1);
        $manager->persist($cp4);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ProduitFixtures::class,
        ];
    }
}