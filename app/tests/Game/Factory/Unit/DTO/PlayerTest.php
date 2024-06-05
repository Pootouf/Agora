<?php

namespace App\Tests\Game\Factory\Unit\DTO;


use App\Entity\Game\DTO\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{

    private Player $player;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // setUp()
        //THEN
        $this->assertTrue($this->player->getId() >= 0);
    }
    public function testSetUsername(): void
    {
        //GIVEN
        $username = "test";
        //WHEN
        $this->player->setUsername($username);
        //THEN
        $this->assertSame($username, $this->player->getUsername());
    }

    public function testSetPlayerTurnWhenFalse(): void
    {
        //GIVEN
        $bool = false;
        //WHEN
        $this->player->setTurnOfPlayer($bool);
        //THEN
        $this->assertFalse($this->player->isTurnOfPlayer());
    }

    public function testSetPlayerTurnWhenTrue(): void
    {
        //GIVEN
        $bool = true;
        //WHEN
        $this->player->setTurnOfPlayer($bool);
        //THEN
        $this->assertTrue($this->player->isTurnOfPlayer());
    }

    public function testSetExcludedWhenReturnTrue() : void
    {
        // GIVEN

        $excluded = true;

        // WHEN

        $this->player->setExcluded($excluded);

        // THEN

        $this->assertTrue($this->player->isExcluded());
    }

    public function testSetExcludedWhenReturnFalse() : void
    {
        // GIVEN

        $excluded = false;

        // WHEN

        $this->player->setExcluded($excluded);

        // THEN

        $this->assertFalse($this->player->isExcluded());
    }

    protected function setUp(): void
    {
        $this->player = new Player();
    }
}