<?php

namespace App\Tests\Game\SixQP\Unit\Entity;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use PHPUnit\Framework\TestCase;
use App\Entity\Game\SixQP\ChosenCardSixQP;

class DiscardSixQPTest extends TestCase
{
    private DiscardSixQP $discardSixQP;
    protected function setUp(): void
    {
        $game = new GameSixQP();
        $player = new PlayerSixQP("test", $game);
        $this->discardSixQP = new DiscardSixQP($player, $game);
        $this->discardSixQP->addCard(new CardSixQP());
    }

    public function testSetPlayer(): void
    {
        //GIVEN
        $game = new GameSixQP();
        $player = new PlayerSixQP('test', $game);
        //WHEN
        $this->discardSixQP->setPlayer($player);
        //THEN
        $this->assertSame($player, $this->discardSixQP->getPlayer());
    }

    public function testSetGame(): void
    {
        //GIVEN
        $game = new GameSixQP();
        //WHEN
        $this->discardSixQP->setGame($game);
        //THEN
        $this->assertSame($game, $this->discardSixQP->getGame());
    }

    public function testAddCard(): void
    {
        //GIVEN
        $card = new CardSixQP();
        //WHEN
        $this->discardSixQP->addCard($card);
        //THEN
        $cards = $this->discardSixQP->getCards();
        $this->assertContains($card, $cards);
    }

    public function testRemoveCard(): void
    {
        //GIVEN
        $card = $this->discardSixQP->getCards()->first();
        //WHEN
        $this->discardSixQP->removeCard($card);
        //THEN
        $cards = $this->discardSixQP->getCards();
        $this->assertNotContains($card, $cards);
    }

    public function testSetTotalPoints() : void
    {
        //GIVEN
        $total = 13;
        //WHEN
        $this->discardSixQP->setTotalPoints($total);
        //THEN
        $this->assertSame($total, $this->discardSixQP->getTotalPoints());
    }

    public function testAddTotalPoints() : void
    {
        //GIVEN
        $total = 13;
        $this->discardSixQP->setTotalPoints($total);
        $bonus = 1;
        //WHEN
        $this->discardSixQP->addPoints($bonus);
        //THEN
        $this->assertSame($total + $bonus, $this->discardSixQP->getTotalPoints());
    }
}
