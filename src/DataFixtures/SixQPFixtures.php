<?php

namespace App\DataFixtures;

use App\Entity\Game\SixQP\CardSixQP;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SixQPFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 104; $i++) {
            $card = new CardSixQP();
            $card->setValue($i);
            $card->setPoints(1);
            if ($i % 5 == 0) {
                $card->setPoints(2);
            }
            if ($i % 10 == 0) {
                $card->setPoints(3);
            }
            if ($i % 11 == 0) {
                $card->setPoints(5);
            }
            if ($i % 55 == 0) {
                $card->setPoints(7);
            }
            $manager->persist($card);
        }
        $manager->flush();
    }
}
