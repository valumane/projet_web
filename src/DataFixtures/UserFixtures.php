<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\PaysRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private PaysRepository $paysRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $france = $this->paysRepository->findOneBy(['code' => 'FR']);
        $belgique = $this->paysRepository->findOneBy(['code' => 'BE']);
        $espagne = $this->paysRepository->findOneBy(['code' => 'ES']);

        $sadmin = new User();
        $sadmin->setLogin('sadmin');
        $sadmin->setPassword($this->passwordHasher->hashPassword($sadmin, 'nimdas'));
        $sadmin->setNom('Admin');
        $sadmin->setPrenom('Super');
        $sadmin->setDateNaissance(new \DateTime('1990-01-01'));
        $sadmin->setIsAdmin(true);
        $sadmin->setIsSuperAdmin(true);
        $sadmin->setPays($france);
        $sadmin->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($sadmin);

        $gilles = new User();
        $gilles->setLogin('gilles');
        $gilles->setPassword($this->passwordHasher->hashPassword($gilles, 'sellig'));
        $gilles->setNom('Subrenat');
        $gilles->setPrenom('Gilles');
        $gilles->setDateNaissance(new \DateTime('1992-05-10'));
        $gilles->setIsAdmin(true);
        $gilles->setIsSuperAdmin(false);
        $gilles->setPays($belgique);
        $gilles->setRoles(['ROLE_ADMIN']);
        $manager->persist($gilles);

        $rita = new User();
        $rita->setLogin('rita');
        $rita->setPassword($this->passwordHasher->hashPassword($rita, 'atir'));
        $rita->setNom('Zrour');
        $rita->setPrenom('Rita');
        $rita->setDateNaissance(new \DateTime('1998-08-20'));
        $rita->setIsAdmin(false);
        $rita->setIsSuperAdmin(false);
        $rita->setPays($espagne);
        $rita->setRoles([]);
        $manager->persist($rita);

        $mathieu = new User();
        $mathieu->setLogin('mathieu');
        $mathieu->setPassword($this->passwordHasher->hashPassword($mathieu, 'ueihtam'));
        $mathieu->setNom('Chartier');
        $mathieu->setPrenom('Mathieu');
        $mathieu->setDateNaissance(new \DateTime('1996-03-15'));
        $mathieu->setIsAdmin(false);
        $mathieu->setIsSuperAdmin(false);
        $mathieu->setPays(null);
        $mathieu->setRoles([]);
        $manager->persist($mathieu);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PaysFixtures::class,
        ];
    }
}