<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\TileMYR;
use PHPUnit\Framework\TestCase;

class PreyMYRTest extends TestCase
{
    private PreyMYR $preyMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->preyMYR->getId() >= 0);
    }

    public function testSetType() : void
    {
        // GIVEN

        $type = "insect";

        // WHEN

        $this->preyMYR->setType($type);

        // THEN

        $this->assertSame($type, $this->preyMYR->getType());
    }

    public function testSetTile() : void
    {
        // GIVEN

        $tile = new TileMYR();

        // WHEN

        $this->preyMYR->setTile($tile);

        // THEN

        $this->assertSame($tile, $this->preyMYR->getTile());
    }

    public function testSetPlayer() : void
    {
        // GIVEN

        $player = new PlayerMYR("user", new GameMYR());

        // WHEN

        $this->preyMYR->setPlayer($player);

        // THEN

        $this->assertSame($player, $this->preyMYR->getPlayer());
    }

    public function testSetMainBoard() : void
    {
        // GIVEN

        $mainBoard = new MainBoardMYR();

        // WHEN

        $this->preyMYR->setMainBoardMYR($mainBoard);

        // THEN

        $this->assertSame($mainBoard, $this->preyMYR->getMainBoardMYR());
    }

    protected function setUp(): void
    {
        $this->preyMYR = new PreyMYR();
    }
}