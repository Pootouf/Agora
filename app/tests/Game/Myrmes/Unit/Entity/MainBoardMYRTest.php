<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Entity\Game\Myrmes\TileMYR;
use PHPUnit\Framework\TestCase;

class MainBoardMYRTest extends TestCase
{
    private MainBoardMYR $mainBoardMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->mainBoardMYR->getId() >= 0);
        $this->assertNotNull($this->mainBoardMYR->getGardenWorkers());
        $this->assertNotNull($this->mainBoardMYR->getPreys());
        $this->assertNotNull($this->mainBoardMYR->getTiles());
        $this->assertNotNull($this->mainBoardMYR->getAnthillHoles());
    }

    public function testSetYearNum() : void
    {
        // GIVEN

        $yearNum = 3;

        // WHEN

        $this->mainBoardMYR->setYearNum($yearNum);

        // THEN

        $this->assertSame($yearNum, $this->mainBoardMYR->getYearNum());
    }

    public function testSetGame() : void
    {
        // GIVEN

        $game = new GameMYR();

        // WHEN

        $this->mainBoardMYR->setGame($game);

        // THEN

        $this->assertSame($game, $this->mainBoardMYR->getGame());
    }

    public function testAddGardenWorkerNotYetAdded() : void
    {
        // GIVEN

        $gardenWorker = new GardenWorkerMYR();

        // WHEN

        $this->mainBoardMYR->addGardenWorker($gardenWorker);

        // THEN

        $this->assertContains($gardenWorker, $this->mainBoardMYR->getGardenWorkers());
        $this->assertSame($this->mainBoardMYR, $gardenWorker->getMainBoardMYR());
    }

    public function testRemoveGardenWorkerNotYetRemoved() : void
    {
        // GIVEN

        $gardenWorker = new GardenWorkerMYR();
        $this->mainBoardMYR->addGardenWorker($gardenWorker);

        // WHEN

        $this->mainBoardMYR->removeGardenWorker($gardenWorker);

        // THEN

        $this->assertNotContains($gardenWorker, $this->mainBoardMYR->getGardenWorkers());
        $this->assertNull($gardenWorker->getMainBoardMYR());
    }

    public function testAddPreyNotYetAdded() : void
    {
        // GIVEN

        $prey = new PreyMYR();

        // WHEN

        $this->mainBoardMYR->addPrey($prey);

        // THEN

        $this->assertContains($prey, $this->mainBoardMYR->getPreys());
        $this->assertSame($this->mainBoardMYR, $prey->getMainBoardMYR());
    }

    public function testRemovePreyNotYetRemoved() : void
    {
        // GIVEN

        $prey = new PreyMYR();
        $this->mainBoardMYR->addPrey($prey);

        // WHEN

        $this->mainBoardMYR->removePrey($prey);

        // THEN

        $this->assertNotContains($prey, $this->mainBoardMYR->getPreys());
        $this->assertNull($prey->getMainBoardMYR());
    }

    public function testAddTileNotYetAdded() : void
    {
        // GIVEN

        $tile = new TileMYR();

        // WHEN

        $this->mainBoardMYR->addTile($tile);

        // THEN

        $this->assertContains($tile, $this->mainBoardMYR->getTiles());
    }

    public function testRemoveTile() : void
    {
        // GIVEN

        $tile = new TileMYR();
        $this->mainBoardMYR->addTile($tile);

        // WHEN

        $this->mainBoardMYR->removeTile($tile);

        // THEN

        $this->assertNotContains($tile, $this->mainBoardMYR->getTiles());
    }

    public function testAddAnthillHoleNotYetAdded() : void
    {
        // GIVEN

        $anthillHole = new AnthillHoleMYR();

        // WHEN

        $this->mainBoardMYR->addAnthillHole($anthillHole);

        // THEN

        $this->assertContains($anthillHole, $this->mainBoardMYR->getAnthillHoles());
        $this->assertSame($this->mainBoardMYR, $anthillHole->getMainBoardMYR());
    }

    public function testRemoveAnthillHoleNotYetRemoved() : void
    {
        // GIVEN

        $anthillHole = new AnthillHoleMYR();
        $this->mainBoardMYR->addAnthillHole($anthillHole);

        // WHEN

        $this->mainBoardMYR->removeAnthillHole($anthillHole);

        // THEN

        $this->assertNotContains($anthillHole, $this->mainBoardMYR->getAnthillHoles());
        $this->assertNull($anthillHole->getMainBoardMYR());
    }

    public function testAddSeasonNotYetAdded() : void
    {
        // GIVEN

        $season = new SeasonMYR();

        // WHEN

        $this->mainBoardMYR->addSeason($season);

        // THEN

        $this->assertContains($season, $this->mainBoardMYR->getSeasons());
        $this->assertSame($this->mainBoardMYR, $season->getMainBoard());
    }

    public function testRemoveSeasonNotYetRemoved() : void
    {
        // GIVEN

        $season = new SeasonMYR();
        $this->mainBoardMYR->addSeason($season);

        // WHEN

        $this->mainBoardMYR->removeSeason($season);

        // THEN

        $this->assertNotContains($season, $this->mainBoardMYR->getSeasons());
        $this->assertNull($season->getMainBoard());
    }

    public function testAddGameGoalsLevelOne() : void
    {
        // GIVEN

        $gameGoal = new GameGoalMYR();

        // WHEN

        $this->mainBoardMYR->addGameGoalsLevelOne($gameGoal);

        // THEN

        $this->assertContains($gameGoal, $this->mainBoardMYR->getGameGoalsLevelOne());
        $this->assertSame($this->mainBoardMYR, $gameGoal->getMainBoardLevelOne());
    }

    public function testRemoveGameGoalLevelOneYetRemoved() : void
    {
        // GIVEN

        $gameGoal = new GameGoalMYR();
        $this->mainBoardMYR->addGameGoalsLevelOne($gameGoal);

        // WHEN

        $this->mainBoardMYR->removeGameGoalsLevelOne($gameGoal);

        // THEN

        $this->assertNotContains($gameGoal, $this->mainBoardMYR->getGameGoalsLevelOne());
        $this->assertNull($gameGoal->getMainBoardLevelOne());
    }

    public function testAddGameGoalsLevelTwo() : void
    {
        // GIVEN

        $gameGoal = new GameGoalMYR();

        // WHEN

        $this->mainBoardMYR->addGameGoalsLevelTwo($gameGoal);

        // THEN

        $this->assertContains($gameGoal, $this->mainBoardMYR->getGameGoalsLevelTwo());
        $this->assertSame($this->mainBoardMYR, $gameGoal->getMainBoardLevelTwo());
    }

    public function testRemoveGameGoalLevelTwoYetRemoved() : void
    {
        // GIVEN

        $gameGoal = new GameGoalMYR();
        $this->mainBoardMYR->addGameGoalsLevelTwo($gameGoal);

        // WHEN

        $this->mainBoardMYR->removeGameGoalsLevelTwo($gameGoal);

        // THEN

        $this->assertNotContains($gameGoal, $this->mainBoardMYR->getGameGoalsLevelTwo());
        $this->assertNull($gameGoal->getMainBoardLevelTwo());
    }

    public function testAddGameGoalsLevelThree() : void
    {
        // GIVEN

        $gameGoal = new GameGoalMYR();

        // WHEN

        $this->mainBoardMYR->addGameGoalsLevelThree($gameGoal);

        // THEN

        $this->assertContains($gameGoal, $this->mainBoardMYR->getGameGoalsLevelThree());
        $this->assertSame($this->mainBoardMYR, $gameGoal->getMainBoardLevelThree());
    }

    public function testRemoveGameGoalLevelThreeYetRemoved() : void
    {
        // GIVEN

        $gameGoal = new GameGoalMYR();
        $this->mainBoardMYR->addGameGoalsLevelThree($gameGoal);

        // WHEN

        $this->mainBoardMYR->removeGameGoalsLevelThree($gameGoal);

        // THEN

        $this->assertNotContains($gameGoal, $this->mainBoardMYR->getGameGoalsLevelThree());
        $this->assertNull($gameGoal->getMainBoardLevelThree());
    }

    protected function setUp(): void
    {
        $this->mainBoardMYR = new MainBoardMYR();
    }
}