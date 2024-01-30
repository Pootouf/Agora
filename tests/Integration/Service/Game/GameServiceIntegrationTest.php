<?php

namespace App\Tests\Integration\Service\Game;

use App\Entity\Game\DTO\Player;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\GameService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameServiceIntegrationTest extends KernelTestCase
{

    

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
        $gameId = $validGame->getId();
        $entityManager->flush();
        $gameService->joinGame($gameId, new PlayerSixQP("player1", $validGame));
        $gameService->joinGame($gameId, new PlayerSixQP("player2", $validGame));
        $gameService->joinGame($gameId, new PlayerSixQP("player3", $validGame));
        $gameService->joinGame($gameId, new PlayerSixQP("player4", $validGame));
        
        $this->assertEquals(1, $gameService->launchGame($gameId));
    }
}
