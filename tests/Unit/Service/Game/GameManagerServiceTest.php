<?php

namespace Unit\Service\Game;

use App\Entity\Game\GameUser;
use App\Entity\Game\SixQP\GameSixQP;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\GameManagerService;
use App\Service\Game\SixQP\SixQPGameManagerService;
use PHPUnit\Framework\TestCase;

class GameManagerServiceTest extends TestCase
{
    private GameManagerService $gameService;

    protected function setUp(): void
    {
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $this->gameService = new GameManagerService($gameSixQPRepository, $sixQPService);
    }
}
