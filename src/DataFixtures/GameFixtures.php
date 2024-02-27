<?php

namespace App\DataFixtures;

use App\Entity\Platform\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        //Six Qui Prend fixture

        $sqp = new Game();
        $sqp->setName("Six Qui Prends");
        $sqp->setLabel("6QP");
        $sqp->setDescrRule("C'est le 6 qui prends");
        $sqp->setMinPlayers(2);
        $sqp->setMaxPlayers(10);
        $sqp->setIsActive(true);
        $sqp->setImgURL("NoImgNow");
        $manager->persist($sqp);
        $manager->flush();
    }

}