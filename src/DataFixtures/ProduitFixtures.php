<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Repository\PaysRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private PaysRepository $paysRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $france = $this->paysRepository->findOneBy(['code' => 'FR']);
        $monaco = $this->paysRepository->findOneBy(['code' => 'MC']);
        $allemagne = $this->paysRepository->findOneBy(['code' => 'DE']);
        $italie = $this->paysRepository->findOneBy(['code' => 'IT']);
        $espagne = $this->paysRepository->findOneBy(['code' => 'ES']);
        $belgique = $this->paysRepository->findOneBy(['code' => 'BE']);

        $p1 = new Produit();
        $p1->setLibelle('2€ France');
        $p1->setPrixUnitaire('24.90');
        $p1->setQuantiteStock(8);
        $p1->addPays($france);
        $p1->addPays($belgique);
        $manager->persist($p1);

        $p2 = new Produit();
        $p2->setLibelle('2€ Monaco');
        $p2->setPrixUnitaire('3499.00');
        $p2->setQuantiteStock(1);
        $p2->addPays($monaco);
        $p2->addPays($france);
        $manager->persist($p2);

        $p3 = new Produit();
        $p3->setLibelle('2€ Allemagne');
        $p3->setPrixUnitaire('19.90');
        $p3->setQuantiteStock(12);
        $p3->addPays($allemagne);
        $p3->addPays($france);
        $manager->persist($p3);

        $p4 = new Produit();
        $p4->setLibelle('2€ Italie');
        $p4->setPrixUnitaire('17.50');
        $p4->setQuantiteStock(10);
        $p4->addPays($italie);
        $p4->addPays($france);
        $manager->persist($p4);

        $p5 = new Produit();
        $p5->setLibelle('2€ Espagne');
        $p5->setPrixUnitaire('16.90');
        $p5->setQuantiteStock(9);
        $p5->addPays($espagne);
        $p5->addPays($france);
        $manager->persist($p5);

        $p6 = new Produit();
        $p6->setLibelle('2€ Belgique');
        $p6->setPrixUnitaire('14.90');
        $p6->setQuantiteStock(15);
        $p6->addPays($belgique);
        $p6->addPays($france);
        $manager->persist($p6);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PaysFixtures::class,
        ];
    }
}