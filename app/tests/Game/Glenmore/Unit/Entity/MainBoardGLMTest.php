<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use PHPUnit\Framework\TestCase;

class MainBoardGLMTest extends TestCase
{
    private MainBoardGLM $mainBoardGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->mainBoardGLM->getId() >= 0);
        $this->assertNotNull($this->mainBoardGLM->getBoardTiles());
        $this->assertNotNull($this->mainBoardGLM->getPawns());
        $this->assertNotNull($this->mainBoardGLM->getDrawTiles());
    }

    public function testAddBoardTileNotYetAdded() : void
    {
        // GIVEN

        $boardTile = new BoardTileGLM();

        // WHEN

        $this->mainBoardGLM->addBoardTile($boardTile);

        // THEN

        $this->assertContains($boardTile, $this->mainBoardGLM->getBoardTiles());
        $this->assertSame($this->mainBoardGLM, $boardTile->getMainBoardGLM());
    }

    public function testAddBoardTileAlreadyAdded() : void
    {
        // GIVEN

        $boardTile = new BoardTileGLM();
        $this->mainBoardGLM->addBoardTile($boardTile);
        $length = $this->mainBoardGLM->getBoardTiles()->count();

        // WHEN

        $this->mainBoardGLM->addBoardTile($boardTile);

        // THEN

        $this->assertContains($boardTile, $this->mainBoardGLM->getBoardTiles());
        $this->assertSame($length, $this->mainBoardGLM->getBoardTiles()->count());
    }

    public function testRemoveBoardTileNotYetRemoved() : void
    {
        // GIVEN

        $boardTile = new BoardTileGLM();
        $this->mainBoardGLM->addBoardTile($boardTile);

        // WHEN

        $this->mainBoardGLM->removeBoardTile($boardTile);

        // THEN

        $this->assertNotContains($boardTile, $this->mainBoardGLM->getBoardTiles());
        $this->assertNull($boardTile->getMainBoardGLM());
    }

    public function testRemoveBoardTileAlreadyRemoved() : void
    {
        // GIVEN

        $boardTile = new BoardTileGLM();
        $this->mainBoardGLM->addBoardTile($boardTile);
        $this->mainBoardGLM->removeBoardTile($boardTile);
        $length = $this->mainBoardGLM->getBoardTiles()->count();

        // WHEN

        $this->mainBoardGLM->removeBoardTile($boardTile);

        // THEN

        $this->assertNotContains($boardTile, $this->mainBoardGLM->getBoardTiles());
        $this->assertSame($length, $this->mainBoardGLM->getBoardTiles()->count());
    }

    public function testAddDrawTileNotYetAdded() : void
    {
        // GIVEN

        $drawTile = new DrawTilesGLM();

        // WHEN

        $this->mainBoardGLM->addDrawTile($drawTile);

        // THEN

        $this->assertContains($drawTile, $this->mainBoardGLM->getDrawTiles());
        $this->assertSame($this->mainBoardGLM, $drawTile->getMainBoardGLM());
    }

    public function testAddDrawTileAlreadyAdded() : void
    {
        /// GIVEN

        $drawTile = new DrawTilesGLM();
        $this->mainBoardGLM->addDrawTile($drawTile);
        $length = $this->mainBoardGLM->getDrawTiles()->count();

        // WHEN

        $this->mainBoardGLM->addDrawTile($drawTile);

        // THEN

        $this->assertContains($drawTile, $this->mainBoardGLM->getDrawTiles());
        $this->assertSame($length, $this->mainBoardGLM->getDrawTiles()->count());
    }

    public function testRemoveDrawTileNotYetRemoved() : void
    {
        // GIVEN

        $drawTile = new DrawTilesGLM();
        $this->mainBoardGLM->addDrawTile($drawTile);

        // WHEN

        $this->mainBoardGLM->removeDrawTile($drawTile);

        // THEN

        $this->assertNotContains($drawTile, $this->mainBoardGLM->getBoardTiles());
        $this->assertNull($drawTile->getMainBoardGLM());
    }

    public function testRemoveDrawTileAlreadyRemoved() : void
    {
        // GIVEN

        $drawTile = new DrawTilesGLM();
        $this->mainBoardGLM->addDrawTile($drawTile);
        $this->mainBoardGLM->removeDrawTile($drawTile);
        $length = $this->mainBoardGLM->getDrawTiles()->count();

        // WHEN

        $this->mainBoardGLM->removeDrawTile($drawTile);

        // THEN

        $this->assertNotContains($drawTile, $this->mainBoardGLM->getBoardTiles());
        $this->assertSame($length, $this->mainBoardGLM->getDrawTiles()->count());
    }

    public function testSetWarehouse() : void
    {
        // GIVEN

        $warehouse = new WarehouseGLM();

        // WHEN

        $this->mainBoardGLM->setWarehouse($warehouse);

        // THEN

        $this->assertSame($warehouse, $this->mainBoardGLM->getWarehouse());
    }

    public function testAddPawnNotYetAdded() : void
    {
        // GIVEN

        $pawn = new PawnGLM();

        // WHEN

        $this->mainBoardGLM->addPawn($pawn);

        // THEN

        $this->assertContains($pawn, $this->mainBoardGLM->getPawns());
        $this->assertSame($this->mainBoardGLM, $pawn->getMainBoardGLM());
    }

    public function testAddPawnAlreadyAdded() : void
    {
        // GIVEN

        $pawn = new PawnGLM();
        $this->mainBoardGLM->addPawn($pawn);
        $length = $this->mainBoardGLM->getPawns()->count();

        // WHEN

        $this->mainBoardGLM->addPawn($pawn);

        // THEN

        $this->assertContains($pawn, $this->mainBoardGLM->getPawns());
        $this->assertSame($length, $this->mainBoardGLM->getPawns()->count());
    }

    public function testRemovePawnNotYetRemoved() : void
    {
        // GIVEN

        $pawn = new PawnGLM();
        $this->mainBoardGLM->addPawn($pawn);

        // WHEN

        $this->mainBoardGLM->removePawn($pawn);

        // THEN

        $this->assertNotContains($pawn, $this->mainBoardGLM->getPawns());
        $this->assertNull($pawn->getMainBoardGLM());
    }

    public function testRemovePawnsAlreadyRemoved() : void
    {
        // GIVEN

        $pawn = new PawnGLM();
        $this->mainBoardGLM->addPawn($pawn);
        $this->mainBoardGLM->removePawn($pawn);
        $length = $this->mainBoardGLM->getPawns()->count();

        // WHEN

        $this->mainBoardGLM->removePawn($pawn);

        // THEN

        $this->assertNotContains($pawn, $this->mainBoardGLM->getPawns());
        $this->assertSame($length,  $this->mainBoardGLM->getPawns()->count());
    }

    public function testSetGameWhenMainBoardIsNotSetInGame() : void
    {
        // GIVEN

        $game = new GameGLM();

        // WHEN

        $this->mainBoardGLM->setGameGLM($game);

        // THEN

        $this->assertSame($game, $this->mainBoardGLM->getGameGLM());
        $this->assertSame($this->mainBoardGLM, $game->getMainBoard());
    }

    public function testSetGameWhenMainBoardIsSetInGame() : void
    {
        // GIVEN

        $game = new GameGLM();
        $game->setMainBoard($this->mainBoardGLM);

        // WHEN

        $this->mainBoardGLM->setGameGLM($game);

        // THEN

        $this->assertSame($game, $this->mainBoardGLM->getGameGLM());
        $this->assertSame($this->mainBoardGLM, $game->getMainBoard());
    }

    public function testSetLastPosition() : void
    {
        // GIVEN

        $lastPosition = 7;

        // WHEN

        $this->mainBoardGLM->setLastPosition($lastPosition);

        // THEN

        $this->assertSame($lastPosition, $this->mainBoardGLM->getLastPosition());
    }

    protected function setUp(): void
    {
        $this->mainBoardGLM = new MainBoardGLM();
    }
}