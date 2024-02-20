<?php

namespace App\DataFixtures;

use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
use App\Entity\Platform\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BoardFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager){

        //On récupère
        $users = $manager->getRepository(User::class)->findAll();

        for ($i=1; $i <= 50; $i++) {
            $board = new Board();
            //The number of player required to launch the game
            $nbPlayersMax = rand(6, 10);
            $board->setNbUserMax($nbPlayersMax);
            //The number of players who have already joined the table
            $nbJoinedPlayers = rand(1, $nbPlayersMax);
            shuffle($users);
            for($j=0; $j < $nbJoinedPlayers; $j++)
            {
                $users[$j]->addBoard($board);
            }
            $board->setInactivityTimer(new \DateTime());
            //The number of invited people
            $board->setNbInvitations(rand(0,$nbPlayersMax - $nbJoinedPlayers));
            $board->setInactivityHours(rand(6,48));
            $board->setCreationDate(new \DateTime());
            $board->setInvitationTimer(new \DateTime());
            $manager->persist($board);
        }
        $manager->flush();

    }

    //Board depends on Users and Game
    public function getDependencies()
    {
        return[UserFixtures::class];
    }
}