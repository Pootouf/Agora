<?php

namespace App\DataFixtures;

use App\Entity\Platform\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i <= 11; ++$i) {
            $user = new User();
            $username = "test" . $i;
            $user->setUsername($username);
            $user->setPassword("test");
            $user->setEmail("test".$i."@test.com");
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
