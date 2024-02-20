<?php

namespace App\DataFixtures;


use App\Entity\Platform\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class dUserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

// ...
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i <= 50; $i++) {
            $user = new User();
            $user->setUsername('user'.$i);
            $user->setEmail("user".$i."@"."univ-rouen."."fr");
            $user->setIsVerified(true);
            $password = $this->hasher->hashPassword($user, 'agora');
            $user->setPassword($password);
            $manager->persist($user);
        }
        $manager->flush();
    }
}