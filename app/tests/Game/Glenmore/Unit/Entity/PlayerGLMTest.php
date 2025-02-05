<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
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
        $this->assertNotNull($this->playerGLM->getPlayerTileResourceGLMs());
    }

    public function testSetPersonalBoard() : void
    {
        // GIVEN

        $personalBoard = new PersonalBoardGLM();

        // WHEN

        $this->playerGLM->setPersonalBoard($personalBoard);

        // THEN

        $this->assertSame($personalBoard, $this->playerGLM->getPersonalBoard());
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

    public function testAddPlayerTileResourceYetNotAdded() : void
    {
        // GIVEN

        $playerTileResource = new PlayerTileResourceGLM();

        // WHEN

        $this->playerGLM->addPlayerTileResourceGLM($playerTileResource);

        // THEN

        $this->assertContains($playerTileResource, $this->playerGLM->getPlayerTileResourceGLMs());
    }

    public function testAddPlayerTileResourceAlreadyAdded() : void
    {
        // GIVEN

        $playerTileResource = new PlayerTileResourceGLM();
        $this->playerGLM->addPlayerTileResourceGLM($playerTileResource);
        $length = $this->playerGLM->getPlayerTileResourceGLMs()->count();

        // WHEN

        $this->playerGLM->addPlayerTileResourceGLM($playerTileResource);

        // THEN

        $this->assertContains($playerTileResource, $this->playerGLM->getPlayerTileResourceGLMs());
        $this->assertSame($length, $this->playerGLM->getPlayerTileResourceGLMs()->count());
    }

    public function testAddPlayerTileResourceNotYetRemoved() : void
    {
        // GIVEN

        $playerTileResource = new PlayerTileResourceGLM();
        $this->playerGLM->addPlayerTileResourceGLM($playerTileResource);

        // WHEN

        $this->playerGLM->removePlayerTileResourceGLM($playerTileResource);

        // THEN

        $this->assertNotContains($playerTileResource, $this->playerGLM->getPlayerTileResourceGLMs());
    }

    public function testAddPlayerTileResourceAlreadyRemoved() : void
    {
        // GIVEN

        $playerTileResource = new PlayerTileResourceGLM();
        $this->playerGLM->addPlayerTileResourceGLM($playerTileResource);
        $this->playerGLM->removePlayerTileResourceGLM($playerTileResource);
        $length = $this->playerGLM->getPlayerTileResourceGLMs()->count();

        // WHEN

        $this->playerGLM->removePlayerTileResourceGLM($playerTileResource);

        // THEN

        $this->assertNotContains($playerTileResource, $this->playerGLM->getPlayerTileResourceGLMs());
        $this->assertSame($length, $this->playerGLM->getPlayerTileResourceGLMs()->count());
    }

    public function testSetActivatedResourceSelection() : void
    {
        // GIVEN et WHEN

        $this->playerGLM->setActivatedResourceSelection(true);

        // THEN

        $this->assertTrue($this->playerGLM->isActivatedResourceSelection());
    }

    public function testSetBot() : void
    {
        // GIVEN et WHEN

        $this->playerGLM->setBot(true);

        // THEN

        $this->assertTrue($this->playerGLM->isBot());
    }

    public function testSetPreviousPhase() : void
    {
        // GIVEN

        $previousPhase = 3;

        // WHEN


        $this->playerGLM->setPreviousPhase($previousPhase);

        // THEN

        $this->assertSame($previousPhase, $this->playerGLM->getPreviousPhase());
    }

    public function testSetActivatedNewResourcesAcquisition() : void
    {
        // GIVEN

        $activateNewResourceAcquisition = true;

        // WHEN

        $this->playerGLM->setActivatedNewResourcesAcqusition($activateNewResourceAcquisition);

        // THEN

        $this->assertSame($activateNewResourceAcquisition, $this->playerGLM->isActivatedNewResourcesAcqusition());
    }

    protected function setUp(): void
    {
        $this->playerGLM = new PlayerGLM("user", new GameGLM());
    }
}