<?php

namespace Integration\Service\Game;

use App\Entity\Game\GameUser;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\GameManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameManagerServiceIntegrationTest extends KernelTestCase
{
    public function testDeleteGameWhenGameIsInvalid() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);

        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_GAME, $gameService->deleteGame(-1));
    }

    public function testDeleteGameWhenGameIsValid() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $invalidGame = new GameSixQP();
        $entityManager->persist($invalidGame);
        $entityManager->flush();
        $this->assertEquals(AbstractGameManagerService::$SUCCESS, $gameService->deleteGame($invalidGame->getId()));
    }

    public function testLaunchGame6QPFailWhenNotEnoughPlayers() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $invalidGame = new GameSixQP();
        $entityManager->persist($invalidGame);
        $entityManager->flush();
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER, $gameService->launchGame($invalidGame->getId()));
    }

    public function testLaunchGame6QPFailWhenGameIsInvalid() : void
    {
        $gameService = static::getContainer()->get(GameManagerService::class);
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_GAME, $gameService->launchGame(-1));
    }

    public function testLaunchGame6QPFailWhenGameIsValid() : void
    {
        $user1 = new GameUser();
        $user1->setUsername("test1");
        $user2 = new GameUser();
        $user2->setUsername("test2");
        $user3 = new GameUser();
        $user3->setUsername("test3");
        $user4 = new GameUser();
        $user4->setUsername("test4");

        $gameService = static::getContainer()->get(GameManagerService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $validGame = $this->createSixQPGame(4, 4);
        $entityManager->persist($validGame);
        $entityManager->flush();
        $gameId = $validGame->getId();
        $gameService->joinGame($gameId, $user1);
        $gameService->joinGame($gameId, $user2);
        $gameService->joinGame($gameId, $user3);
        $gameService->joinGame($gameId, $user4);

        $this->assertEquals(AbstractGameManagerService::$SUCCESS, $gameService->launchGame($gameId));
    }

    public function testQuitGameWhenGameIsNull()
    {
        // GIVEN
        $gameService = static::getContainer()->get(GameManagerService::class);
        $gameId = -1;
        $user = new GameUser();
        // WHEN
        $result = $gameService->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(-2, $result);
    }

    public function testQuitGameWhenGameIsLaunched()
    {
        // GIVEN
        $gameService = static::getContainer()->get(GameManagerService::class);
        $game = $this->createSixQPGame(3, 4);
        $gameId = $game->getId();
        $gameService->launchGame($gameId);
        $user = new GameUser();
        // WHEN
        $result = $gameService->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(-3, $result);
    }

    public function testQuitGameWhenPlayerIsInvalid()
    {
        // GIVEN
        $gameService = static::getContainer()->get(GameManagerService::class);
        $game = $this->createSixQPGame(3, 4);
        $gameId = $game->getId();
        $user = new GameUser();
        $user->setUsername("vrgyuvgryugerzygvzyegfyzefgbyezgfy");
        // WHEN
        $result = $gameService->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_PLAYER_NOT_FOUND, $result);
    }
    public function testQuitGameOnSuccess()
    {
        // GIVEN
        $gameService = static::getContainer()->get(GameManagerService::class);
        $game = $this->createSixQPGame(3, 4);
        $gameId = $game->getId();
        $userName = $game->getPlayerSixQPs()[0]->getUsername();
        $user = new GameUser();
        $user->setUsername($userName);
        // WHEN
        $result = $gameService->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$SUCCESS, $result);
    }

    private function createSixQPGame(int $numberOfPlayer, int $numberOfRow): GameSixQP
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = new GameSixQP();
        $game->setGameName(AbstractGameManagerService::$SIXQP_LABEL);
        for ($i = 0; $i < $numberOfPlayer; $i++) {
            $player = new PlayerSixQP('test', $game);
            $game->addPlayerSixQP($player);
            $entityManager->persist($player);
        }
        for ($i = 0; $i < $numberOfRow; $i++) {
            $row = new RowSixQP();
            $row->setPosition($i);
            $game->addRowSixQP($row);
            $entityManager->persist($row);
        }
        $entityManager->persist($game);
        $entityManager->flush();
        return $game;
    }
}
