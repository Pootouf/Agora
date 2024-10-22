<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use PHPUnit\Framework\TestCase;

class PawnGLMTest extends TestCase
{
    private PawnGLM $pawnGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->pawnGLM->getId() >= 0);
    }

    public function testSetPosition() : void
    {
        // GIVEN

        $position = 5;

        // WHEN

        $this->pawnGLM->setPosition($position);

        // THEN

        $this->assertSame($position, $this->pawnGLM->getPosition());
    }

    public function testSetPlayerGLMWhenPawnIsNotSetInPlayer() : void
    {
        // GIVEN

        $player = new PlayerGLM("user", new GameGLM());

        // WHEN

        $this->pawnGLM->setPlayerGLM($player);

        // THEN

        $this->assertSame($player, $this->pawnGLM->getPlayerGLM());
        $this->assertSame($player->getPawn(), $this->pawnGLM);
    }

    public function testSetPlayerGLMWhenPawnIsSetInPlayer() : void
    {
        // GIVEN

        $player = new PlayerGLM("user", new GameGLM());
        $player->setPawn($this->pawnGLM);

        // WHEN

        $this->pawnGLM->setPlayerGLM($player);

        // THEN

        $this->assertSame($player, $this->pawnGLM->getPlayerGLM());
        $this->assertSame($player->getPawn(), $this->pawnGLM);
    }

    public function testSetMainBoard() : void
    {
        // GIVEN

        $mainBoard = new MainBoardGLM();

        // GIVEN

        $this->pawnGLM->setMainBoardGLM($mainBoard);

        // THEN

        $this->assertSame($mainBoard, $this->pawnGLM->getMainBoardGLM());
    }

    public function testSetDice() : void
    {
        // GIVEN et WHEN

        $this->pawnGLM->setDice(true);

        // THEN

        $this->assertTrue($this->pawnGLM->isDice());

    }

    protected function setUp(): void
    {
        $this->pawnGLM = new PawnGLM();
    }
}