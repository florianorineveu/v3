<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new Admin()
            ->setEmail('admin@test.tld')
            ->setFirstName('Admin')
            ->setLastName('User')
            ->setActive(true)
            ->setRoles(['ROLE_ADMIN'])
        ;

        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'admin'));

        $manager->persist($user);
        $manager->flush();
    }
}
