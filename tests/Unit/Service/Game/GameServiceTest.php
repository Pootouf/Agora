<?php

namespace App\Tests\Unit\Service\Game;

use App\Service\Game\GameService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{

    private GameService $gameService;

    public function testGameSixQPCreationValidWithValidNumberOfPlayers(): void
    {
        $players = ['test', 'test', 'test', 'test']; //TODO : add real users
        $game = $this->gameService->createSixQPGame($players);
        $this->expectNotToPerformAssertions();
    }

    public function testGameSixQPCreationInvalidWithInvalidNumberOfPlayers(): void
    {
        $players = ['test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test']; //TODO : add real users
        $this->expectException(Exception::class);
        $game = $this->gameService->createSixQPGame($players);
    }

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->gameService = new GameService($entityManager);
    }
}
