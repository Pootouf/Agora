<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\SelectedResourceGLM;
use App\Entity\Game\Glenmore\TileGLM;
use PHPUnit\Framework\TestCase;

class PlayerTileGLMTest extends TestCase
{
    private PlayerTileGLM $playerTileGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->playerTileGLM->getId() >= 0);
        $this->assertNotNull($this->playerTileGLM->getAdjacentTiles());
        $this->assertNotNull($this->playerTileGLM->getPlayerTileResource());
        $this->assertNotNull($this->playerTileGLM->getSelectedResources());
    }

    public function testSetPersonalBoard() : void
    {
        // GIVEN

        $personalBoard = new PersonalBoardGLM();

        // WHEN

        $this->playerTileGLM->setPersonalBoard($personalBoard);

        // THEN

        $this->assertSame($personalBoard, $this->playerTileGLM->getPersonalBoard());
    }

    public function testSetTile() : void
    {
        // GIVEN

        $tile = new TileGLM();

        // WHEN

        $this->playerTileGLM->setTile($tile);

        // THEN

        $this->assertSame($tile, $this->playerTileGLM->getTile());
    }

    public function testAddAdjacentTilesYetNotAdded() : void
    {
        // GIVEN

        $playerTile = new PlayerTileGLM();

        // WHEN

        $this->playerTileGLM->addAdjacentTile($playerTile);

        // THEN

        $this->assertContains($playerTile, $this->playerTileGLM->getAdjacentTiles());
    }

    public function testAddAdjacentTilesAlreadyAdded() : void
    {
        // GIVEN

        $playerTile = new PlayerTileGLM();
        $this->playerTileGLM->addAdjacentTile($playerTile);
        $length = $this->playerTileGLM->getAdjacentTiles()->count();

        // WHEN

        $this->playerTileGLM->addAdjacentTile($playerTile);

        // THEN

        $this->assertContains($playerTile, $this->playerTileGLM->getAdjacentTiles());
        $this->assertSame($length, $this->playerTileGLM->getAdjacentTiles()->count());
    }

    public function testRemoveAdjacentTile() : void
    {
        // GIVEN

        $playerTile = new PlayerTileGLM();
        $this->playerTileGLM->addAdjacentTile($playerTile);

        // WHEN

        $this->playerTileGLM->removeAdjacentTile($playerTile);

        // THEN

        $this->assertNotContains($playerTile, $this->playerTileGLM->getAdjacentTiles());
    }

    public function testAddPlayerTileResourceYetNotAdded() : void
    {
        // GIVEN

        $playerTileResource = new PlayerTileResourceGLM();

        // WHEN

        $this->playerTileGLM->addPlayerTileResource($playerTileResource);

        // THEN

        $this->assertContains($playerTileResource, $this->playerTileGLM->getPlayerTileResource());
        $this->assertSame($this->playerTileGLM, $playerTileResource->getPlayerTileGLM());
    }

    public function testAddPlayerTileResourceAlreadyAdded() : void
    {
        // GIVEN

        $playerTileResource = new PlayerTileResourceGLM();
        $this->playerTileGLM->addPlayerTileResource($playerTileResource);
        $length = $this->playerTileGLM->getPlayerTileResource()->count();

        // WHEN

        $this->playerTileGLM->addPlayerTileResource($playerTileResource);

        // THEN

        $this->assertContains($playerTileResource, $this->playerTileGLM->getPlayerTileResource());
        $this->assertSame($length, $this->playerTileGLM->getPlayerTileResource()->count());
    }

    public function testRemovePlayerTileResourceNotYetRemoved() : void
    {
        // GIVEN

        $playerTileResource = new PlayerTileResourceGLM();
        $this->playerTileGLM->addPlayerTileResource($playerTileResource);

        // WHEN

        $this->playerTileGLM->removePlayerTileResource($playerTileResource);

        // THEN

        $this->assertNotContains($playerTileResource, $this->playerTileGLM->getPlayerTileResource());
        $this->assertNull($playerTileResource->getPlayerTileGLM());
    }

    public function testRemovePlayerTileResourceAlreadyRemoved() : void
    {
        // GIVEN

        $playerTileResource = new PlayerTileResourceGLM();
        $this->playerTileGLM->addPlayerTileResource($playerTileResource);
        $this->playerTileGLM->removePlayerTileResource($playerTileResource);
        $length = $this->playerTileGLM->getPlayerTileResource()->count();

        // WHEN

        $this->playerTileGLM->removePlayerTileResource($playerTileResource);

        // THEN

        $this->assertNotContains($playerTileResource, $this->playerTileGLM->getPlayerTileResource());
        $this->assertSame($length, $this->playerTileGLM->getPlayerTileResource()->count());
    }

    public function testSetCoordinateX() : void
    {
        // GIVEN

        $abscissa = 4;

        // WHEN

        $this->playerTileGLM->setCoordX($abscissa);

        // THEN

        $this->assertSame($this->playerTileGLM->getCoordX(), $abscissa);
    }

    public function testSetCoordinateY() : void
    {
        // GIVEN

        $ordinate = 4;

        // WHEN

        $this->playerTileGLM->setCoordY($ordinate);

        // THEN

        $this->assertSame($this->playerTileGLM->getCoordY(), $ordinate);
    }

    public function testSetIsActivated() : void
    {
        // WHEN

        $this->playerTileGLM->setActivated(true);

        // THEN

        $this->assertTrue($this->playerTileGLM->isActivated());
    }

    public function testAddSelectedResourceYetNotAdded() : void
    {
        // GIVEN

        $selectedResource = new SelectedResourceGLM();

        // WHEN

        $this->playerTileGLM->addSelectedResource($selectedResource);

        // THEN

        $this->assertContains($selectedResource, $this->playerTileGLM->getSelectedResources());
        $this->assertSame($this->playerTileGLM, $selectedResource->getPlayerTile());
    }

    public function testAddSelectedResourceAlreadyAdded() : void
    {
        // GIVEN

        $selectedResource = new SelectedResourceGLM();
        $this->playerTileGLM->addSelectedResource($selectedResource);
        $length = $this->playerTileGLM->getSelectedResources()->count();

        // WHEN

        $this->playerTileGLM->addSelectedResource($selectedResource);

        // THEN

        $this->assertContains($selectedResource, $this->playerTileGLM->getSelectedResources());
        $this->assertSame($length, $this->playerTileGLM->getSelectedResources()->count());
    }

    public function testRemoveSelectedResourceYetNotRemoved() : void
    {
        // GIVEN

        $selectedResource = new SelectedResourceGLM();
        $this->playerTileGLM->addSelectedResource($selectedResource);

        // WHEN

        $this->playerTileGLM->removeSelectedResource($selectedResource);

        // THEN

        $this->assertNotContains($selectedResource, $this->playerTileGLM->getSelectedResources());
        $this->assertNull($selectedResource->getPlayerTile());
    }

    public function testRemoveSelectedResourceAlreadyRemoved() : void
    {
        // GIVEN

        $selectedResource = new SelectedResourceGLM();
        $this->playerTileGLM->addSelectedResource($selectedResource);
        $this->playerTileGLM->removeSelectedResource($selectedResource);
        $length = $this->playerTileGLM->getSelectedResources()->count();

        // WHEN

        $this->playerTileGLM->removeSelectedResource($selectedResource);

        // THEN

        $this->assertNotContains($selectedResource, $this->playerTileGLM->getSelectedResources());
        $this->assertSame($length, $this->playerTileGLM->getSelectedResources()->count());
    }

    protected function setUp(): void
    {
        $this->playerTileGLM = new PlayerTileGLM();
    }
}