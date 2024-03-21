<?php

namespace App\Tests\Game\Myrmes\Unit;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
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

    public function testSetActualSeason() : void
    {
        // GIVEN

        $season = new SeasonMYR();

        // WHEN

        $this->mainBoardMYR->setActualSeason($season);

        // THEN

        $this->assertSame($season, $this->mainBoardMYR->getActualSeason());
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

    protected function setUp(): void
    {
        $this->mainBoardMYR = new MainBoardMYR();
    }
}