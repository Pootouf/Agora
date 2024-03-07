<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use PHPUnit\Framework\TestCase;

class PlayerGLMTest extends TestCase
{

    private PlayerGLM $playerGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->playerGLM->getId() >= 0);
        $this->assertNotNull($this->playerGLM->getGameGLM());
        $this->assertSame("user", $this->playerGLM->getUsername());
    }

    public function testSetPersonalBoard() : void {
        // GIVEN

        $personalBoard = new PersonalBoardGLM();

        // WHEN

        $this->playerGLM->setPersonalBoard($personalBoard);

        // THEN

        $this->assertSame($personalBoard, $this->playerGLM->getPersonalBoard());
    }

    public function testSetPoints() : void
    {
        // GIVEN

        $point = 40;

        // WHEN

        $this->playerGLM->setPoints($point);

        // THEN

        $this->assertSame($point, $this->playerGLM->getPoints());
    }

    public function testSetPawn() : void
    {
        // GIVEN

        $pawn = new PawnGLM();

        // WHEN

        $this->playerGLM->setPawn($pawn);

        // THEN

        $this->assertSame($pawn, $this->playerGLM->getPawn());
    }

    public function testSetGame() : void
    {
        // GIVEN

        $game = new GameGLM();

        // WHEN

        $this->playerGLM->setGameGLM($game);

        // THEN

        $this->assertSame($game, $this->playerGLM->getGameGLM());
    }

    protected function setUp(): void
    {
        $this->playerGLM = new PlayerGLM("user", new GameGLM());
    }
}