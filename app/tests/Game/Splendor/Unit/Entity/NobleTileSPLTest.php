<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\Splendor\CardCostSPL;
use App\Entity\Game\Splendor\NobleTileSPL;
use PHPUnit\Framework\TestCase;

class NobleTileSPLTest extends TestCase
{
    private NobleTileSPL $nobleTileSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertTrue($this->nobleTileSPL->getId() >= 0);
        $this->assertEmpty($this->nobleTileSPL->getCardsCost());
    }

    public function testSetPrestigePoints(): void
    {
        //GIVEN
        $points = 2;
        //WHEN
        $this->nobleTileSPL->setPrestigePoints($points);
        //THEN
        $this->assertSame($points, $this->nobleTileSPL->getPrestigePoints());
    }

    public function testAddCardCostWhenNotOwned(): void
    {
        //GIVEN
        $cardCost = new CardCostSPL();
        //WHEN
        $this->nobleTileSPL->addCardsCost($cardCost);
        //THEN
        $this->assertContains($cardCost, $this->nobleTileSPL->getCardsCost());
    }

    public function testAddCardCostWhenOwned(): void
    {
        //GIVEN
        $cardCost = new CardCostSPL();
        $this->nobleTileSPL->addCardsCost($cardCost);
        $expectedLength = 1;
        //WHEN
        $this->nobleTileSPL->addCardsCost($cardCost);
        //THEN
        $this->assertContains($cardCost, $this->nobleTileSPL->getCardsCost());
        $this->assertEquals($expectedLength, $this->nobleTileSPL->getCardsCost()->count());
    }

    public function testRemoveCardCost(): void
    {
        //GIVEN
        $cardCost = new CardCostSPL();
        $this->nobleTileSPL->addCardsCost($cardCost);
        //WHEN
        $this->nobleTileSPL->removeCardsCost($cardCost);
        //THEN
        $this->assertNotContains($cardCost, $this->nobleTileSPL->getCardsCost());
    }

    protected function setUp(): void
    {
        $this->nobleTileSPL = new NobleTileSPL();
    }
}


