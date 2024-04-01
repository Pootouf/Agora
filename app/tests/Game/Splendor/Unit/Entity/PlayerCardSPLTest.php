<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerCardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\RowSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\TokenSPL;
use PHPUnit\Framework\TestCase;

class PlayerCardSPLTest extends TestCase
{
    private PlayerCardSPL $playerCardSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertTrue($this->playerCardSPL->getId() >= 0);
    }

    public function testSetDevelopmentCard(): void
    {
        //GIVEN
        $card = new DevelopmentCardsSPL();
        //WHEN
        $this->playerCardSPL->setDevelopmentCard($card);
        //THEN
        $this->assertSame($card, $this->playerCardSPL->getDevelopmentCard());
    }

    public function testSetReservedWhenTrue(): void
    {
        //GIVEN
        $isReserved = true;
        //WHEN
        $this->playerCardSPL->setIsReserved($isReserved);
        //THEN
        $this->assertTrue($this->playerCardSPL->isIsReserved());
    }

    public function testSetReservedWhenFalse(): void
    {
        //GIVEN
        $isReserved = false;
        //WHEN
        $this->playerCardSPL->setIsReserved($isReserved);
        //THEN
        $this->assertFalse($this->playerCardSPL->isIsReserved());
    }

    public function testSetPersonalBoard(): void
    {
        //GIVEN
        $personalBoard = new PersonalBoardSPL();
        //WHEN
        $this->playerCardSPL->setPersonalBoardSPL($personalBoard);
        //THEN
        $this->assertSame($personalBoard, $this->playerCardSPL->getPersonalBoardSPL());
    }

    protected function setUp(): void
    {
        $player = new PlayerSPL('test', new GameSPL());
        $card = new DevelopmentCardsSPL();
        $bool = false;
        $this->playerCardSPL = new PlayerCardSPL($player, $card, $bool);
    }
}


