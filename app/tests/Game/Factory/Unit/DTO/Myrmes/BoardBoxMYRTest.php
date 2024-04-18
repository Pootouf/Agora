<?php

namespace App\Tests\Game\Factory\Unit\DTO\Myrmes;

use App\Entity\Game\DTO\Myrmes\BoardBoxMYR;
use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\TileMYR;
use Exception;
use PHPUnit\Framework\TestCase;

class BoardBoxMYRTest extends TestCase
{

    private TileMYR $tileMYR;
    private GardenWorkerMYR $gardenWorkerMYR;
    private BoardBoxMYR $boardBoxMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertSame($this->tileMYR, $this->boardBoxMYR->getTile());
        $this->assertNull($this->boardBoxMYR->getAnthillHole());
        $this->assertNull($this->boardBoxMYR->getPrey());
        $this->assertNull($this->boardBoxMYR->getAnthillHole());
        $this->assertNull($this->boardBoxMYR->getPheromonTile());
        $this->assertSame($this->gardenWorkerMYR, $this->boardBoxMYR->getAnt());
        $this->assertSame(0, $this->boardBoxMYR->getCoordX());
        $this->assertSame(0, $this->boardBoxMYR->getCoordY());
        $this->assertTrue($this->boardBoxMYR->hasTile());
        $this->assertFalse($this->boardBoxMYR->isEmptyBox());
    }

    public function testCreateBoardBoxWithErrorWithTileIsNull() : void
    {
        // THEN

        $this->expectException(Exception::class);

        // WHEN

        new BoardBoxMYR(null, $this->gardenWorkerMYR,
            new PheromonTileMYR(), null,
            null, 0 ,0, 0);
    }

    public function testCreateBoardBoxWithErrorWithAntIsNull() : void
    {
        // GIVEN

        $movementsPoints = 6;

        // WHEN

        $boardBox = new BoardBoxMYR(null, null,
            null, null,
            null, 0 ,0, $movementsPoints);

        // THEN

        $this->assertSame($movementsPoints, $boardBox->getMovementPoints());
    }

    public function testSetMovementsPoints() : void
    {
        // GIVEN

        $movementsPoints = 5;

        // WHEN

        $this->boardBoxMYR->setMovementPoints($movementsPoints);

        // THEN

        $this->assertSame($movementsPoints, $this->boardBoxMYR->getMovementPoints());
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tileMYR = new TileMYR();
        $this->gardenWorkerMYR = new GardenWorkerMYR();
        $this->gardenWorkerMYR->setShiftsCount(4);
        $this->boardBoxMYR = new BoardBoxMYR(
            $this->tileMYR,
            $this->gardenWorkerMYR,
            null,
            null,
            null,
            0,
            0,
            0
        );
    }
}