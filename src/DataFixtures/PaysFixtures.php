<?php

namespace App\DataFixtures;

use App\Entity\Pays;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaysFixtures extends Fixture
{
    public const FRANCE = 'pays-france';
    public const MONACO = 'pays-monaco';
    public const ALLEMAGNE = 'pays-allemagne';
    public const ITALIE = 'pays-italie';
    public const ESPAGNE = 'pays-espagne';
    public const BELGIQUE = 'pays-belgique';

    public function load(ObjectManager $manager): void
    {
        $france = new Pays();
        $france->setNom('France');
        $france->setCode('FR');
        $manager->persist($france);
        $this->addReference(self::FRANCE, $france);

        $monaco = new Pays();
        $monaco->setNom('Monaco');
        $monaco->setCode('MC');
        $manager->persist($monaco);
        $this->addReference(self::MONACO, $monaco);

        $allemagne = new Pays();
        $allemagne->setNom('Allemagne');
        $allemagne->setCode('DE');
        $manager->persist($allemagne);
        $this->addReference(self::ALLEMAGNE, $allemagne);

        $italie = new Pays();
        $italie->setNom('Italie');
        $italie->setCode('IT');
        $manager->persist($italie);
        $this->addReference(self::ITALIE, $italie);

        $espagne = new Pays();
        $espagne->setNom('Espagne');
        $espagne->setCode('ES');
        $manager->persist($espagne);
        $this->addReference(self::ESPAGNE, $espagne);

        $belgique = new Pays();
        $belgique->setNom('Belgique');
        $belgique->setCode('BE');
        $manager->persist($belgique);
        $this->addReference(self::BELGIQUE, $belgique);

        $manager->flush();
    }
}