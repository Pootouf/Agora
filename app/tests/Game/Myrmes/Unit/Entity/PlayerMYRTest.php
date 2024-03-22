<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenTileMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use PHPUnit\Framework\TestCase;

class PlayerMYRTest extends TestCase
{
    private PlayerMYR $playerMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->playerMYR->getId() >= 0);
        $this->assertNotNull($this->playerMYR->getGardenWorkerMYRs());
        $this->assertNotNull($this->playerMYR->getGameGoalMYRs());
        $this->assertNotNull($this->playerMYR->getAnthillHoleMYRs());
        $this->assertNotNull($this->playerMYR->getPheromonMYRs());
        $this->assertNotNull($this->playerMYR->getPreyMYRs());
    }

    public function testSetScore() : void
    {
        // GIVEN

        $score = 5;

        // WHEN

        $this->playerMYR->setScore($score);

        // THEN

        $this->assertSame($score, $this->playerMYR->getScore());
    }

    public function testSetGoalLevel() : void
    {
        // GIVEN

        $goalLevel = 2;

        // WHEN

        $this->playerMYR->setGoalLevel($goalLevel);

        // THEN

        $this->assertSame($goalLevel, $this->playerMYR->getGoalLevel());
    }

    public function testAddNurseYetNotAdded() : void
    {
        // GIVEN

        $nurse = new NurseMYR();

        // WHEN

        $this->playerMYR->addNurseMYR($nurse);

        // THEN

        $this->assertContains($nurse, $this->playerMYR->getNurseMYRs());
    }

    public function testRemoveNurseNotYetRemoved() : void
    {
        // GIVEN

        $nurse = new NurseMYR();
        $this->playerMYR->addNurseMYR($nurse);

        // WHEN

        $this->playerMYR->removeNurseMYR($nurse);

        // THEN

        $this->assertNotContains($nurse, $this->playerMYR->getNurseMYRs());
        $this->assertNull($nurse->getPlayer());
    }

    public function testAddAnthillWorkerYetNotAdded() : void
    {
        // GIVEN

        $anthillWorker = new AnthillWorkerMYR();

        // WHEN

        $this->playerMYR->addAnthillWorkerMYR($anthillWorker);

        // THEN

        $this->assertContains($anthillWorker, $this->playerMYR->getAnthillWorkerMYRs());
        $this->assertSame($this->playerMYR, $anthillWorker->getPlayer());
    }

    public function testRemoveAnthillWorkerYetNotRemoved() : void
    {
        // GIVEN

        $anthillWorker = new AnthillWorkerMYR();
        $this->playerMYR->addAnthillWorkerMYR($anthillWorker);

        // WHEN

        $this->playerMYR->removeAnthillWorkerMYR($anthillWorker);

        // THEN

        $this->assertNotContains($anthillWorker, $this->playerMYR->getAnthillWorkerMYRs());
        $this->assertNull($anthillWorker->getPlayer());
    }

    public function testAddGardenWorkerYetNotAdded() : void
    {
        // GIVEN

        $gardenWorker = new GardenWorkerMYR();

        // WHEN

        $this->playerMYR->addGardenWorkerMYR($gardenWorker);

        // THEN

        $this->assertContains($gardenWorker, $this->playerMYR->getGardenWorkerMYRs());
        $this->assertSame($this->playerMYR, $gardenWorker->getPlayer());
    }

    public function testRemoveGardenWorkerYetNotRemoved() : void
    {
        // GIVEN

        $gardenWorker = new GardenWorkerMYR();
        $this->playerMYR->addGardenWorkerMYR($gardenWorker);

        // WHEN

        $this->playerMYR->removeGardenWorkerMYR($gardenWorker);

        // THEN

        $this->assertNotContains($gardenWorker, $this->playerMYR->getGardenWorkerMYRs());
        $this->assertNull($gardenWorker->getPlayer());
    }

    public function testAddGardenTileYetNotAdded() : void
    {
        // GIVEN

        $gardenTile = new GardenTileMYR();

        // WHEN

        $this->playerMYR->addGardenTileMYR($gardenTile);

        // THEN

        $this->assertContains($gardenTile, $this->playerMYR->getGardenTileMYRs());
        $this->assertSame($this->playerMYR, $gardenTile->getPlayer());
    }

    public function testRemoveGardenTileYetNotRemoved() : void
    {
        // GIVEN

        $gardenTile = new GardenTileMYR();
        $this->playerMYR->addGardenTileMYR($gardenTile);

        // WHEN

        $this->playerMYR->removeGardenTileMYR($gardenTile);

        // THEN

        $this->assertNotContains($gardenTile, $this->playerMYR->getGardenTileMYRs());
        $this->assertNull($gardenTile->getPlayer());
    }

    public function testAddGameGoalYetNotAdded() : void
    {
        // GIVEN

        $gameGoal = new GameGoalMYR();

        // WHEN

        $this->playerMYR->addGameGoalMYR($gameGoal);

        // THEN

        $this->assertContains($gameGoal, $this->playerMYR->getGameGoalMYRs());
        $this->assertContains($this->playerMYR, $gameGoal->getPrecedentsPlayers());
    }

    public function testRemoveGameGoalYetNotRemoved() : void
    {
        // GIVEN

        $gameGoal = new GameGoalMYR();
        $this->playerMYR->addGameGoalMYR($gameGoal);

        // WHEN

        $this->playerMYR->removeGameGoalMYR($gameGoal);

        // THEN

        $this->assertNotContains($gameGoal, $this->playerMYR->getGameGoalMYRs());
        $this->assertNotContains($this->playerMYR, $gameGoal->getPrecedentsPlayers());
    }

    public function testAddAnthillHoleYetNotAdded() : void
    {
        // GIVEN

        $anthillHole = new AnthillHoleMYR();

        // WHEN

        $this->playerMYR->addAnthillHoleMYR($anthillHole);

        // THEN

        $this->assertContains($anthillHole, $this->playerMYR->getAnthillHoleMYRs());
        $this->assertSame($this->playerMYR, $anthillHole->getPlayer());
    }

    public function testRemoveAnthillHoleYetNotRemoved() : void
    {
        // GIVEN

        $anthillHole = new AnthillHoleMYR();
        $this->playerMYR->addAnthillHoleMYR($anthillHole);

        // WHEN

        $this->playerMYR->removeAnthillHoleMYR($anthillHole);

        // THEN

        $this->assertNotContains($anthillHole, $this->playerMYR->getAnthillHoleMYRs());
        $this->assertNull($anthillHole->getPlayer());
    }

    public function testSetPersonalBoard() : void
    {
        // GIVEN

        $personalBoard = new PersonalBoardMYR();

        // WHEN

        $this->playerMYR->setPersonalBoardMYR($personalBoard);

        // THEN

        $this->assertSame($personalBoard, $this->playerMYR->getPersonalBoardMYR());
        $this->assertSame($this->playerMYR, $personalBoard->getPlayer());
    }

    public function testSetGame() : void
    {
        // GIVEN

        $game = new GameMYR();

        // WHEN

        $this->playerMYR->setGameMyr($game);

        // THEN

        $this->assertSame($game, $this->playerMYR->getGameMyr());
    }

    protected function setUp(): void
    {
        $this->playerMYR = new PlayerMYR("user", new GameMYR());
    }
}