<?php

namespace App\DataFixtures;

use App\Entity\Platform\Board;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BoardFixtures extends Fixture
{

    public function load(ObjectManager $manager){
        $board = new Board(rand(1,5));
        $board->setNbUserMax(rand(5,10));
        $board->setInactivityTimer(new \DateTimeImmutable());
    }
}