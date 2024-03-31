<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\SeasonMYR;
use PHPUnit\Framework\TestCase;

class SeasonMYRTest extends TestCase
{
    private SeasonMYR $seasonMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->seasonMYR->getId() >= 0);
    }

    public function testSetDiceResult() : void
    {
        // GIVEN

        $diceResult = 4;

        // WHEN

        $this->seasonMYR->setDiceResult($diceResult);

        // THEN

        $this->assertSame($diceResult, $this->seasonMYR->getDiceResult());
    }

    public function testSetMainBoardWhenCurrentSeasonOfMainBoardIsDifferent() : void
    {
        // GIVEN

        $mainBoard = new MainBoardMYR();

        // WHEN

        $this->seasonMYR->setMainBoard($mainBoard);

        // THEN

        $this->assertSame($mainBoard, $this->seasonMYR->getMainBoard());
    }

    public function testSetName() : void
    {
        // GIVEN

        $name = MyrmesParameters::$SUMMER_SEASON_NAME;

        // WHEN

        $this->seasonMYR->setName($name);

        // THEN

        $this->assertSame($name, $this->seasonMYR->getName());
    }

    protected function setUp(): void
    {
        $this->seasonMYR = new SeasonMYR();
    }
}