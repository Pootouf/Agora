<?php

namespace App\Tests\Game\Factory\Unit;

use App\Entity\Game\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    private Log $log;

    public function testInit()
    {
        //GIVEN

        //WHEN
        // setUp()

        //THEN
        $this->assertTrue($this->log->getId() >= 0);
    }

    public function testSetGameId(): void
    {
        //GIVEN
        $gameId = 556;
        //WHEN
        $this->log->setGameId($gameId);
        //THEN
        $this->assertSame($gameId, $this->log->getGameId());
    }

    public function testSetPlayerId(): void
    {
        //GIVEN
        $playerId = 589;
        //WHEN
        $this->log->setPlayerId($playerId);
        //THEN
        $this->assertSame($playerId, $this->log->getPlayerId());
    }

    public function testSetMessage(): void
    {
        //GIVEN
        $message = "bonjour à tous les amis";
        //WHEN
        $this->log->setMessage($message);
        //THEN
        $this->assertEquals($message, $this->log->getMessage());
    }

    public function testSetDate(): void
    {
        //GIVEN
        $date = new \DateTime();
        //WHEN
        $this->log->setDate($date);
        //THEN
        $this->assertSame($date, $this->log->getDate());
    }
    protected function setUp(): void
    {
        $this->log = new Log();
    }
}