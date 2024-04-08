<?php

namespace App\Tests\Game\Factory\Unit\DTO\Myrmes;

use App\Entity\Game\DTO\Myrmes\BoardTileMYR;
use App\Entity\Game\Myrmes\TileMYR;
use PHPUnit\Framework\TestCase;

class BoardTileMYRTest extends TestCase
{

    public function testInitShouldSucceed() : void
    {
        // GIVEN
        $tile = new TileMYR();
        $isPivot = true;
        //WHEN
        $boardTile = new BoardTileMYR($tile, $isPivot);

        //THEN
        $this->assertTrue($boardTile->isPivot());
        $this->assertSame($tile, $boardTile->getTile());
    }

    public function testInitShouldFail() : void
    {
        // GIVEN
        $tile = null;
        $isPivot = true;
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        new BoardTileMYR($tile, $isPivot);
    }


}