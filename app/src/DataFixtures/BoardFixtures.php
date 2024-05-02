<?php

namespace App\DataFixtures;

use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
use App\Entity\Platform\User;
use App\Service\Game\GameManagerService;
use App\Service\Platform\BoardManagerService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BoardFixtures extends Fixture implements DependentFixtureInterface
{
    private BoardManagerService $boardManagerService;
    public function __construct(BoardManagerService $boardManagerService)
    {
        $this->boardManagerService = $boardManagerService;
    }
    public function load(ObjectManager $manager):void
    {

        //Getting all users and games created from fixtures
        $users = $manager->getRepository(User::class)->findAll();
        $games = $manager->getRepository(Game::class)->findAll();

        for ($i=1; $i <= 50; $i++) {
            //Fake data from the form
            $game = $games[rand(0,count($games) -  1)];
            $nbPlayersMax = rand($game->getMinPlayers(), $game->getMaxPlayers());
            $nbJoinedPlayers = rand(1, $nbPlayersMax - 1);
            $nbInvitations = (0);

            $board = new Board();
            //setting the data from the board creation form :
                $board->setGame($game);
                $board->setNbUserMax($nbPlayersMax);
                $board->setInactivityHours(rand(24,72));

            //Setting up the board :
            $this->boardManagerService->setUpBoard($board, $game);


            //Adding users to the board
            shuffle($users);
            for($j=0; $j < $nbJoinedPlayers; $j++)
            {
                $user = $users[$j];
                $this->boardManagerService->addUserToBoard($board, $user);
            }

            for($j=$nbJoinedPlayers; $j < $nbJoinedPlayers + $nbInvitations; $j++)
            {
                $user = $users[$j];
                $board->addInvitedContact($user);
            }

            $manager->persist($board);
            $manager->flush();
        }

    }

    //Board depends on Users and Game
    public function getDependencies():array
    {
        return[
            UserPlatformFixtures::class,
        ];
    }
}