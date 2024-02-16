<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\Splendor\CardCostSPL;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\TokenSPL;
use PHPUnit\Framework\TestCase;

class DevelopmentCardsSPLTest extends TestCase
{
    private DevelopmentCardsSPL $developmentCardsSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertTrue($this->developmentCardsSPL->getId() >= 0);
        $this->assertEmpty($this->developmentCardsSPL->getCardCost());
    }

    public function testSetPrestigePoints(): void
    {
        //GIVEN
        $points = 456;
        //WHEN
        $this->developmentCardsSPL->setPrestigePoints($points);
        //THEN
        $this->assertSame($points, $this->developmentCardsSPL->getPrestigePoints());
    }

    public function testSetColor(): void
    {
        //GIVEN
        $color = "red";
        //WHEN
        $this->developmentCardsSPL->setColor($color);
        //THEN
        $this->assertSame($color, $this->developmentCardsSPL->getColor());
    }

    public function testSetLevel(): void
    {
        //GIVEN
        $level = DevelopmentCardsSPL::$LEVEL_THREE;
        //WHEN
        $this->developmentCardsSPL->setLevel($level);
        //THEN
        $this->assertSame($level, $this->developmentCardsSPL->getLevel());
    }
    protected function setUp(): void
    {
        $this->developmentCardsSPL = new DevelopmentCardsSPL();
    }
}


