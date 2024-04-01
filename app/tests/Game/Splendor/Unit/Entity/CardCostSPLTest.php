<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\Splendor\CardCostSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Entity\Game\Splendor\TokenSPL;
use PHPUnit\Framework\TestCase;

class CardCostSPLTest extends TestCase
{
    private CardCostSPL $cardCostSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertTrue($this->cardCostSPL->getId() >= 0);
    }

    public function testSetColor(): void
    {
        //GIVEN
        $color = SplendorParameters::$COLOR_BLACK;
        //WHEN
        $this->cardCostSPL->setColor($color);
        //THEN
        $this->assertSame($color, $this->cardCostSPL->getColor());
    }

    public function testSetPrice(): void
    {
        //GIVEN
        $price = 5;
        //WHEN
        $this->cardCostSPL->setPrice($price);
        //THEN
        $this->assertSame($price, $this->cardCostSPL->getPrice());
    }

    protected function setUp(): void
    {
        $this->cardCostSPL = new CardCostSPL();
    }
}


