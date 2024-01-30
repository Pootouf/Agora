<?php

namespace App\Tests\Integration\Service\Game;

use App\Entity\Game\DTO\Player;
use App\Entity\Game\GameUser;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\GameService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameServiceIntegrationTest extends KernelTestCase
{

    public function testDeleteGameWhenGameIsInvalid() : void
    {
        $gameService = static::getContainer()->get(GameService::class);
        
        $this->assertEquals(-6, $gameService->deleteGame(-1));
    }

    public function testDeleteGameWhenGameIsValid() : void
    {
        $gameService = static::getContainer()->get(GameService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $invalidGame = new GameSixQP();
        $entityManager->persist($invalidGame);
        $entityManager->flush();
        $this->assertEquals(1, $gameService->deleteGame($invalidGame->getId()));
    }

    public function testLaunchGame6QPFailWhenNotEnoughPlayers() : void
    {
        $gameService = static::getContainer()->get(GameService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $invalidGame = new GameSixQP();
        $entityManager->persist($invalidGame);
        $entityManager->flush();
        $this->assertEquals(-4, $gameService->launchGame($invalidGame->getId()));
    }

    public function testLaunchGame6QPFailWhenGameIsInvalid() : void
    {
        $gameService = static::getContainer()->get(GameService::class);
        $this->assertEquals(-6, $gameService->launchGame(-1));
    }

    public function testLaunchGame6QPFailWhenGameIsValid() : void
    {
        $gameService = static::getContainer()->get(GameService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $validGame = new GameSixQP();
        $entityManager->persist($validGame);
        $entityManager->flush();
        $gameId = $validGame->getId();
        $gameService->joinGame($gameId, new GameUser());
        $gameService->joinGame($gameId, new GameUser());
        $gameService->joinGame($gameId, new GameUser());
        $gameService->joinGame($gameId, new GameUser());
        
        $this->assertEquals(1, $gameService->launchGame($gameId));
    }
}
