<?php

namespace App\DataFixtures;

use App\Entity\Platform\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        for ($i=1; $i <= 50; $i++) {
            $game = new Game();
            $game->setName("gameN°". $i);
            $game->setDescrRule("Les règles du jeu blablabla");
            $game->setLabel("G".$i);
            $game->setMinPlayers(rand(2,4));
            $game->setMaxPlayers(rand(5,10));
            $game->setIsActive(true);
            $game->setImgURL("NoImgNow");
            $manager->persist($game);
        }

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