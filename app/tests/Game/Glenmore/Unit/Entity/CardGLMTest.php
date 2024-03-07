<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\CardGLM;
use App\Entity\Game\Glenmore\TileBuyBonusGLM;
use PHPUnit\Framework\TestCase;

class CardGLMTest extends TestCase
{
    private CardGLM $cardGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->cardGLM->getId() >= 0);
    }

    public function testSetName() : void
    {
        // GIVEN

        $name = "card";

        // WHEN

        $this->cardGLM->setName($name);

        // THEN

        $this->assertSame($name, $this->cardGLM->getName());
    }

    public function testSetBonus() : void
    {
        // GIVEN

        $bonus = new TileBuyBonusGLM();

        // WHEN

        $this->cardGLM->setBonus($bonus);

        // THEN

        $this->assertSame($bonus, $this->cardGLM->getBonus());
    }

    protected function setUp() : void
    {
        $this->cardGLM = new CardGLM();
    }
}