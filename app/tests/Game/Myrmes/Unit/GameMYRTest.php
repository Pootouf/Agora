<?php

namespace App\Tests\Game\Myrmes\Unit;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use PHPUnit\Framework\TestCase;

class GameMYRTest extends TestCase
{
    private GameMYR $gameMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->gameMYR->getId() >= 0);
        $this->assertNotNull($this->gameMYR->getPlayers());
    }

    public function testSetFirstPlayer() : void
    {
        // GIVEN

        $player = new PlayerMYR("test", new GameMYR());

        // WHEN

        $this->gameMYR->setFirstPlayer($player);

        // THEN

        $this->assertSame($player, $this->gameMYR->getFirstPlayer());
    }

    public function testAddPlayersNotYetAdded() : void
    {
        // GIVEN

        $player = new PlayerMYR("test", new GameMYR());

        // WHEN

        $this->gameMYR->addPlayer($player);

        // THEN

        $this->assertContains($player, $this->gameMYR->getPlayers());
        $this->assertSame($this->gameMYR, $player->getGameMyr());
    }

    public function testRemovePlayersNotYetRemoved() : void
    {
        // GIVEN

        $player = new PlayerMYR("test", new GameMYR());
        $this->gameMYR->addPlayer($player);

        // WHEN

        $this->gameMYR->removePlayer($player);

        // THEN

        $this->assertNotContains($player, $this->gameMYR->getPlayers());
        $this->assertNull($player->getGameMyr());
    }

    public function test() : void
    {
        // GIVEN

        $mainBoard = new MainBoardMYR();

        // WHEN

        $this->gameMYR->setMainBoardMYR($mainBoard);

        // THEN

        $this->assertSame($mainBoard, $this->gameMYR->getMainBoardMYR());
        $this->assertSame($this->gameMYR, $mainBoard->getGame());
    }

    protected function setUp(): void
    {
        $this->gameMYR = new GameMYR();
    }
}