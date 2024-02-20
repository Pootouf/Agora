<?php

namespace App\Tests\Game\Factory\Unit\DTO;


use App\Entity\Game\DTO\Tile;
use PHPUnit\Framework\TestCase;

class TileTest extends TestCase
{

    private Tile $tile;

    public function testSetType(): void
    {
        //GIVEN
        $type = "type";
        //WHEN
        $this->tile->setType($type);
        //THEN
        $this->assertSame($type, $this->tile->getType());
    }

    protected function setUp(): void
    {
        $this->tile = new Tile();
    }
}