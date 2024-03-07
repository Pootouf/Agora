<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\TileBuyCostGLM;
use PHPUnit\Framework\TestCase;

class TileBuyCostGLMTest extends TestCase
{
    private TileBuyCostGLM $tileBuyCostGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->tileBuyCostGLM->getId() >= 0);
    }

    public function testSetResource() : void
    {
        // GIVEN

        $resource = new ResourceGLM();

        // WHEN

        $this->tileBuyCostGLM->setResource($resource);

        // THEN

        $this->assertSame($resource, $this->tileBuyCostGLM->getResource());
    }

    public function testSetPrice() : void
    {
        // GIVEN

        $price = 3;

        // WHEN

        $this->tileBuyCostGLM->setPrice($price);

        // THEN

        $this->assertSame($price, $this->tileBuyCostGLM->getPrice());
    }

    protected function setUp(): void
    {
        $this->tileBuyCostGLM = new TileBuyCostGLM();
    }
}