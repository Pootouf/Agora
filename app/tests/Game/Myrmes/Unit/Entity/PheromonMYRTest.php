<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use PHPUnit\Framework\TestCase;

class PheromonMYRTest extends TestCase
{
    private PheromonMYR $pheromonMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->pheromonMYR->getId() >= 0);
        $this->assertNotNull($this->pheromonMYR->getPheromonTiles());
    }

    public function testAddPheromoneTileYetNotAdded() : void
    {
        // GIVEN

        $pheromoneTile = new PheromonTileMYR();

        // WHEN

        $this->pheromonMYR->addPheromonTile($pheromoneTile);

        // THEN

        $this->assertContains($pheromoneTile, $this->pheromonMYR->getPheromonTiles());
        $this->assertSame($this->pheromonMYR, $pheromoneTile->getPheromonMYR());
    }

    public function testRemovePheromoneTile() : void
    {
        // GIVEN

        $pheromoneTile = new PheromonTileMYR();
        $this->pheromonMYR->addPheromonTile($pheromoneTile);

        // WHEN

        $this->pheromonMYR->removePheromonTile($pheromoneTile);

        // THEN

        $this->assertNotContains($pheromoneTile, $this->pheromonMYR->getPheromonTiles());
        $this->assertNull($pheromoneTile->getPheromonMYR());
    }

    public function testSetHarvested() : void
    {
        // WHEN

        $this->pheromonMYR->setHarvested(true);

        // THEN

        $this->assertTrue($this->pheromonMYR->isHarvested());
    }

    public function testSetPlayer() : void
    {
        // GIVEN

        $player = new PlayerMYR("user", new GameMYR());

        // WHEN

        $this->pheromonMYR->setPlayer($player);

        // THEN

        $this->assertSame($player, $this->pheromonMYR->getPlayer());
    }

    public function testSetType() : void
    {
        // GIVEN

        $tileType = new TileTypeMYR();

        // WHEN

        $this->pheromonMYR->setType($tileType);

        // THEN

        $this->assertSame($tileType, $this->pheromonMYR->getType());
    }

    protected function setUp(): void
    {
        $this->pheromonMYR = new PheromonMYR();
    }
}