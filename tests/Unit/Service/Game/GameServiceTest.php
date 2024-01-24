<?php

namespace App\Tests\Unit\Service\Game;

use App\Service\Game\GameService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{

    private GameService $gameService;

    public function testUserCreationValidWithValidNumberOfPlayers(): void
    {
        $players = ['test', 'test', 'test', 'test']; //TODO : add real users
        $game = $this->gameService->createSixQPGame($players);
        $this->expectNotToPerformAssertions();
    }

    public function testUserCreationInvalidWithTooManyPlayers(): void
    {
        $players = ['test', 'test', 'test', 'test', 'test', 'test', 'test'
                    , 'test', 'test', 'test', 'test', 'test', 'test']; //TODO : add real users
        $this->expectException(Exception::class);
        $game = $this->gameService->createSixQPGame($players);
    }

    public function testUserCreationInvalidWithNotEnoughPlayers(): void
    {
        $players = ['test']; //TODO : add real users
        $this->expectException(Exception::class);
        $game = $this->gameService->createSixQPGame($players);
    }

    public function testGameSixQPCreationGameValidWithValidNumberOfPlayers(): void
    {
        $players = ['test', 'test', 'test', 'test']; //TODO : add real users
        $game = $this->gameService->createSixQPGame($players);

        $rows = $game->getRowSixQPs();
        $this->assertSame(count($rows), 4);

        $i = 0;
        foreach ($rows as $row) {
            $this->assertSame(count($row->getCards()), 0);
            $this->assertSame($row->getPosition(), $i++);
        }
    }

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->gameService = new GameService($entityManager);
    }
}
