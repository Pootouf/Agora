<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\TileGLM;
use PHPUnit\Framework\TestCase;

class BoardTileGLMTest extends TestCase
{
    private BoardTileGLM $boardTileGLM;

    public function testInit() : void
    {

        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->boardTileGLM->getId() >= 0);
    }

    public function testSetPosition() : void
    {
        // GIVEN

        $position = 5;

        // WHEN

        $this->boardTileGLM->setPosition($position);

        // THEN

        $this->assertSame($position, $this->boardTileGLM->getPosition());
    }

    public function testSetTile() : void
    {
        // GIVEN

        $tile = new TileGLM();

        // WHEN

        $this->boardTileGLM->setTile($tile);

        // THEN

        $this->assertSame($tile, $this->boardTileGLM->getTile());
    }

    public function testSetMainBoard() : void
    {
        // GIVEN

        $mainBoard = new MainBoardGLM();

        // GIVEN

        $this->boardTileGLM->setMainBoardGLM($mainBoard);

        // THEN

        $this->assertSame($mainBoard, $this->boardTileGLM->getMainBoardGLM());
    }

    protected function setUp() : void
    {
        $this->boardTileGLM = new BoardTileGLM();
    }
}