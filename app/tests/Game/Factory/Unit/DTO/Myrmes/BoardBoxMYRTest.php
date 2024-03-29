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
    private PheromonTileMYR $pheromoneTileMYR;
    private GardenWorkerMYR $gardenWorkerMYR;
    private AnthillHoleMYR $anthillHoleMYR;
    private PreyMYR $preyMYR;
    private BoardBoxMYR $boardBoxMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertSame($this->tileMYR, $this->boardBoxMYR->getTile());
        $this->assertSame($this->anthillHoleMYR, $this->boardBoxMYR->getAnthillHole());
        $this->assertSame($this->preyMYR, $this->boardBoxMYR->getPrey());
        $this->assertSame($this->anthillHoleMYR, $this->boardBoxMYR->getAnthillHole());
        $this->assertSame($this->pheromoneTileMYR, $this->boardBoxMYR->getPheromonTile());
        $this->assertSame($this->gardenWorkerMYR, $this->boardBoxMYR->getAnt());
        $this->assertSame(0, $this->boardBoxMYR->getCoordX());
        $this->assertSame(0, $this->boardBoxMYR->getCoordY());
        $this->assertTrue($this->boardBoxMYR->hasTile());
        $this->assertFalse($this->boardBoxMYR->isEmptyBox());
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tileMYR = new TileMYR();
        $this->gardenWorkerMYR = new GardenWorkerMYR();
        $this->pheromoneTileMYR = new PheromonTileMYR();
        $this->anthillHoleMYR = new AnthillHoleMYR();
        $this->preyMYR = new PreyMYR();
        $this->boardBoxMYR = new BoardBoxMYR(
            $this->tileMYR,
            $this->gardenWorkerMYR,
            $this->pheromoneTileMYR,
            $this->anthillHoleMYR,
            $this->preyMYR,
            0,
            0
        );
    }
}