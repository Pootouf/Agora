<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\BuyingTileGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use PHPUnit\Framework\TestCase;

class BuyingTileGLMTest extends TestCase
{

    private BuyingTileGLM $buyingTileGLM;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->buyingTileGLM->getId() >= 0);
    }

    public function testSetBoardTile() : void
    {
        // GIVEN

        $boardTile = new BoardTileGLM();

        // WHEN

        $this->buyingTileGLM->setBoardTile($boardTile);

        // THEN

        $this->assertSame($boardTile, $this->buyingTileGLM->getBoardTile());
    }

    public function testSetPersonalBoardWhenIsNotNull() : void
    {
        // GIVEN

        $personalBoard = new PersonalBoardGLM();

        // WHEN

        $this->buyingTileGLM->setPersonalBoardGLM($personalBoard);

        // THEN

        $this->assertSame($personalBoard, $this->buyingTileGLM->getPersonalBoardGLM());
        $this->assertSame($this->buyingTileGLM, $personalBoard->getBuyingTile());
    }

    public function testSetPersonalBoardWhenIsNull() : void
    {
        // GIVEN

        $personalBoard = new PersonalBoardGLM();
        $this->buyingTileGLM->setPersonalBoardGLM($personalBoard);


        // WHEN

        $this->buyingTileGLM->setPersonalBoardGLM(null);

        // THEN

        $this->assertNull($this->buyingTileGLM->getPersonalBoardGLM());
        $this->assertNull($personalBoard->getBuyingTile());
    }

    public function testSetXCoordinate() : void
    {
        // GIVEN

        $x = 2;

        // WHEN

        $this->buyingTileGLM->setCoordX($x);

        // THEN

        $this->assertSame($x, $this->buyingTileGLM->getCoordX());
    }

    public function testSetYCoordinate() : void
    {
        // GIVEN

        $y = 2;

        // WHEN

        $this->buyingTileGLM->setCoordY($y);

        // THEN

        $this->assertSame($y, $this->buyingTileGLM->getCoordY());
    }

    protected function setUp(): void
    {
        $this->buyingTileGLM = new BuyingTileGLM();
    }

}