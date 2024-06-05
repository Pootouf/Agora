<?php

namespace App\Tests\Game\Factory\Integration\Service\Game;

use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\RowSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Entity\Platform\User;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\GameManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SplendorGameManagerServiceIntegrationTest extends KernelTestCase
{

    public function testCreateValidGame() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);

        $labelGames = array(AbstractGameManagerService::SPL_LABEL);

        foreach ($labelGames as $labelGame)
        {
            $game = $gameService->createGame($labelGame);
            $this->assertTrue($game > 0);
        }
    }

    public function testJoinGameWhenGameIsNull() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $gameId = -1;
        $result = $gameService->joinGame($gameId, new User());
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testJoinGameWhenGameIsLaunched() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $game = $this->createGame(3, 4);
        $user = new User();
        $user->setUsername("toto");
        $game->setLaunched(true);
        $result = $gameService->joinGame($game->getId(), $user);
        $this->assertEquals(AbstractGameManagerService::ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testJoinGameWhenGameIsFull() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $game = $this->createGame(10, 4);
        $user = new User();
        $user->setUsername("toto");
        $result = $gameService->joinGame($game->getId(), $user);
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_NUMBER_OF_PLAYER, $result);
    }

    public function testJoinGameWhenGameIsInvalid() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $user = new User();
        $user->setUsername("toto");
        $result = $gameService->joinGame(-1, $user);
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testJoinGameSuccessfully() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $game = $this->createGame(3, 4);
        $user = new User();
        $user->setUsername("toto");
        $result = $gameService->joinGame($game->getId(), $user);
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $result);
    }
    public function testQuitGameWhenGameIsNull()
    {
        // GIVEN
        $gameService = static::getContainer()->get(GameManagerService::class);
        $gameId = -1;
        $user = new User();
        // WHEN
        $result = $gameService->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testQuitGameWhenGameIsLaunched()
    {
        // GIVEN
        $gameService = static::getContainer()->get(GameManagerService::class);
        $game = $this->createGame(3, 4);
        $gameId = $game->getId();
        $gameService->launchGame($gameId);
        $user = new User();
        $user->setUsername("a");
        // WHEN
        $result = $gameService->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testQuitGameWhenPlayerIsInvalid()
    {
        // GIVEN
        $gameService = static::getContainer()->get(GameManagerService::class);
        $game = $this->createGame(3, 4);
        $gameId = $game->getId();
        $user = new User();
        $user->setUsername("vrgyuvgryugerzygvzyegfyzefgbyezgfy");
        // WHEN
        $result = $gameService->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_PLAYER_NOT_FOUND, $result);
    }
    public function testQuitGameOnSuccess()
    {
        // GIVEN
        $gameService = static::getContainer()->get(GameManagerService::class);
        $game = $this->createGame(2);
        $gameId = $game->getId();
        $userName = $game->getPlayers()[0]->getUsername();
        $user = new User();
        $user->setUsername($userName);
        // WHEN
        $result = $gameService->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $result);
    }

    public function testDeleteGameWhenGameIsInvalid() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);

        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $gameService->deleteGame(-1));
    }

    public function testDeleteGameWhenGameIsValid() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $invalidGame = new GameSPL();
        $invalidGame->setGameName("SPL");
        $invalidGame->setMainBoard(new MainBoardSPL());
        $entityManager->persist($invalidGame);
        $entityManager->flush();
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $gameService->deleteGame($invalidGame->getId()));
    }

    public function testLaunchGameSPLFailWhenNotEnoughPlayers() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $invalidGame = new GameSPL();
        $invalidGame->setGameName("SPL");
        $invalidGame->setMainBoard(new MainBoardSPL());
        $entityManager->persist($invalidGame);
        $entityManager->flush();
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_NUMBER_OF_PLAYER, $gameService->launchGame($invalidGame->getId()));
    }

    public function testLaunchGameSPLFailWhenTooManyPlayers() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $invalidGame = $this->createGame(6);
        $entityManager->persist($invalidGame);
        $entityManager->flush();
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_NUMBER_OF_PLAYER, $gameService->launchGame($invalidGame->getId()));
    }
    public function testLaunchGameSPLFailWhenGameIsInvalid() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $gameService->launchGame(-1));
    }

    public function testLaunchGameSPLSuccessWhenGameIsValid() : void
    {
        $user1 = new User();
        $user1->setUsername("test1");
        $user2 = new User();
        $user2->setUsername("test2");
        $user3 = new User();
        $user3->setUsername("test3");
        $user4 = new User();
        $user4->setUsername("test4");

        $gameService = static::getContainer()->get(GameManagerService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $validGame = $this->createGame(4, 4);
        $entityManager->persist($validGame);
        $entityManager->flush();
        $gameId = $validGame->getId();
        $gameService->joinGame($gameId, $user1);
        $gameService->joinGame($gameId, $user2);
        $gameService->joinGame($gameId, $user3);
        $gameService->joinGame($gameId, $user4);

        $this->assertEquals(AbstractGameManagerService::SUCCESS, $gameService->launchGame($gameId));
    }

    private function createGame($numberOfPlayer): GameSPL
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = new GameSPL();
        $game->setGameName(AbstractGameManagerService::SPL_LABEL);
        $mainBoard = new MainBoardSPL();
        $game->setMainBoard($mainBoard);
        for ($i = 1; $i <= SplendorParameters::NUMBER_OF_ROWS_BY_GAME; $i++) {
            $row = new RowSPL();
            $row->setLevel($i);
            $mainBoard->addRowsSPL($row);
            $entityManager->persist($row);
            $drawCard = new DrawCardsSPL();
            $drawCard->setLevel($i);
            $mainBoard->addDrawCard($drawCard);
            $entityManager->persist($drawCard);
        }
        $entityManager->persist($mainBoard);
        $entityManager->persist($game);
        for ($i = 0; $i < $numberOfPlayer; $i++) {
            $player = new PlayerSPL('test', $game);
            $personalBoard = new PersonalBoardSPL();
            $personalBoard->setPlayerSPL($player);
            $game->addPlayer($player);
            $entityManager->persist($player);
            $entityManager->persist($personalBoard);
            $entityManager->flush();
        }
        $entityManager->flush();
        return $game;
    }

}
