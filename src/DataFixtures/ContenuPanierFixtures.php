<?php

namespace App\DataFixtures;

use App\Entity\ContenuPanier;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContenuPanierFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private ProduitRepository $produitRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $rita = $this->userRepository->findOneBy(['login' => 'rita']);
        $mathieu = $this->userRepository->findOneBy(['login' => 'mathieu']);
        $gaudi = $this->produitRepository->findOneBy(['libelle' => '2€ Espagne']);
        $atomium = $this->produitRepository->findOneBy(['libelle' => '2€ Belgique']);
        $mur = $this->produitRepository->findOneBy(['libelle' => '2€ Allemagne']);
        $dante = $this->produitRepository->findOneBy(['libelle' => '2€ Italie']);

        
        $cp1 = new ContenuPanier();
        $cp1->setUser($rita)
            ->setProduit($gaudi)
            ->setQuantite(1);
        $manager->persist($cp1);

        $cp2 = new ContenuPanier();
        $cp2->setUser($rita)
            ->setProduit($atomium)
            ->setQuantite(2);
        $manager->persist($cp2);

        $cp3 = new ContenuPanier();
        $cp3->setUser($mathieu)
            ->setProduit($mur)
            ->setQuantite(1);
        $manager->persist($cp3);

        $cp4 = new ContenuPanier();
        $cp4->setUser($mathieu)
            ->setProduit($dante)
            ->setQuantite(1);
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