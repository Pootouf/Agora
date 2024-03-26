<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\TileBuyBonusGLM;
use PHPUnit\Framework\TestCase;

class TileBuyBonusGLMTest extends TestCase
{

    private TileBuyBonusGLM $tileBuyBonusGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->tileBuyBonusGLM->getId() >= 0);
    }

    public function testSetResource() : void
    {
        // GIVEN

        $resource = new ResourceGLM();

        // WHEN

        $this->tileBuyBonusGLM->setResource($resource);

        // THEN

        $this->assertSame($resource, $this->tileBuyBonusGLM->getResource());
    }

    public function testSetAmount() : void
    {
        // GIVEN

        $amount = 4;

        // WHEN

        $this->tileBuyBonusGLM->setAmount($amount);

        // THEN

        $this->assertSame($amount, $this->tileBuyBonusGLM->getAmount());
    }

    protected function setUp(): void
    {
        $this->tileBuyBonusGLM = new TileBuyBonusGLM();
    }
}