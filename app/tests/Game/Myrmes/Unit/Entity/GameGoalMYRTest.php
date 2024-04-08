<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GoalMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use PHPUnit\Framework\TestCase;

class GameGoalMYRTest extends TestCase
{
    private GameGoalMYR $gameGoalMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->gameGoalMYR->getId() >= 0);
        $this->assertNotNull($this->gameGoalMYR->getPrecedentsPlayers());
    }

    public function testAddPrecedentPlayerNotYetAdded() : void
    {
        // GIVEN

        $player = new PlayerMYR("user", new GameMYR());

        // WHEN

        $this->gameGoalMYR->addPrecedentsPlayer($player);

        // THEN

        $this->assertContains($player, $this->gameGoalMYR->getPrecedentsPlayers());
    }

    public function testRemovePrecedentPlayer() : void
    {
        // GIVEN

        $player = new PlayerMYR("user", new GameMYR());

        // WHEN

        $this->gameGoalMYR->removePrecedentsPlayer($player);

        // THEN

        $this->assertNotContains($player, $this->gameGoalMYR->getPrecedentsPlayers());
    }

    public function testSetGoal() : void
    {
        // GIVEN

        $goal = new GoalMYR();

        // WHEN

        $this->gameGoalMYR->setGoal($goal);

        // THEN

        $this->assertSame($goal, $this->gameGoalMYR->getGoal());
    }

    public function testSetMainBoardLevelOne() : void
    {
        //GIVEN
        $mainBoardOne = new MainBoardMYR();
        //WHEN
        $this->gameGoalMYR->setMainBoardLevelOne($mainBoardOne);
        //THEN
        $this->assertSame($mainBoardOne, $this->gameGoalMYR->getMainBoardLevelOne());
    }

    public function testSetMainBoardLevelTwo() : void
    {
        //GIVEN
        $mainBoardTwo = new MainBoardMYR();
        //WHEN
        $this->gameGoalMYR->setMainBoardLevelTwo($mainBoardTwo);
        //THEN
        $this->assertSame($mainBoardTwo, $this->gameGoalMYR->getMainBoardLevelTwo());
    }

    public function testSetMainBoardLevelThree() : void
    {
        //GIVEN
        $mainBoardThree = new MainBoardMYR();
        //WHEN
        $this->gameGoalMYR->setMainBoardLevelThree($mainBoardThree);
        //THEN
        $this->assertSame($mainBoardThree, $this->gameGoalMYR->getMainBoardLevelThree());
    }

    protected function setUp(): void
    {
        $this->gameGoalMYR = new GameGoalMYR();
    }
}