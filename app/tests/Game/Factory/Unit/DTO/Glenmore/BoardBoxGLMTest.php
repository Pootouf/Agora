<?php

namespace App\Tests\Game\Factory\Unit\DTO\Glenmore;


use App\Entity\Game\DTO\Glenmore\BoardBoxGLM;
use App\Entity\Game\DTO\Pawn;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\TileGLM;
use phpDocumentor\Reflection\Types\Nullable;
use PHPUnit\Framework\TestCase;

class BoardBoxGLMTest extends TestCase
{

    private BoardBoxGLM $boardBoxGLM;
    public function testConstructWithNothing(): void
    {
        //GIVEN
        $pawn = null;
        $tile = null;
        //WHEN
        $boardBoxGLM = new BoardBoxGLM($tile, $pawn);
        //THEN
        $this->assertFalse($boardBoxGLM->hasTile());
        $this->assertFalse($boardBoxGLM->hasPawn());
    }

    public function testConstructWithPawn(): void
    {
        //GIVEN
        $pawn = new PawnGLM();
        $tile = null;
        //WHEN
        $boardBoxGLM = new BoardBoxGLM($tile, $pawn);
        //THEN
        $this->assertFalse($boardBoxGLM->hasTile());
        $this->assertTrue($boardBoxGLM->hasPawn());
        $this->assertSame($pawn, $boardBoxGLM->getPawn());
    }

    /*public function testConstructWithTile(): void
    {
        //GIVEN
        $pawn = null;
        $tile = new TileGLM();
        //WHEN
        $boardBoxGLM = new BoardBoxGLM($tile, $pawn);
        //THEN
        $this->assertTrue($boardBoxGLM->hasTile());
        $this->assertFalse($boardBoxGLM->hasPawn());
        $this->assertSame($tile, $boardBoxGLM->getTile());
    }*/

    /*public function testConstructWithBoth(): void
    {
        //GIVEN
        $pawn = new PawnGLM();
        $tile = new TileGLM();

        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $boardBoxGLM = new BoardBoxGLM($tile, $pawn);
    }*/

   /* public function testIsEmptyBoxWithPawn(): void
    {
        //GIVEN
        $pawn = new PawnGLM();
        $tile = null;
        //WHEN
        $boardBoxGLM = new BoardBoxGLM($tile, $pawn);
        //THEN
        $this->assertFalse($boardBoxGLM->isEmptyBox());
    }

    public function testIsEmptyBoxWithTile(): void
    {
        //GIVEN
        $pawn = null;
        $tile = new TileGLM();
        //WHEN
        $boardBoxGLM = new BoardBoxGLM($tile, $pawn);
        //THEN
        $this->assertFalse($boardBoxGLM->isEmptyBox());
    }

    public function testIsEmptyBoxWithNothing(): void
    {
        //GIVEN
        $pawn = null;
        $tile = null;
        //WHEN
        $boardBoxGLM = new BoardBoxGLM($tile, $pawn);
        //THEN
        $this->assertTrue($boardBoxGLM->isEmptyBox());
    }*/
}