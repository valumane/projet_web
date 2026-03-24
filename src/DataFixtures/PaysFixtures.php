<?php

namespace App\DataFixtures;

use App\Entity\Pays;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaysFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $france = new Pays();
        $france->setNom('France');
        $france->setCode('FR');
        $manager->persist($france);

        $monaco = new Pays();
        $monaco->setNom('Monaco');
        $monaco->setCode('MC');
        $manager->persist($monaco);

        $allemagne = new Pays();
        $allemagne->setNom('Allemagne');
        $allemagne->setCode('DE');
        $manager->persist($allemagne);

        $italie = new Pays();
        $italie->setNom('Italie');
        $italie->setCode('IT');
        $manager->persist($italie);

        $espagne = new Pays();
        $espagne->setNom('Espagne');
        $espagne->setCode('ES');
        $manager->persist($espagne);

        $belgique = new Pays();
        $belgique->setNom('Belgique');
        $belgique->setCode('BE');
        $manager->persist($belgique);

        $manager->flush();
    }
}