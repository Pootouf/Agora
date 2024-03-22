<?php

namespace App\Tests\Game\Myrmes\Unit\Entity;

use App\Entity\Game\Myrmes\PreyMYR;
use PHPUnit\Framework\TestCase;

class PreyMYRTest extends TestCase
{
    private PreyMYR $preyMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->preyMYR->getId() >= 0);
    }

    public function testSetType() : void
    {
        // GIVEN

        $type = "insect";

        // WHEN

        $this->preyMYR->setType($type);

        // THEN

        $this->assertSame($type, $this->preyMYR->getType());
    }

    protected function setUp(): void
    {
        $this->preyMYR = new PreyMYR();
    }
}