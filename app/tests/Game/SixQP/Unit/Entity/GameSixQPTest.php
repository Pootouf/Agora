<?php

namespace App\Tests\Game\SixQP\Unit\Entity;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Service\Game\AbstractGameManagerService;
use PHPUnit\Framework\TestCase;
use App\Entity\Game\SixQP\ChosenCardSixQP;

class GameSixQPTest extends TestCase
{
    private GameSixQP $gameSixQP;

    protected function setUp(): void
    {
        $this->gameSixQP = new GameSixQP();
        $this->gameSixQP->addRowSixQP(new RowSixQP());
        $this->gameSixQP->addPlayerSixQP(new PlayerSixQP("test", $this->gameSixQP));
    }

    public function testInitGame(): void
    {
        $this->assertSame(AbstractGameManagerService::$SIXQP_LABEL, $this->gameSixQP->getGameName());
        $this->assertFalse($this->gameSixQP->isLaunched());
    }

    public function testAddRow(): void
    {
        //GIVEN
        $row = new RowSixQP();
        //WHEN
        $this->gameSixQP->addRowSixQP($row);
        //THEN
        $this->assertContains($row, $this->gameSixQP->getRowSixQPs());
    }

    public function testRemoveRow(): void
    {
        //GIVEN
        $row = $this->gameSixQP->getRowSixQPs()->first();
        //WHEN
        $this->gameSixQP->removeRowSixQP($row);
        //THEN
        $this->assertNotContains($row, $this->gameSixQP->getRowSixQPs());
    }

    public function testAddPlayer(): void
    {
        //GIVEN
        $player = new PlayerSixQP("test", $this->gameSixQP);
        //WHEN
        $this->gameSixQP->addPlayerSixQP($player);
        //THEN
        $this->assertContains($player, $this->gameSixQP->getPlayerSixQPs());
    }

    public function testRemovePlayer(): void
    {
        //GIVEN
        $player = $this->gameSixQP->getPlayerSixQPs()->first();
        //WHEN
        $this->gameSixQP->removePlayerSixQP($player);
        //THEN
        $this->assertNotContains($player, $this->gameSixQP->getPlayerSixQPs());
    }
}
