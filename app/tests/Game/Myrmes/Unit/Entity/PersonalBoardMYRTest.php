<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use PHPUnit\Framework\TestCase;

class PersonalBoardMYRTest extends TestCase
{
    private PersonalBoardMYR $personalBoardMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertNotNull($this->personalBoardMYR->getNurses());
        $this->assertNotNull($this->personalBoardMYR->getAnthillWorkers());
        $this->assertNotNull($this->personalBoardMYR->getPlayerResourceMYRs());
    }

    public function testSetPlayer() : void
    {
        // GIVEN

        $player = new PlayerMYR("user", new GameMYR());

        // WHEN

        $this->personalBoardMYR->setPlayer($player);

        // THEN

        $this->assertSame($player, $this->personalBoardMYR->getPlayer());
    }

    public function testSetAnthillLevel() : void
    {
        // GIVEN

        $anthillLevel = 8;

        // WHEN

        $this->personalBoardMYR->setAnthillLevel($anthillLevel);

        // THEN

        $this->assertSame($anthillLevel, $this->personalBoardMYR->getAnthillLevel());
    }

    public function testSetLarvaCount() : void
    {
        // GIVEN

        $larvaCount = 5;

        // WHEN

        $this->personalBoardMYR->setLarvaCount($larvaCount);

        // THEN

        $this->assertSame($larvaCount, $this->personalBoardMYR->getLarvaCount());
    }

    public function testAddNurseNotYetAdded() : void
    {
        // GIVEN

        $nurse = new NurseMYR();

        // WHEN

        $this->personalBoardMYR->addNurse($nurse);

        // THEN

        $this->assertContains($nurse, $this->personalBoardMYR->getNurses());
        $this->assertSame($this->personalBoardMYR, $nurse->getPersonalBoardMYR());
    }

    public function testRemoveNurseNotYetRemoved() : void
    {
        // GIVEN

        $nurse = new NurseMYR();
        $this->personalBoardMYR->addNurse($nurse);

        // WHEN

        $this->personalBoardMYR->removeNurse($nurse);

        // THEN

        $this->assertNull($nurse->getPersonalBoardMYR());
        $this->assertNotContains($nurse, $this->personalBoardMYR->getNurses());
    }

    public function testSetWarriorCount() : void
    {
        // GIVEN

        $warriorsCount = 5;

        // WHEN

        $this->personalBoardMYR->setWarriorsCount($warriorsCount);

        // THEN

        $this->assertSame($warriorsCount, $this->personalBoardMYR->getWarriorsCount());
    }

    public function testAddAnthillWorkerNotYetAdded() : void
    {
        // GIVEN

        $anthillWorker = new AnthillWorkerMYR();

        // WHEN

        $this->personalBoardMYR->addAnthillWorker($anthillWorker);

        // THEN

        $this->assertContains($anthillWorker, $this->personalBoardMYR->getAnthillWorkers());
        $this->assertSame($this->personalBoardMYR, $anthillWorker->getPersonalBoardMYR());
    }

    public function testRemoveAnthillWorkerNotYetRemoved() : void
    {
        // GIVEN

        $anthillWorker = new AnthillWorkerMYR();
        $this->personalBoardMYR->addAnthillWorker($anthillWorker);

        // WHEN

        $this->personalBoardMYR->removeAnthillWorker($anthillWorker);

        // THEN

        $this->assertNotContains($anthillWorker, $this->personalBoardMYR->getAnthillWorkers());
        $this->assertNull($anthillWorker->getPersonalBoardMYR());
    }

    public function testSetBonus() : void
    {
        // GIVEN

        $bonus = 4;

        // WHEN

        $this->personalBoardMYR->setBonus($bonus);

        // THEN

        $this->assertSame($bonus, $this->personalBoardMYR->getBonus());
    }

    public function testAddPlayerResourceYetNotAdded() : void
    {
        // GIVEN

        $resource = new PlayerResourceMYR();

        // WHEN

        $this->personalBoardMYR->addPlayerResourceMYR($resource);

        // THEN

        $this->assertContains($resource, $this->personalBoardMYR->getPlayerResourceMYRs());
        $this->assertSame($this->personalBoardMYR, $resource->getPersonalBoard());
    }

    public function testRemovePlayerResourceYetNotRemoved() : void
    {
        // GIVEN

        $resource = new PlayerResourceMYR();
        $this->personalBoardMYR->addPlayerResourceMYR($resource);

        // WHEN

        $this->personalBoardMYR->removePlayerResourceMYR($resource);

        // THEN

        $this->assertNotContains($resource, $this->personalBoardMYR->getPlayerResourceMYRs());
        $this->assertNull($resource->getPersonalBoard());
    }

    protected function setUp(): void
    {
        $this->personalBoardMYR = new PersonalBoardMYR();
    }
}