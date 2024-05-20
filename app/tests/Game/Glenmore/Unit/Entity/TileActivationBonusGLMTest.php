<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\TileActivationBonusGLM;
use PHPUnit\Framework\TestCase;

class TileActivationBonusGLMTest extends TestCase
{
    private TileActivationBonusGLM $tileActivationBonusGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->tileActivationBonusGLM->getId() >= 0);
    }

    public function testSetResource() : void
    {
        // GIVEN

        $resource = new ResourceGLM();

        // WHEN

        $this->tileActivationBonusGLM->setResource($resource);

        // THEN

        $this->assertSame($this->tileActivationBonusGLM->getResource(), $resource);
    }

    public function testSetAmount() : void
    {
        // GIVEN

        $amount = 9;

        // WHEN

        $this->tileActivationBonusGLM->setAmount($amount);

        // THEN

        $this->assertSame($amount, $this->tileActivationBonusGLM->getAmount());
    }

    protected function setUp(): void
    {
        $this->tileActivationBonusGLM = new TileActivationBonusGLM();
    }
}