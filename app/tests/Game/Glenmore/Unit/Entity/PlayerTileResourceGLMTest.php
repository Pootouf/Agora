<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use PHPUnit\Framework\TestCase;

class PlayerTileResourceGLMTest extends TestCase
{
    private PlayerTileResourceGLM $playerTileResourceGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->playerTileResourceGLM->getId() >= 0);
    }

    public function testSetResource() : void
    {
        // GIVEN

        $resource = new ResourceGLM();

        // WHEN

        $this->playerTileResourceGLM->setResource($resource);

        // THEN

        $this->assertSame($resource, $this->playerTileResourceGLM->getResource());
    }

    public function testSetQuantity() : void
    {
        // GIVEN

        $quantity = 5;

        // WHEN

        $this->playerTileResourceGLM->setQuantity($quantity);

        // THEN

        $this->assertSame($quantity, $this->playerTileResourceGLM->getQuantity());
    }

    public function testSetPlayerTile() : void
    {
        // GIVEN

        $playerTile = new PlayerTileGLM();

        // WHEN

        $this->playerTileResourceGLM->setPlayerTileGLM($playerTile);

        // THEN

        $this->assertSame($playerTile, $this->playerTileResourceGLM->getPlayerTileGLM());
    }

    public function testSetPlayer() : void
    {
        // GIVEN

        $player = new PlayerGLM("user", new GameGLM());

        // WHEN

        $this->playerTileResourceGLM->setPlayer($player);

        // THEN

        $this->assertSame($player, $this->playerTileResourceGLM->getPlayer());
    }

    protected function setUp(): void
    {
        $this->playerTileResourceGLM = new PlayerTileResourceGLM();
    }
}