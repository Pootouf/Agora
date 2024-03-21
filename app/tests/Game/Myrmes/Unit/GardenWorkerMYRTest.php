<?php

namespace App\Tests\Game\Myrmes\Unit;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\TileMYR;
use PHPUnit\Framework\TestCase;

class GardenWorkerMYRTest extends TestCase
{
    private GardenWorkerMYR $gardenWorkerMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->gardenWorkerMYR->getId() >= 0);
    }

    public function testSetPlayer() : void
    {
        // GIVEN

        $player = new PlayerMYR("user", new GameMYR());

        // WHEN

        $this->gardenWorkerMYR->setPlayer($player);

        // THEN

        $this->assertSame($player, $this->gardenWorkerMYR->getPlayer());
    }

    public function testSetTile() : void
    {
        // GIVEN

        $tile = new TileMYR();

        // WHEN

        $this->gardenWorkerMYR->setTile($tile);

        // THEN

        $this->assertSame($tile, $this->gardenWorkerMYR->getTile());
    }

    public function testSetShiftsCount() : void
    {
        // GIVEN

        $shiftsCount = 7;

        // WHEN

        $this->gardenWorkerMYR->setShiftsCount($shiftsCount);

        // THEN

        $this->assertSame($shiftsCount, $this->gardenWorkerMYR->getShiftsCount());
    }

    public function testSetMainBoard() : void
    {
        // GIVEN

        $mainBoard = new MainBoardMYR();

        // WHEN

        $this->gardenWorkerMYR->setMainBoardMYR($mainBoard);

        // THEN

        $this->assertSame($mainBoard, $this->gardenWorkerMYR->getMainBoardMYR());
    }

    protected function setUp(): void
    {
        $this->gardenWorkerMYR = new GardenWorkerMYR();
    }
}