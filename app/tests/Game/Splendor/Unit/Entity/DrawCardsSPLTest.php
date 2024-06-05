<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use PHPUnit\Framework\TestCase;

class DrawCardsSPLTest extends TestCase
{
    private DrawCardsSPL $drawCardsSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertTrue($this->drawCardsSPL->getId() >= 0);
        $this->assertEmpty($this->drawCardsSPL->getDevelopmentCards());
    }

    public function testSetLevel(): void
    {
        //GIVEN
        $level = SplendorParameters::DRAW_CARD_LEVEL_THREE;
        //WHEN
        $this->drawCardsSPL->setLevel($level);
        //THEN
        $this->assertSame($level, $this->drawCardsSPL->getLevel());
    }

    public function testAddDevelopmentCardWhenNotOwned(): void
    {
        //GIVEN
        $developCard = new DevelopmentCardsSPL();
        //WHEN
        $this->drawCardsSPL->addDevelopmentCard($developCard);
        //THEN
        $this->assertContains($developCard, $this->drawCardsSPL->getDevelopmentCards());
    }

    public function testAddDevelopmentCardWhenOwned(): void
    {
        //GIVEN
        $developCard = new DevelopmentCardsSPL();
        $this->drawCardsSPL->addDevelopmentCard($developCard);
        $expectedLength = 1;
        //WHEN
        $this->drawCardsSPL->addDevelopmentCard($developCard);
        //THEN
        $this->assertContains($developCard, $this->drawCardsSPL->getDevelopmentCards());
        $this->assertSame($expectedLength, $this->drawCardsSPL->getDevelopmentCards()->count());
    }

    public function testRemoveDevelopmentCard(): void
    {
        //GIVEN
        $developCard = new DevelopmentCardsSPL();
        $this->drawCardsSPL->addDevelopmentCard($developCard);
        //WHEN
        $this->drawCardsSPL->removeDevelopmentCard($developCard);
        //THEN
        $this->assertNotContains($developCard, $this->drawCardsSPL->getDevelopmentCards());
    }

    public function testSetMainBoardSPL(): void
    {
        //GIVEN
        $mainBoard = new MainBoardSPL();
        //WHEN
        $this->drawCardsSPL->setMainBoardSPL($mainBoard);
        //THEN
        $this->assertSame($mainBoard, $this->drawCardsSPL->getMainBoardSPL());
    }
    protected function setUp(): void
    {
        $this->drawCardsSPL = new DrawCardsSPL();
    }
}


