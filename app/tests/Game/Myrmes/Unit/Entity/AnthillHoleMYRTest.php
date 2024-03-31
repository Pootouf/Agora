<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\TileMYR;
use PHPUnit\Framework\TestCase;

class AnthillHoleMYRTest extends TestCase
{
    private AnthillHoleMYR $anthillHoleMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->anthillHoleMYR->getId() >= 0);
    }

    public function testSetTile() : void
    {
        // GIVEN

        $tile = new TileMYR();

        // WHEN

        $this->anthillHoleMYR->setTile($tile);

        // THEN

        $this->assertSame($tile, $this->anthillHoleMYR->getTile());
    }

    public function testSetPlayer() : void
    {
        // GIVEN

        $player = new PlayerMYR("user", new GameMYR());

        // WHEN

        $this->anthillHoleMYR->setPlayer($player);

        // THEN

        $this->assertSame($player, $this->anthillHoleMYR->getPlayer());
    }

    public function testSetMainBoard() : void
    {
        // GIVEN

        $mainBoard = new MainBoardMYR();

        // WHEN

        $this->anthillHoleMYR->setMainBoardMYR($mainBoard);

        // THEN

        $this->assertSame($mainBoard, $this->anthillHoleMYR->getMainBoardMYR());
    }

    protected function setUp(): void
    {
        $this->anthillHoleMYR = new AnthillHoleMYR();
    }
}