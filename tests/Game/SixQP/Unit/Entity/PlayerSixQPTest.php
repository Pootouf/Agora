<?php

namespace App\Tests\Game\SixQP\Unit\Entity;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use PHPUnit\Framework\TestCase;

class PlayerSixQPTest extends TestCase
{
    private PlayerSixQP $playerSixQP;
    protected function setUp(): void
    {
        $game = new GameSixQP();
        $this->playerSixQP = new PlayerSixQP("test", $game);
        $this->playerSixQP->addCard(new CardSixQP());
    }

    public function testInit() : void
    {
        //GIVEN
        $username = "test";
        $game = new GameSixQP();
        //WHEN
        $player = new PlayerSixQP($username, $game);
        //THEN
        $this->assertEmpty($player->getCards());
        $this->assertSame($username, $player->getUsername());
        $this->assertSame($game, $player->getGame());
    }
    public function testSetUsername() : void
    {
        //GIVEN
        $newUsername = "a";
        //WHEN
        $this->playerSixQP->setUsername($newUsername);
        //THEN
        $this->assertSame($newUsername, $this->playerSixQP->getUsername());
    }

    public function testSetTurnOfPlayer() : void
    {
        //GIVEN
        $turnOfPlayer = true;
        //WHEN
        $this->playerSixQP->setTurnOfPlayer($turnOfPlayer);
        //THEN
        $this->assertTrue($this->playerSixQP->isTurnOfPlayer());
    }

    public function testSetGame() : void
    {
        //GIVEN
        $game = new GameSixQP();
        //WHEN
        $this->playerSixQP->setGame($game);
        //THEN
        $this->assertSame($game, $this->playerSixQP->getGame());
    }

    public function testAddCard() : void
    {
        //GIVEN
        $card = new CardSixQP();
        //WHEN
        $this->playerSixQP->addCard($card);
        //THEN
        $this->assertContains($card, $this->playerSixQP->getCards());
    }

    public function testRemoveCard() : void
    {
        //GIVEN
        $card = $this->playerSixQP->getCards()->first();
        //WHEN
        $this->playerSixQP->removeCard($card);
        //THEN
        $this->assertNotContains($card, $this->playerSixQP->getCards());
    }

    public function testClearCards() : void
    {
        //GIVEN

        //WHEN
        $this->playerSixQP->clearCards();
        //THEN
        $this->assertEmpty($this->playerSixQP->getCards());
    }

    public function testSetChosenCard() : void
    {
        //GIVEN
        $chosenCard = new ChosenCardSixQP($this->playerSixQP,
            $this->playerSixQP->getGame(), $this->playerSixQP->getCards()->first());
        //WHEN
        $this->playerSixQP->setChosenCardSixQP($chosenCard);
        //THEN
        $this->assertSame($chosenCard, $this->playerSixQP->getChosenCardSixQP());
    }

    public function testSetChosenCardWhenChosenCardIsOwnedByAnotherPlayer() : void
    {
        //GIVEN
        $chosenCard = new ChosenCardSixQP(new PlayerSixQP("test2", $this->playerSixQP->getGame()),
            $this->playerSixQP->getGame(), $this->playerSixQP->getCards()->first());
        //WHEN
        $this->playerSixQP->setChosenCardSixQP($chosenCard);
        //THEN
        $this->assertSame($this->playerSixQP, $chosenCard->getPlayer());
        $this->assertSame($chosenCard, $this->playerSixQP->getChosenCardSixQP());
    }
    public function testSetDiscard() : void
    {
        //GIVEN
        $discard = new DiscardSixQP($this->playerSixQP, $this->playerSixQP->getGame());
        //WHEN
        $this->playerSixQP->setDiscardSixQP($discard);
        //THEN
        $this->assertSame($discard, $this->playerSixQP->getDiscardSixQP());
    }

    public function testSetDiscardWhenDiscardIsOwnedByAnotherPlayer() : void
    {
        //GIVEN
        $discard = new DiscardSixQP(new PlayerSixQP("test2", $this->playerSixQP->getGame()),
            $this->playerSixQP->getGame());
        //WHEN
        $this->playerSixQP->setDiscardSixQP($discard);
        //THEN
        $this->assertSame($this->playerSixQP, $discard->getPlayer());
        $this->assertSame($discard, $this->playerSixQP->getDiscardSixQP());
    }
}