<?php

namespace App\Tests\Game\SixQP\Unit\Entity;

use App\Entity\Game\SixQP\CardSixQP;
use PHPUnit\Framework\TestCase;

class CardSixQPTest extends TestCase
{
    public function testCreateCardWithValidValue() : void
    {
        //GIVEN

        //WHEN
        $card = new CardSixQP();
        $card->setValue(5);
        $card->setPoints(12);

        //THEN
        $this->assertSame(5, $card->getValue());
        $this->assertSame(12, $card->getPoints());
    }

    public function testCreateCardWithInvalidValueWhenTooHigh() : void
    {
        //GIVEN
        $card = new CardSixQP();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $card->setValue(105);
    }
    public function testCreateCardWithInvalidValueWhenTooLow()
    {
        //GIVEN
        $card = new CardSixQP();
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $card->setValue(0);
    }

}
