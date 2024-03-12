<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\CreatedResourceGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use PHPUnit\Framework\TestCase;

class CreatedResourceGLMTest extends TestCase
{
    private CreatedResourceGLM $createdResourceGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->createdResourceGLM->getId() >= 0);
    }

    public function testSetResource() : void
    {
        // GIVEN

        $resource = new ResourceGLM();

        // WHEN

        $this->createdResourceGLM->setResource($resource);

        // THEN

        $this->assertSame($resource, $this->createdResourceGLM->getResource());
    }

    public function testSetQuantity() : void
    {
        // GIVEN

        $quantity = 5;

        // WHEN

        $this->createdResourceGLM->setQuantity($quantity);

        // THEN

        $this->assertSame($quantity, $this->createdResourceGLM->getQuantity());
    }

    public function testSetPersonalBoard() : void
    {
        // GIVEN

        $personal = new PersonalBoardGLM();

        // WHEN

        $this->createdResourceGLM->setPersonalBoardGLM($personal);

        // THEN

        $this->assertSame($personal, $this->createdResourceGLM->getPersonalBoardGLM());
    }

    protected function setUp(): void
    {
        $this->createdResourceGLM = new CreatedResourceGLM();
    }
}