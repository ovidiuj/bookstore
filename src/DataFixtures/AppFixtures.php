<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setRoles([User::ROLE_ADMIN]);
        $user->setEmail('admin@bookstore.test');
        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'bookstore'));

        $manager->persist($user);
        $manager->flush();
    }
}
