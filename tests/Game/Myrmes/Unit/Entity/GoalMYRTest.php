<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\GoalMYR;
use PHPUnit\Framework\TestCase;

class GoalMYRTest extends TestCase
{
    private GoalMYR $goalMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->goalMYR->getId() >= 0);
    }

    public function testSetName() : void
    {
        // GIVEN

        $name = "goal";

        // WHEN

        $this->goalMYR->setName($name);

        // THEN

        $this->assertSame($name, $this->goalMYR->getName());
    }

    public function testSetDifficulty() : void
    {
        // GIVEN

        $difficulty = 100;

        // WHEN

        $this->goalMYR->setDifficulty($difficulty);

        // THEN

        $this->assertSame($difficulty, $this->goalMYR->getDifficulty());
    }

    protected function setUp(): void
    {
        $this->goalMYR = new GoalMYR();
    }
}