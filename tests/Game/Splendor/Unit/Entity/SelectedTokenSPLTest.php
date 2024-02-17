<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\DTO\Token;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\TokenSPL;
use PHPUnit\Framework\TestCase;

class SelectedTokenSPLTest extends TestCase
{
    private SelectedTokenSPL $selectedTokenSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertTrue($this->selectedTokenSPL->getId() >= 0);
    }

    public function testSetToken(): void
    {
        //GIVEN
        $token = new TokenSPL();
        //WHEN
        $this->selectedTokenSPL->setToken($token);
        //THEN
        $this->assertSame($token, $this->selectedTokenSPL->getToken());
    }

    public function testSetPersonalBoard(): void
    {
        //GIVEN
        $personalBoard = new PersonalBoardSPL();
        //WHEN
        $this->selectedTokenSPL->setPersonalBoardSPL($personalBoard);
        //THEN
        $this->assertSame($personalBoard, $this->selectedTokenSPL->getPersonalBoardSPL());
    }

    protected function setUp(): void
    {
        $this->selectedTokenSPL = new SelectedTokenSPL();
    }
}
