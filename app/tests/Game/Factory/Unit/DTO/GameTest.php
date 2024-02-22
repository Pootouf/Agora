<?php

namespace App\Tests\Game\Factory\Unit\DTO;

use App\Entity\Game\DTO\Component;
use App\Entity\Game\DTO\Game;
use App\Entity\Game\Help;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{

    private Game $game;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // setUp()
        //THEN
        $this->assertTrue($this->game->getId() >= 0);
    }
    public function testSetLaunchedWhenTrue(): void
    {
        //GIVEN
        $isLaunched = true;
        //WHEN
        $this->game->setLaunched($isLaunched);
        //THEN
        $this->assertTrue($this->game->isLaunched());
    }

    public function testSetLaunchedWhenFalse(): void
    {
        //GIVEN
        $isLaunched = false;
        //WHEN
        $this->game->setLaunched($isLaunched);
        //THEN
        $this->assertFalse($this->game->isLaunched());
    }

    public function testSetGameName(): void
    {
        //GIVEN
        $name = "aaaa";
        //WHEN
        $this->game->setGameName($name);
        //THEN
        $this->assertSame($name, $this->game->getGameName());
    }
    protected function setUp(): void
    {
        $this->game = new Game();
    }
}