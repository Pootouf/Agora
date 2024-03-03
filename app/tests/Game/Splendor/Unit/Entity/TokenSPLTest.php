<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\DTO\Token;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Entity\Game\Splendor\TokenSPL;
use PHPUnit\Framework\TestCase;

class TokenSPLTest extends TestCase
{
    private TokenSPL $tokenSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertTrue($this->tokenSPL->getId() >= 0);
    }

    public function testSetColor() : void
    {
        //GIVEN
        $color = SplendorParameters::$COLOR_BLACK;
        //WHEN
        $this->tokenSPL->setColor($color);
        //THEN
        $this->assertSame($color, $this->tokenSPL->getColor());
    }

    public function testSetType() : void
    {
        //GIVEN
        $type = "Ruby";
        //WHEN
        $this->tokenSPL->setType($type);
        //THEN
        $this->assertSame($type, $this->tokenSPL->getType());
    }

    protected function setUp(): void
    {
        $this->tokenSPL = new TokenSPL();
    }
}
