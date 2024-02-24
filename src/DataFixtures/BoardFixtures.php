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
    public function __construct(GameManagerService $gameManagerService, BoardManagerService $boardManagerService)
    {
        $this->boardManagerService = $boardManagerService;
    }
    public function load(ObjectManager $manager){

        //Getting all users and games created from fixtures
        $users = $manager->getRepository(User::class)->findAll();
        $games = $manager->getRepository(Game::class)->findAll();

        for ($i=1; $i <= 50; $i++) {
            $board = new Board();

            //Setting the game with a fake party
            $board->setGame($games[rand(0,count($games) -  1)]);
            $board->setPartyId($i);

            //The number of player required to launch the game
            $nbPlayersMax = rand(6, 10);
            $board->setNbUserMax($nbPlayersMax);
            //The number of players who have already joined the table
            $nbJoinedPlayers = rand(1, $nbPlayersMax);
            shuffle($users);
            for($j=0; $j < $nbJoinedPlayers; $j++)
            {
                $user = $users[$j];
                $user->addBoard($board);
            }
            $board->setInactivityTimer(new \DateTime());
            //The number of invited people
            $board->setNbInvitations(rand(0,$nbPlayersMax - $nbJoinedPlayers));
            $board->setInactivityHours(rand(6,48));
            $board->setCreationDate(new \DateTime());
            $board->setInvitationTimer(new \DateTime());
            $manager->persist($board);
            $manager->flush();
        }

        $sqpGame = $manager->getRepository(Game::class)->findBy(array("label" => "6QP"))[0];
        //Board test for launching 6QP
        $boardSqp = new Board();
        $boardSqp->setGame($sqpGame);
        $this->boardManagerService->setUpBoard($boardSqp, $sqpGame);
        $boardSqp->setNbInvitations(0);
        $boardSqp->setNbUserMax(3);
        $boardSqp->setInactivityHours(24);
        $manager->persist($boardSqp);
        $manager->flush();
        $this->boardManagerService->addUserToBoard($boardSqp, $users[0]);
        $this->boardManagerService->addUserToBoard($boardSqp, $users[1]);



    }

    //Board depends on Users and Game
    public function getDependencies()
    {
        return[
            UserPlatformFixtures::class,
            GameFixtures::class
        ];
    }
}