<?php

namespace App\Tests\Game\Myrmes\Unit;

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

    public function testSetXMinCoordinate() : void
    {
        // GIVEN

        $xMin = 7;

        // WHEN

        $this->tileMYR->setXMinCoord($xMin);

        // THEN

        $this->assertSame($xMin, $this->tileMYR->getXMinCoord());
    }

    public function testSetXMaxCoordinate() : void
    {
        // GIVEN

        $xMax = 8;

        // WHEN

        $this->tileMYR->setXMaxCoord($xMax);

        // THEN

        $this->assertSame($xMax, $this->tileMYR->getXMaxCoord());
    }

    public function testSetYCoordinate() : void
    {
        // GIVEN

        $y = 2;

        // WHEN

        $this->tileMYR->setYCoord($y);

        // THEN

        $this->assertSame($y, $this->tileMYR->getYCoord());
    }

    public function testSetType() : void
    {
        // GIVEN

        $type = new TileTypeMYR();

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