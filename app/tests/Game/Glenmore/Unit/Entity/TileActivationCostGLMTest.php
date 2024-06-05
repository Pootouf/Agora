<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\TileActivationCostGLM;
use PHPUnit\Framework\TestCase;

class TileActivationCostGLMTest extends TestCase
{
    private TileActivationCostGLM $tileActivationCostGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->tileActivationCostGLM->getId() >= 0);
    }

    public function testSetResource() : void
    {
        // GIVEN

        $resource = new ResourceGLM();

        // WHEN

        $this->tileActivationCostGLM->setResource($resource);

        // THEN

        $this->assertSame($resource, $this->tileActivationCostGLM->getResource());
    }

    public function testSetPrice() : void
    {
        // GIVEN

        $price = 5;

        // WHEN

        $this->tileActivationCostGLM->setPrice($price);

        // THEN

        $this->assertSame($price, $this->tileActivationCostGLM->getPrice());
    }

    protected function setUp(): void
    {
        $this->tileActivationCostGLM = new TileActivationCostGLM();
    }
}