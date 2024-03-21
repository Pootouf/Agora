<?php

namespace App\Tests\Game\Myrmes\Unit;

use App\Entity\Game\Myrmes\ResourceMYR;
use PHPUnit\Framework\TestCase;

class ResourceMYRTest extends TestCase
{
    private ResourceMYR $resourceMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->resourceMYR->getId() >= 0);
    }

    public function testSetDescription() : void
    {
        // GIVEN

        $desc = "description";

        // WHEN

        $this->resourceMYR->setDescription($desc);

        // THEN

        $this->assertSame($desc, $this->resourceMYR->getDescription());
    }

    protected function setUp(): void
    {
        $this->resourceMYR = new ResourceMYR();
    }
}