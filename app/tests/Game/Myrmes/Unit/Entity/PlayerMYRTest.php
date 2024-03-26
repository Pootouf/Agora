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
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PreyMYR;
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

    public function testAddPheromoneYetNotAdded() : void
    {
        // GIVEN

        $pheromone = new PheromonMYR();

        // WHEN

        $this->playerMYR->addPheromonMYR($pheromone);

        // THEN

        $this->assertContains($pheromone, $this->playerMYR->getPheromonMYRs());
        $this->assertSame($pheromone->getPlayer(), $this->playerMYR);
    }

    public function testRemovePheromoneYetNotRemoved() : void
    {
        // GIVEN

        $pheromone = new PheromonMYR();
        $this->playerMYR->addPheromonMYR($pheromone);

        // WHEN

        $this->playerMYR->removePheromonMYR($pheromone);

        // THEN

        $this->assertNotContains($pheromone, $this->playerMYR->getPheromonMYRs());
        $this->assertNull($pheromone->getPlayer());
    }

    public function testAddPreyNotYetAdded() : void
    {
        // GIVEN

        $prey = new PreyMYR();

        // WHEN

        $this->playerMYR->addPreyMYR($prey);

        // THEN

        $this->assertContains($prey, $this->playerMYR->getPreyMYRs());
        $this->assertSame($this->playerMYR, $prey->getPlayer());
    }

    public function testRemovePreyYetNotRemoved() : void
    {
        // GIVEN

        $prey = new PreyMYR();
        $this->playerMYR->addPreyMYR($prey);

        // WHEN

        $this->playerMYR->removePreyMYR($prey);

        // THEN

        $this->assertNotContains($prey, $this->playerMYR->getPreyMYRs());
        $this->assertNull($prey->getPlayer());
    }

    protected function setUp(): void
    {
        $this->playerMYR = new PlayerMYR("user", new GameMYR());
    }
}