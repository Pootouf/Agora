<?php

namespace App\Tests\Game\SixQP\Unit\Entity;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use PHPUnit\Framework\TestCase;

class RowSixQPTest extends TestCase
{
    private RowSixQP $rowSixQP;

    protected function setUp(): void
    {
        $this->rowSixQP = new RowSixQP();
        $this->rowSixQP->addCard(new CardSixQP());
    }

    public function testInit() : void
    {
        //GIVEN

        //WHEN
        $row = new RowSixQP();
        //THEN
        $this->assertEmpty($row->getCards());
    }
    public function testSetPosition() : void
    {
        //GIVEN
        $pos = 2;
        //WHEN
        $this->rowSixQP->setPosition($pos);
        //THEN
        $this->assertSame($pos, $this->rowSixQP->getPosition());
    }

    public function testSetGame() : void
    {
        //GIVEN
        $game = new GameSixQP();
        //WHEN
        $this->rowSixQP->setGame($game);
        //THEN
        $this->assertSame($game, $this->rowSixQP->getGame());
    }

    public function testAddCard() : void
    {
        //GIVEN
        $card = new CardSixQP();
        //WHEN
        $this->rowSixQP->addCard($card);
        //THEN
        $this->assertContains($card, $this->rowSixQP->getCards());
    }
    public function testIsCardInRowShouldReturnTrue() : void
    {
        //GIVEN
        $card = $this->rowSixQP->getCards()->first();
        //WHEN
        $result = $this->rowSixQP->isCardInRow($card);
        //THEN
        $this->assertTrue($result);
    }

    public function testIsCardInRowShouldReturnFalse() : void
    {
        //GIVEN
        $card = new CardSixQP();
        //WHEN
        $result = $this->rowSixQP->isCardInRow($card);
        //THEN
        $this->assertFalse($result);
    }
    public function testRemoveCard() : void
    {
        //GIVEN
        $card = $this->rowSixQP->getCards()->first();
        //WHEN
        $this->rowSixQP->removeCard($card);
        //THEN
        $this->assertNotContains($card, $this->rowSixQP->getCards());
    }

    public function testClearCards() : void
    {
        //GIVEN

        //WHEN
        $this->rowSixQP->clearCards();
        //THEN
        $this->assertEmpty($this->rowSixQP->getCards());
    }

}
