<?php

namespace App\Tests\Game\Factory\Unit\DTO;


use App\Entity\Game\DTO\Pawn;
use PHPUnit\Framework\TestCase;

class PawnTest extends TestCase
{

    private Pawn $pawn;
    public function testSetColor(): void
    {
        //GIVEN
        $color = "red";
        //WHEN
        $this->pawn->setColor($color);
        //THEN
        $this->assertSame($color, $this->pawn->getColor());
    }
    protected function setUp(): void
    {
        $this->pawn = new Pawn();
    }
}