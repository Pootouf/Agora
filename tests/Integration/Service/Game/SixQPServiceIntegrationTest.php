<?php

namespace Integration\Service\Game;

use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\SixQPService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SixQPServiceIntegrationTest extends KernelTestCase
{

    public function testInitializeNewRoundValidWithValidGame(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);

        $game = $this->createGame(6, 4);
        $sixQPService->initializeNewRound($game);

        $gameRepository = static::getContainer()->get(GameSixQPRepository::class);
        $newGame = $gameRepository->findOneBy(['id' => $game->getId()]);
        $players = $newGame->getPlayerSixQPs();
        $rows = $newGame->getRowSixQPs();

        foreach ($players as $player) {
            $this->assertNotNull($player->getCards());
            $this->assertSame(PlayerSixQP::$NUMBER_OF_CARDS_BY_PLAYER, count($player->getCards()));
            $this->assertSame(count($game->getPlayerSixQPs()), count($players));
        }
        foreach ($rows as $row) {
            $this->assertNotNull($row->getCards());
            $this->assertSame(1, count($row->getCards()));
            $this->assertSame(count($game->getRowSixQPs()), count($rows));
        }
    }

    public function testInitializeNewRoundInvalidWithNotEnoughPlayers(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $game = $this->createGame(1, 4);
        $this->expectException(Exception::class);
        $sixQPService->initializeNewRound($game);
    }

    public function testInitializeNewRoundInvalidWithTooManyPlayers(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $game = $this->createGame(1, 4);
        $this->expectException(Exception::class);
        $sixQPService->initializeNewRound($game);
    }

    public function testInitializeNewRoundInvalidWithInvalidNumberOfRows(): void
    {
        $sixQPService = static::getContainer()->get(SixQPService::class);
        $game = $this->createGame(4, 3);
        $this->expectException(Exception::class);
        $sixQPService->initializeNewRound($game);
    }



    private function createGame(int $numberOfPlayer, int $numberOfRow): GameSixQP
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = new GameSixQP();
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
