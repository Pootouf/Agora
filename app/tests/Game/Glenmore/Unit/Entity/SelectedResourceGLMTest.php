<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\SelectedResourceGLM;
use PHPUnit\Framework\TestCase;

class SelectedResourceGLMTest extends TestCase
{
    private SelectedResourceGLM $selectedResourceGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->selectedResourceGLM->getId() >= 0);
    }

    public function testSetResource() : void
    {
        // GIVEN

        $resource = new ResourceGLM();

        // WHEN

        $this->selectedResourceGLM->setResource($resource);

        // THEN

        $this->assertSame($resource, $this->selectedResourceGLM->getResource());
    }

    public function testSetQuantity() : void
    {
        // GIVEN

        $quantity = 3;

        // WHEN

        $this->selectedResourceGLM->setQuantity($quantity);

        // THEN

        $this->assertSame($quantity, $this->selectedResourceGLM->getQuantity());
    }

    public function testSetPersonalBoard() : void
    {
        // GIVEN

        $personal = new PersonalBoardGLM();

        // WHEN

        $this->selectedResourceGLM->setPersonalBoardGLM($personal);

        // THEN

        $this->assertSame($personal, $this->selectedResourceGLM->getPersonalBoardGLM());
    }

    protected function setUp(): void
    {
        $this->selectedResourceGLM = new SelectedResourceGLM();
    }
}