<?php

namespace App\Tests\Game\Myrmes\Unit;

use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use PHPUnit\Framework\TestCase;

class AnthillWorkerMYRTest extends TestCase
{
    private AnthillWorkerMYR $anthillWorkerMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->anthillWorkerMYR->getId() >= 0);
    }

    public function testSetWorkFloor() : void
    {
        // GIVEN

        $workFloor = 5;

        // WHEN

        $this->anthillWorkerMYR->setWorkFloor($workFloor);

        // THEN

        $this->assertSame($workFloor, $this->anthillWorkerMYR->getWorkFloor());
    }

    public function testSetPlayer() : void
    {
        // GIVEN

        $player = new PlayerMYR("user", new GameMYR());

        // WHEN

        $this->anthillWorkerMYR->setPlayer($player);

        // THEN

        $this->assertSame($player, $this->anthillWorkerMYR->getPlayer());
    }

    public function testSetPersonalBoard() : void
    {
        // GIVEN

        $personalBoard = new PersonalBoardMYR();

        // WHEN

        $this->anthillWorkerMYR->setPersonalBoardMYR($personalBoard);

        // THEN

        $this->assertSame($personalBoard, $this->anthillWorkerMYR->getPersonalBoardMYR());
    }

    protected function setUp(): void
    {
        $this->anthillWorkerMYR = new AnthillWorkerMYR();
    }
}