<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use PHPUnit\Framework\TestCase;

class GameSPLTest extends TestCase
{
    private GameSPL $gameSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertEmpty($this->gameSPL->getPlayers());
    }

    public function testAddPlayerWhenNotJoined(): void
    {
        //GIVEN
        $player = new PlayerSPL('test', $this->gameSPL);
        //WHEN
        $this->gameSPL->addPlayer($player);
        //THEN
        $this->assertContains($player, $this->gameSPL->getPlayers());
    }

    public function testAddPlayerWhenJoined(): void
    {
        //GIVEN
        $player = new PlayerSPL('test', $this->gameSPL);
        $this->gameSPL->addPlayer($player);
        $expectedLength = 1;
        //WHEN
        $this->gameSPL->addPlayer($player);
        //THEN
        $this->assertContains($player, $this->gameSPL->getPlayers());
        $this->assertSame($expectedLength, $this->gameSPL->getPlayers()->count());
    }

    public function testRemovePlayer(): void
    {
        //GIVEN
        $player = new PlayerSPL('test', $this->gameSPL);
        $this->gameSPL->addPlayer($player);
        //WHEN
        $this->gameSPL->removePlayer($player);
        //THEN
        $this->assertNotContains($player, $this->gameSPL->getPlayers());
    }

    public function testSetMainBoardSPL(): void
    {
        //GIVEN
        $mainBoard = new MainBoardSPL();
        //WHEN
        $this->gameSPL->setMainBoard($mainBoard);
        //THEN
        $this->assertSame($mainBoard, $this->gameSPL->getMainBoard());
    }
    protected function setUp(): void
    {
        $this->gameSPL = new GameSPL();
    }
}


