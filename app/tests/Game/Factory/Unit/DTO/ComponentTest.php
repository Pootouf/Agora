<?php

namespace App\Tests\Game\Factory\Unit\DTO;

use App\Entity\Game\DTO\Card;
use App\Entity\Game\DTO\Component;
use App\Entity\Game\Help;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{

    private Component $component;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // setUp()
        //THEN
        $this->assertTrue($this->component->getId() >= 0);
    }
    public function testSetHelp(): void
    {
        //GIVEN
        $help = new Help();
        //WHEN
        $this->component->setHelp($help);
        //THEN
        $this->assertSame($help, $this->component->getHelp());
    }
    protected function setUp(): void
    {
        $this->component = new Component();
    }
}