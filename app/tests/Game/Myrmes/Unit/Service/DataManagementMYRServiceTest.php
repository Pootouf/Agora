<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

use App\Entity\Game\DTO\Myrmes\BoardBoxMYR;
use App\Entity\Game\DTO\Myrmes\BoardTileMYR;
use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\GardenWorkerMYRRepository;
use App\Repository\Game\Myrmes\PheromonTileMYRRepository;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use App\Service\Game\Myrmes\DataManagementMYRService;

class DataManagementMYRServiceTest extends TestCase
{
    private DataManagementMYRService $dataManagementMYRService;

    protected function setUp(): void
    {
        $tileMYRRepository = $this->createMock(TileMYRRepository::class);
        $antHole = $this->createMock(AnthillHoleMYRRepository::class);
        $preyRepo = $this->createMock(PreyMYRRepository::class);
        $gardenWorkerRepo = $this->createMock(GardenWorkerMYRRepository::class);
        $pheromoneTileRepo = $this->createMock(PheromonTileMYRRepository::class);
        $this->dataManagementMYRService = new DataManagementMYRService($tileMYRRepository, $antHole,
            $preyRepo, $gardenWorkerRepo, $pheromoneTileRepo);
    }

    public function testOrganizeMainBoardRowsWhenNotWorkerPhase() : void
    {
        //GIVEN
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $tiles = [];
        for ($i = 0; $i < 2; ++$i) {
            for ($j = 0 ; $j < 4; $j += 2) {
                $tile = new TileMYR();
                $tile->setCoordX($i);
                $tile->setCoordY($j);
                $mainBoard->addTile($tile);
                $boardBox = new BoardBoxMYR($tile, null, null, null, null, $i, $j);
                $tiles[$i][$j] = $boardBox;
            }
        }
        $expectedResult = new ArrayCollection();
        $expectedLine = new ArrayCollection();
        $expectedLine->add($tiles[0][0]);
        $expectedLine->add($tiles[0][2]);
        $expectedResult->add($expectedLine);
        $expectedLine = new ArrayCollection();
        $expectedLine->add($tiles[1][0]);
        $expectedLine->add($tiles[1][2]);
        $expectedResult->add($expectedLine);
        //WHEN
        $result = $this->dataManagementMYRService->organizeMainBoardRows($game, false);

        //THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testOrganizeMainBoardRowsWhenWorkerPhase() : void
    {
        //GIVEN
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $tiles = [];
        for ($i = 0; $i < 2; ++$i) {
            for ($j = 0 ; $j <= 6; $j += 2) {
                if ($j == 2 || $j == 4) {
                    continue;
                }
                $tile = new TileMYR();
                $tile->setCoordX($i);
                $tile->setCoordY($j);
                $mainBoard->addTile($tile);
                $boardBox = new BoardBoxMYR($tile, null, null, null, null, $i, $j);
                $tiles[$i][$j] = $boardBox;
            }
        }
        $expectedResult = new ArrayCollection();
        $expectedLine = new ArrayCollection();
        $expectedLine->add($tiles[0][0]);
        $expectedLine->add(new BoardBoxMYR(null, null, null, null, null, 0, 2));
        $expectedLine->add(new BoardBoxMYR(null, null, null, null, null, 0, 4));
        $expectedLine->add($tiles[0][6]);
        $expectedResult->add($expectedLine);
        $expectedLine = new ArrayCollection();
        $expectedLine->add($tiles[1][0]);
        $expectedLine->add(new BoardBoxMYR(null, null, null, null, null, 1, 2));
        $expectedLine->add(new BoardBoxMYR(null, null, null, null, null, 1, 4));
        $expectedLine->add($tiles[1][6]);
        $expectedResult->add($expectedLine);
        //WHEN
        $result = $this->dataManagementMYRService->organizeMainBoardRows($game, true,
            null, null, new PlayerMYR("test", $game), 0);
        //THEN
        $this->assertEquals($expectedResult, $result);
    }
}