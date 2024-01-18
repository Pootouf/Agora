<?php

namespace App\Tests\Unit\Service\Game;

use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\GameService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameServiceTest extends KernelTestCase
{

    public function testGameSixQPCreationValidWithValidNumberOfPlayers(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());

        $gameService = static::getContainer()->get(GameService::class);
        $gameRepository = static::getContainer()->get(GameSixQPRepository::class);

        $players = ['test', 'test', 'test', 'test'];

        $game = $gameService->createSixQPGame($players);

        $game = $gameRepository->findOneBy(['id' => $game->getId()]);

        $this->assertSame(count($players), count($game->getPlayerSixQPs()));
    }
}
