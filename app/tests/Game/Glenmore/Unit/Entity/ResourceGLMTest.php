<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\ResourceGLM;
use PHPUnit\Framework\TestCase;

class ResourceGLMTest extends TestCase
{

    private ResourceGLM $resourceGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->resourceGLM->getId() >= 0);
    }

    public function testSetType()
    {
        // GIVEN

        $type = GlenmoreParameters::HAT_RESOURCE;

        // WHEN

        $this->resourceGLM->setType($type);

        // THEN

        $this->assertSame($type, $this->resourceGLM->getType());
    }

    public function testSetColor() : void
    {
        // GIVEN

        $color = GlenmoreParameters::COLOR_RED;

        // WHEN

        $this->resourceGLM->setColor($color);

        // THEN

        $this->assertSame($color, $this->resourceGLM->getColor());
    }

    protected function setUp() : void
    {
        $this->resourceGLM = new ResourceGLM();
    }
}