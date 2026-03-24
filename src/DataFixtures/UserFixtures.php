<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Pays;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_SADMIN = 'user-sadmin';
    public const USER_GILLES = 'user-gilles';
    public const USER_RITA = 'user-rita';
    public const USER_MATHIEU = 'user-mathieu';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $sadmin = new User();
        $sadmin->setLogin('sadmin');
        $sadmin->setPassword($this->passwordHasher->hashPassword($sadmin, 'nimdas'));
        $sadmin->setNom('Admin');
        $sadmin->setPrenom('Super');
        $sadmin->setDateNaissance(new \DateTime('1990-01-01'));
        $sadmin->setIsAdmin(true);
        $sadmin->setIsSuperAdmin(true);
        $sadmin->setPays($this->getReference(PaysFixtures::FRANCE, Pays::class));
        $sadmin->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($sadmin);
        $this->addReference(self::USER_SADMIN, $sadmin);

        $gilles = new User();
        $gilles->setLogin('gilles');
        $gilles->setPassword($this->passwordHasher->hashPassword($gilles, 'sellig'));
        $gilles->setNom('Martin');
        $gilles->setPrenom('Gilles');
        $gilles->setDateNaissance(new \DateTime('1992-05-10'));
        $gilles->setIsAdmin(true);
        $gilles->setIsSuperAdmin(false);
        $gilles->setPays($this->getReference(PaysFixtures::BELGIQUE, Pays::class));
        $gilles->setRoles(['ROLE_ADMIN']);
        $manager->persist($gilles);
        $this->addReference(self::USER_GILLES, $gilles);

        $rita = new User();
        $rita->setLogin('rita');
        $rita->setPassword($this->passwordHasher->hashPassword($rita, 'atir'));
        $rita->setNom('Durand');
        $rita->setPrenom('Rita');
        $rita->setDateNaissance(new \DateTime('1998-08-20'));
        $rita->setIsAdmin(false);
        $rita->setIsSuperAdmin(false);
        $rita->setPays($this->getReference(PaysFixtures::ESPAGNE, Pays::class));
        $rita->setRoles([]);
        $manager->persist($rita);
        $this->addReference(self::USER_RITA, $rita);

        $mathieu = new User();
        $mathieu->setLogin('mathieu');
        $mathieu->setPassword($this->passwordHasher->hashPassword($mathieu, 'ueihtam'));
        $mathieu->setNom('Bernard');
        $mathieu->setPrenom('Mathieu');
        $mathieu->setDateNaissance(new \DateTime('1996-03-15'));
        $mathieu->setIsAdmin(false);
        $mathieu->setIsSuperAdmin(false);
        $mathieu->setPays(null);
        $mathieu->setRoles([]);
        $manager->persist($mathieu);
        $this->addReference(self::USER_MATHIEU, $mathieu);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PaysFixtures::class,
        ];
    }
}