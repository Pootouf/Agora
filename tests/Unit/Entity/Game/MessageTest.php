<?php

namespace App\Tests\Unit\Entity\Game;

use App\Entity\Game\Message;
use DateTime;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testCreateMessage() {
        
        // GIVEN

        // WHEN
        $message = new Message();

        // THEN
        
        $this->assertNull($message->getContent());
        $this->assertNull($message->getDate());
        $this->assertNull($message->getAuthorId());
        $this->assertNull($message->getGameId());
    }

    public function testSetContent() {
        // GIVEN
        $message = new Message();

        // WHEN
        $message->setContent("Bonjour à tous");

        // THEN
        $this->assertEquals("Bonjour à tous", $message->getContent());
    }

    public function testSetAuthorId() {
        // GIVEN
        $message = new Message();

        // WHEN
        $message->setAuthorId(1);

        // THEN
        $this->assertEquals(1, $message->getAuthorId());
    }

    public function testSetGameId() {
        // GIVEN
        $message = new Message();

        // WHEN
        $message->setGameId(1);

        // THEN
        $this->assertEquals(1, $message->getGameId());
    }

    public function testSetDate() {
        // GIVEN
        $message = new Message();
        $date = new DateTime("now");
        $date->setDate(2024, 6, 16);

        // WHEN
        $message->setDate($date);

        // THEN
        $this->assertEquals($date, $message->getDate());
    }

}
