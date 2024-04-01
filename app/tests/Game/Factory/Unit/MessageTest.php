<?php

namespace App\Tests\Game\Factory\Unit;

use App\Entity\Game\Message;
use DateTime;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testCreateMessage()
    {

        // GIVEN

        // WHEN
        $message = new Message();

        // THEN
        $this->assertTrue($message->getId() >= 0);
        $this->assertNull($message->getContent());
        $this->assertNull($message->getDate());
        $this->assertNull($message->getAuthorId());
        $this->assertNull($message->getGameId());
    }

    public function testSetContent(): void
    {
        // GIVEN
        $message = new Message();
        $string = "Bonjour à tous";
        // WHEN
        $message->setContent($string);

        // THEN
        $this->assertEquals($string, $message->getContent());
    }

    public function testSetAuthorId(): void
    {
        // GIVEN
        $message = new Message();
        $authorId = 1;
        // WHEN
        $message->setAuthorId($authorId);

        // THEN
        $this->assertEquals($authorId, $message->getAuthorId());
    }

    public function testSetGameId(): void
    {
        // GIVEN
        $message = new Message();
        $gameId = 1;
        // WHEN
        $message->setGameId($gameId);

        // THEN
        $this->assertEquals($gameId, $message->getGameId());
    }

    public function testSetDate(): void
    {
        // GIVEN
        $message = new Message();
        $date = new DateTime("now");
        $date->setDate(2024, 6, 16);

        // WHEN
        $message->setDate($date);

        // THEN
        $this->assertEquals($date, $message->getDate());
    }

    public function testSetAuthorUsername(): void
    {
        //GIVEN
        $message = new Message();
        $author = "me";
        //WHEN
        $message->setAuthorUsername($author);
        //THEN
        $this->assertEquals($author, $message->getAuthorUsername());
    }
}
