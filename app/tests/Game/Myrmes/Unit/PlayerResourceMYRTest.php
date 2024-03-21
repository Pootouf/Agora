<?php

namespace App\Tests\Game\Myrmes\Unit;

use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use PHPUnit\Framework\TestCase;

class PlayerResourceMYRTest extends TestCase
{
    private PlayerResourceMYR $playerResourceMYR;

    public function testSetUp() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN

        $this->assertTrue($this->playerResourceMYR->getId() >= 0);
    }

    public function testSetQuantity() : void
    {
        // GIVEN

        $quantity = 18;

        // WHEN

        $this->playerResourceMYR->setQuantity($quantity);

        // THEN

        $this->assertSame($quantity, $this->playerResourceMYR->getQuantity());
    }

    public function testSetResource() : void
    {
        // GIVEN

        $resource = new ResourceMYR();

        // WHEN

        $this->playerResourceMYR->setResource($resource);

        // THEN

        $this->assertSame($resource, $this->playerResourceMYR->getResource());
    }

    public function testSetPersonalBoard() : void
    {
        // GIVEN

        $personalBoard = new PersonalBoardMYR();

        // WHEN

        $this->playerResourceMYR->setPersonalBoard($personalBoard);

        // THEN

        $this->assertSame($personalBoard, $this->playerResourceMYR->getPersonalBoard());
    }

    protected function setUp(): void
    {
        $this->playerResourceMYR = new PlayerResourceMYR();
    }
}