<?php

namespace App\DataFixtures;

use App\Entity\Game\GameUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       for ($i = 0; $i <= 11; ++$i) {
           $user = new GameUser();
           $username = "test" . $i;
           $user->setUsername($username);
           $user->setPassword("test");
           $user->setRoles([]);
           $manager->persist($user);
       }

       $manager->flush();
    }
}
