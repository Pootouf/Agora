<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\TileMYR;
use PHPUnit\Framework\TestCase;

class PheromonTileMYRTest extends TestCase
{
    private PheromonTileMYR $pheromonTileMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->pheromonTileMYR->getId() >= 0);
    }

    public function testSetTile() : void
    {
        // GIVEN

        $tile = new TileMYR();

        // WHEN

        $this->pheromonTileMYR->setTile($tile);

        // THEN

        $this->assertSame($tile, $this->pheromonTileMYR->getTile());
    }

    public function testSetResource() : void
    {
        // GIVEN

        $resource = new ResourceMYR();

        // WHEN

        $this->pheromonTileMYR->setResource($resource);

        // THEN

        $this->assertSame($resource, $this->pheromonTileMYR->getResource());
    }

    public function testSetPheromone() : void
    {
        // GIVEN

        $pheromone = new PheromonMYR();

        // WHEN

        $this->pheromonTileMYR->setPheromonMYR($pheromone);

        // THEN

        $this->assertSame($pheromone, $this->pheromonTileMYR->getPheromonMYR());
    }

    protected function setUp(): void
    {
        $this->pheromonTileMYR = new PheromonTileMYR();
    }
}