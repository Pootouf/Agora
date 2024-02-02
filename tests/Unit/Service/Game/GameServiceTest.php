<?php

namespace Unit\Service\Game;

use App\Entity\Game\GameUser;
use App\Entity\Game\SixQP\GameSixQP;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\GameService;
use App\Service\Game\SixQPService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{

    private GameService $gameService;

    protected function setUp(): void
    {
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $sixQPService = $this->createMock(SixQPService::class);
        $this->gameService = new GameService($gameSixQPRepository, $sixQPService);
    }

    public function testDeleteGame() : void
    {
        $this->setUp();
        $game = new GameSixQP();
        $id = $game->getId();
        $this->assertTrue($this->gameService->deleteGame($id));
    }

    public function testQuitGame() : void
    {
        $this->setUp();
        $game = new GameSixQP();
        $gameId = $game->getId();
        $user = new GameUser();
        $userId = $user->getId();
        $this->assertTrue($this->gameService->quitGame($gameId, $userId));
    }
}
