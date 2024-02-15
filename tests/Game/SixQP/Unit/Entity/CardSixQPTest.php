<?php

namespace App\Tests\Game\SixQP\Unit\Entity;

use App\Entity\Game\SixQP\CardSixQP;
use PHPUnit\Framework\TestCase;

class CardSixQPTest extends TestCase
{

    public function testCreateCardWithValidValue()
    {
        $card = new CardSixQP();
        $card->setValue(5);
        $this->expectNotToPerformAssertions();
    }

    public function testCreateCardWithInvalidValue()
    {
        $card = new CardSixQP();
        $this->expectException(\Exception::class);
        $card->setValue(105);

        $card = new CardSixQP();
        $this->expectException(\Exception::class);
        $card->setValue(0);
    }

}
