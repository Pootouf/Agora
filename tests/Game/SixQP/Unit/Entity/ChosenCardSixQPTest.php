<?php

namespace App\Tests\Game\SixQP\Unit\Entity;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use PHPUnit\Framework\TestCase;
use App\Entity\Game\SixQP\ChosenCardSixQP;

class ChosenCardSixQPTest extends TestCase
{
    private ChosenCardSixQP $chosenCardSixQP;
    protected function setUp(): void
    {
        $game = new GameSixQP();
        $player = new PlayerSixQP("test", $game);
        $card = new CardSixQP();
        $this->chosenCardSixQP = new ChosenCardSixQP($player, $game, $card);
    }

    public function testSetPlayer(): void
    {
        //GIVEN
        $game = new GameSixQP();
        $player = new PlayerSixQP('test', $game);
        //WHEN
        $this->chosenCardSixQP->setPlayer($player);
        //THEN
        $this->assertSame($player, $this->chosenCardSixQP->getPlayer());
    }

    public function testSetGame(): void
    {
        //GIVEN
        $game = new GameSixQP();
        //WHEN
        $this->chosenCardSixQP->setGame($game);
        //THEN
        $this->assertSame($game, $this->chosenCardSixQP->getGame());
    }

    public function testSetCard(): void
    {
        //GIVEN
        $card = new CardSixQP();
        //WHEN
        $this->chosenCardSixQP->setCard($card);
        //THEN
        $this->assertSame($card, $this->chosenCardSixQP->getCard());
    }
}
