<?php

namespace App\Tests\Game\Factory\Unit\DTO;

use App\Entity\Game\DTO\Card;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{

    private Card $card;

    public function testSetValue(): void
    {
        //GIVEN
        $value = 6;
        //WHEN
        $this->card->setValue($value);
        //THEN
        $this->assertSame($value, $this->card->getValue());
    }
    protected function setUp(): void
    {
        $this->card = new Card();
    }
}