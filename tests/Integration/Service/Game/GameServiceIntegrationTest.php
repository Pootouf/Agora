<?php

namespace App\Tests\Integration\Service\Game;

use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\GameService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameServiceIntegrationTest extends KernelTestCase
{

    public function testCreateSixQPGameValidWithValidPlayer(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());

        $gameService = static::getContainer()->get(GameService::class);

        $players = ['test', 'test', 'test', 'test']; //TODO : add real users
        $game = $gameService->createSixQPGame($players);

        $playerRepository = static::getContainer()->get(PlayerSixQPRepository::class);
        $this->assertInstanceOf(PlayerSixQPRepository::class, $playerRepository);

        $sixQPPlayers = $playerRepository->findAll();
        $this->assertSame(count($sixQPPlayers), count($players));

        $rows = $game->getRowSixQPs();
        $this->assertSame(count($rows), 4);

        $i = 0;
        foreach ($rows as $row) {
            $this->assertSame(count($row->getCards()), 0);
            $this->assertSame($row->getPosition(), $i++);
        }
    }
}
