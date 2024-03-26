<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\CardGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerCardGLM;
use PHPUnit\Framework\TestCase;

class PlayerCardGLMTest extends TestCase
{
    private PlayerCardGLM $playerCardGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->playerCardGLM->getId() >= 0);
        $this->assertNotNull($this->playerCardGLM->getPersonalBoard());
        $this->assertNotNull($this->playerCardGLM->getCard());
    }

    public function testSetPersonalBoardWhenIsNotNull() : void
    {
        // GIVEN

        $personalBoard = new PersonalBoardGLM();

        // WHEN

        $this->playerCardGLM->setPersonalBoard($personalBoard);

        // THEN

        $this->assertSame($personalBoard, $this->playerCardGLM->getPersonalBoard());
    }

    public function testSetPersonalBoardWhenIsNull() : void
    {
        // WHEN

        $this->playerCardGLM->setPersonalBoard(null);

        // THEN
        $this->assertNull($this->playerCardGLM->getPersonalBoard());
    }

    public function testSetCardWhenIsNotNull() : void
    {
        // GIVEN

        $card = new CardGLM();

        // WHEN

        $this->playerCardGLM->setCard($card);

        // THEN

        $this->assertSame($card, $this->playerCardGLM->getCard());
    }

    public function testSetCardWhenIsNull() : void
    {
        // WHEN

        $this->playerCardGLM->setCard(null);

        // THEN

        $this->assertNull($this->playerCardGLM->getCard());
    }

    protected function setUp(): void
    {
        $this->playerCardGLM = new PlayerCardGLM(new PersonalBoardGLM(), new CardGLM());
    }

}