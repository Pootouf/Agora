<?php

namespace App\Tests\Game\Factory\Unit\DTO;


use App\Entity\Game\DTO\Token;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{

    private Token $token;

    public function testSetType(): void
    {
        //GIVEN
        $type = "newType";
        //WHEN
        $this->token->setType($type);
        //THEN
        $this->assertSame($type, $this->token->getType());
    }

    protected function setUp(): void
    {
        $this->token = new Token();
    }
}