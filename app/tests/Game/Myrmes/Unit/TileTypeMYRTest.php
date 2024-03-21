<?php

namespace App\Tests\Game\Myrmes\Unit;

use App\Entity\Game\Myrmes\TileTypeMYR;
use PHPUnit\Framework\TestCase;

class TileTypeMYRTest extends TestCase
{
    private TileTypeMYR $tileTypeMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by method setUp

        // THEN
        $this->assertTrue($this->tileTypeMYR->getId() >= 0);
    }

    public function testSetOrientation() : void
    {
        // GIVEN

        $orientation = 1;

        // WHEN

        $this->tileTypeMYR->setOrientation($orientation);

        // THEN

        $this->assertSame($orientation, $this->tileTypeMYR->getOrientation());
    }

    public function testSetType() : void
    {
        // GIVEN

        $type = 1;

        // WHEN

        $this->tileTypeMYR->setType($type);

        // THEN

        $this->assertSame($type, $this->tileTypeMYR->getType());
    }

    protected function setUp(): void
    {
        $this->tileTypeMYR = new TileTypeMYR();
    }
}