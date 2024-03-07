<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use PHPUnit\Framework\TestCase;

class GameGLMTest extends TestCase
{

    private GameGLM $gameGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->gameGLM->getId() >= 0);
        $this->assertNotNull($this->gameGLM->getPlayers());
    }

    public function testAddPlayerNotYetAdded() : void
    {
        // GIVEN

        $player = new PlayerGLM("user", $this->gameGLM);

        // WHEN

        $this->gameGLM->addPlayer($player);

        // THEN

        $this->assertContains($player, $this->gameGLM->getPlayers());
        $this->assertSame($this->gameGLM, $player->getGameGLM());
    }

    public function testAddPlayerAlreadyAdded() : void
    {
        // GIVEN

        $player = new PlayerGLM("user", $this->gameGLM);
        $this->gameGLM->addPlayer($player);
        $length = $this->gameGLM->getPlayers()->count();

        // WHEN

        $this->gameGLM->addPlayer($player);

        // THEN

        $this->assertSame($length, $this->gameGLM->getPlayers()->count());
    }

    public function testRemovePlayerYetNotRemoved() : void
    {
        // GIVEN

        $player = new PlayerGLM("user", $this->gameGLM);
        $this->gameGLM->addPlayer($player);

        // WHEN

        $this->gameGLM->removePlayer($player);

        // THEN

        $this->assertNotContains($player, $this->gameGLM->getPlayers());
    }

    public function testRemovePlayerAlreadyRemoved() : void
    {
        // GIVEN

        $player = new PlayerGLM("user", $this->gameGLM);
        $this->gameGLM->addPlayer($player);
        $this->gameGLM->removePlayer($player);
        $length = $this->gameGLM->getPlayers()->count();

        // WHEN

        $this->gameGLM->removePlayer($player);

        // THEN

        $this->assertSame($length, $this->gameGLM->getPlayers()->count());
    }

    public function testSetMainBoard() : void
    {
        // GIVEN

        $mainBoard = new MainBoardGLM();

        // WHEN

        $this->gameGLM->setMainBoard($mainBoard);

        // THEN

        $this->assertSame($mainBoard, $this->gameGLM->getMainBoard());
    }

    protected function setUp(): void
    {
        $this->gameGLM = new GameGLM();
    }
}