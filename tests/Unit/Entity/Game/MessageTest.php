<?php

namespace Unit\Entity\Game;

use App\Entity\Game\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function shouldCreateMessage() {
        
        // GIVEN
        $message;

        // WHEN
        $message = new Message();

        // THEN
        
        $this->assertEquals(null, $message->getContent());
        $this->assertEquals(null, $message->getDate());
        $this->assertEquals(null, $message->getAuthorId());
        $this->assertEquals(null, $message->getGameId());
        $this->assertNotEquals(null, $message->getId());
    }

    public function shouldSetContent() {
        // GIVEN
        $message = new Message();

        // WHEN
        $message->setContent("Bonjour à tous");

        // THEN
        $this->assertEquals("Bonjour à tous", $message->getContent());
    }

    public function shouldSetAuthorId() {
        // GIVEN
        $message = new Message();

        // WHEN
        $message->setAuthorId(1);

        // THEN
        $this->assertEquals(1, $message->getAuthorId());
    }

    public function shouldSetGameId() {
        // GIVEN
        $message = new Message();

        // WHEN
        $message->setGameId(1);

        // THEN
        $this->assertEquals(1, $message->getGameId());
    }

    public function shouldSetDate() {
        // GIVEN
        $message = new Message();
        $date = new DateTime();
        $date->setDate(2024, 6, 16);

        // WHEN
        $message->setDate($date);

        // THEN
        $this->assertEquals($date, $message->getDate());
    }

}
