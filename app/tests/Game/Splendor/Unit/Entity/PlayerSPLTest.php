<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use PHPUnit\Framework\TestCase;

class PlayerSPLTest extends TestCase
{
    private PlayerSPL $playerSPL;

    public function testInit(): void
    {
        //GIVEN
        $username = "t";
        $points = 0;
        $game = new GameSPL();
        //WHEN
        $player = new PlayerSPL($username, $game);
        //THEN
        $this->assertSame($username, $player->getUsername());
        $this->assertSame($game, $player->getGame());
        $this->assertSame($points, $player->getScore());
    }

    public function testSetPersonalBoard(): void
    {
        //GIVEN
        $personalBoard = new PersonalBoardSPL();
        //WHEN
        $this->playerSPL->setPersonalBoard($personalBoard);
        //THEN
        $this->assertSame($personalBoard, $this->playerSPL->getPersonalBoard());
    }

    public function testSetGame(): void
    {
        //GIVEN
        $game = new GameSPL();
        //WHEN
        $this->playerSPL->setGame($game);
        //THEN
        $this->assertSame($game, $this->playerSPL->getGame());
    }

    protected function setUp(): void
    {
        $game = new GameSPL();
        $this->playerSPL = new PlayerSPL('test', $game);
    }
}