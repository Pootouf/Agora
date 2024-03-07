<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\TileGLM;
use PHPUnit\Framework\TestCase;

class DrawTileGLMTest extends TestCase
{
    private DrawTilesGLM $drawTilesGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->drawTilesGLM->getId() >= 0);
        $this->assertNotNull($this->drawTilesGLM->getTiles());
    }

    public function testSetLevel() : void
    {
        // GIVEN

        $level = 2;

        // WHEN

        $this->drawTilesGLM->setLevel($level);

        // THEN

        $this->assertSame($level, $this->drawTilesGLM->getLevel());
    }

    public function testAddTileNotYetAdded() : void
    {
        // GIVEN

        $tile = new TileGLM();

        // WHEN
        $this->drawTilesGLM->addTile($tile);

        // THEN

        $this->assertContains($tile, $this->drawTilesGLM->getTiles());
    }

    public function testAddTileAlreadyAdded() : void
    {
        // GIVEN

        $tile = new TileGLM();
        $this->drawTilesGLM->addTile($tile);
        $length = $this->drawTilesGLM->getTiles()->count();

        // WHEN

        $this->drawTilesGLM->addTile($tile);

        // THEN

        $this->assertSame($length, $this->drawTilesGLM->getTiles()->count());
    }

    public function testRemoveTile() : void
    {
        // GIVEN

        $tile = new TileGLM();
        $this->drawTilesGLM->addTile($tile);

        // WHEN

        $this->drawTilesGLM->removeTile($tile);

        // THEN

        $this->assertNotContains($tile, $this->drawTilesGLM->getTiles());
    }

    public function testSetMainBoard() : void
    {
        // GIVEN

        $mainBoard = new MainBoardGLM();

        // WHEN

        $this->drawTilesGLM->setMainBoardGLM($mainBoard);

        // THEN

        $this->assertEquals($mainBoard, $this->drawTilesGLM->getMainBoardGLM());
    }

    protected function setUp() : void {
        $this->drawTilesGLM = new DrawTilesGLM();
    }
}