<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\RowSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\TokenSPL;
use PHPUnit\Framework\TestCase;

class RowSPLTest extends TestCase
{
    private RowSPL $rowSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertTrue($this->rowSPL->getId() >= 0);
        $this->assertEmpty($this->rowSPL->getDevelopmentCards());
    }

    public function testSetLevel(): void
    {
        //GIVEN
        $level = 1;
        //WHEN
        $this->rowSPL->setLevel($level);
        //THEN
        $this->assertSame($level, $this->rowSPL->getLevel());
    }

    public function testAddDevelopmentCard(): void
    {
        //GIVEN
        $card = new DevelopmentCardsSPL();
        //WHEN
        $this->rowSPL->addDevelopmentCard($card);
        //THEN
        $this->assertContains($card, $this->rowSPL->getDevelopmentCards());
    }

    public function testRemoveDevelopmentCard(): void
    {
        //GIVEN
        $card = new DevelopmentCardsSPL();
        $this->rowSPL->addDevelopmentCard($card);
        //WHEN
        $this->rowSPL->removeDevelopmentCard($card);
        //THEN
        $this->assertNotContains($card, $this->rowSPL->getDevelopmentCards());
    }

    public function testSetMainBoard(): void
    {
        //GIVEN
        $mainBoard = new MainBoardSPL();
        //WHEN
        $this->rowSPL->setMainBoardSPL($mainBoard);
        //THEN
        $this->assertSame($mainBoard, $this->rowSPL->getMainBoardSPL());
    }

    protected function setUp(): void
    {
        $this->rowSPL = new RowSPL();
    }
}
