<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use PHPUnit\Framework\TestCase;

class NurseMYRTest extends TestCase
{
    private NurseMYR $nurseMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->nurseMYR->getId() >= 0);
    }

    public function testSetPosition() : void
    {
        // GIVEN

        $position = 7;

        // WHEN

        $this->nurseMYR->setPosition($position);

        // THEN

        $this->assertSame($position, $this->nurseMYR->getPosition());
    }

    public function testSetPlayer() : void
    {
        // GIVEN

        $player = new PlayerMYR("user", new GameMYR());

        // WHEN

        $this->nurseMYR->setPlayer($player);

        // THEN

        $this->assertSame($player, $this->nurseMYR->getPlayer());
    }

    public function testSetAvailable() : void
    {
        // GIVEN

        $available = true;

        // WHEN

        $this->nurseMYR->setAvailable($available);

        // THEN

        $this->assertTrue($this->nurseMYR->isAvailable());
    }

    public function testSetPersonalBoard() : void
    {
        // GIVEN

        $personalBoard = new PersonalBoardMYR();

        // WHEN

        $this->nurseMYR->setPersonalBoardMYR($personalBoard);

        // THEN

        $this->assertSame($personalBoard, $this->nurseMYR->getPersonalBoardMYR());
    }

    public function testSetArea() : void
    {
        // GIVEN

        $area = MyrmesParameters::WORKER_AREA;

        // WHEN

        $this->nurseMYR->setArea($area);

        // THEN

        $this->assertSame($area, $this->nurseMYR->getArea());
    }

    protected function setUp(): void
    {
        $this->nurseMYR = new NurseMYR();
    }

}