<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use PHPUnit\Framework\TestCase;

class TileMYRTest extends TestCase
{
    private TileMYR $tileMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->tileMYR->getId() >= 0);
    }

    public function testSetCoordinateX() : void
    {
        // GIVEN

        $x = 4;

        // WHEN

        $this->tileMYR->setCoordX($x);

        // THEN

        $this->assertSame($x, $this->tileMYR->getCoordX());
    }

    public function testSetCoordinateY() : void
    {
        // GIVEN

        $y = 4;

        // WHEN

        $this->tileMYR->setCoordY($y);

        // THEN

        $this->assertSame($y, $this->tileMYR->getCoordY());
    }

    public function testSetType() : void
    {
        // GIVEN

        $type = "type";

        // WHEN

        $this->tileMYR->setType($type);

        // THEN

        $this->assertSame($type, $this->tileMYR->getType());
    }

    protected function setUp(): void
    {
        $this->tileMYR = new TileMYR();
    }
}