<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Pays;


class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    public const PIECE_2E_FR_2008 = 'produit-2e-fr-2008';
    public const PIECE_2E_MC_2007 = 'produit-2e-mc-2007';
    public const PIECE_2E_DE_MUR = 'produit-2e-de-mur';
    public const PIECE_2E_IT_DANTE = 'produit-2e-it-dante';
    public const PIECE_2E_ES_GAUDI = 'produit-2e-es-gaudi';
    public const PIECE_2E_BE_ATOMIUM = 'produit-2e-be-atomium';

    public function load(ObjectManager $manager): void
    {
        $p1 = new Produit();
        $p1->setLibelle('2€ commémorative France 2008 - Présidence UE');
        $p1->setPrixUnitaire('24.90');
        $p1->setQuantiteStock(8);
        $p1->addPays($this->getReference(PaysFixtures::FRANCE, Pays::class));
        $p1->addPays($this->getReference(PaysFixtures::BELGIQUE, Pays::class));
        $manager->persist($p1);
        $this->addReference(self::PIECE_2E_FR_2008, $p1);

        $p2 = new Produit();
        $p2->setLibelle('2€ rare Monaco 2007 - Grace Kelly');
        $p2->setPrixUnitaire('3499.00');
        $p2->setQuantiteStock(1);
        $p2->addPays($this->getReference(PaysFixtures::MONACO, Pays::class));
        $p2->addPays($this->getReference(PaysFixtures::FRANCE, Pays::class));
        $manager->persist($p2);
        $this->addReference(self::PIECE_2E_MC_2007, $p2);

        $p3 = new Produit();
        $p3->setLibelle('2€ commémorative Allemagne - Chute du mur');
        $p3->setPrixUnitaire('19.90');
        $p3->setQuantiteStock(12);
        $p3->addPays($this->getReference(PaysFixtures::ALLEMAGNE, Pays::class));
        $p3->addPays($this->getReference(PaysFixtures::FRANCE, Pays::class));
        $manager->persist($p3);
        $this->addReference(self::PIECE_2E_DE_MUR, $p3);

        $p4 = new Produit();
        $p4->setLibelle('2€ commémorative Italie - Dante');
        $p4->setPrixUnitaire('17.50');
        $p4->setQuantiteStock(10);
        $p4->addPays($this->getReference(PaysFixtures::ITALIE, Pays::class));
        $p4->addPays($this->getReference(PaysFixtures::FRANCE, Pays::class));
        $manager->persist($p4);
        $this->addReference(self::PIECE_2E_IT_DANTE, $p4);

        $p5 = new Produit();
        $p5->setLibelle('2€ commémorative Espagne - Gaudí');
        $p5->setPrixUnitaire('16.90');
        $p5->setQuantiteStock(9);
        $p5->addPays($this->getReference(PaysFixtures::ESPAGNE, Pays::class));
        $p5->addPays($this->getReference(PaysFixtures::FRANCE, Pays::class));
        $manager->persist($p5);
        $this->addReference(self::PIECE_2E_ES_GAUDI, $p5);

        $p6 = new Produit();
        $p6->setLibelle('2€ commémorative Belgique - Atomium');
        $p6->setPrixUnitaire('14.90');
        $p6->setQuantiteStock(15);
        $p6->addPays($this->getReference(PaysFixtures::BELGIQUE, Pays::class));
        $p6->addPays($this->getReference(PaysFixtures::FRANCE, Pays::class));
        $manager->persist($p6);
        $this->addReference(self::PIECE_2E_BE_ATOMIUM, $p6);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PaysFixtures::class,
        ];
    }
}