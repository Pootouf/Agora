<?php

namespace App\Tests\Game\Factory\Unit;

use App\Entity\Game\Help;
use PHPUnit\Framework\TestCase;

class HelpTest extends TestCase
{
    private Help $help;

    public function testInit()
    {
        //GIVEN

        //WHEN
        // setUp()

        //THEN
        $this->assertTrue($this->help->getId() >= 0);
    }

    public function testSetGameName(): void
    {
        //GIVEN
        $gameName = "aaaaaaaa";
        //WHEN
        $this->help->setGameName($gameName);
        //THEN
        $this->assertEquals($gameName, $this->help->getGameName());
    }

    public function testSetTitle(): void
    {
        //GIVEN
        $title = "Un titre";
        //WHEN
        $this->help->setTitle($title);
        //THEN
        $this->assertEquals($title, $this->help->getTitle());
    }

    public function testSetDescription(): void
    {
        //GIVEN
        $message = "aloha";
        //WHEN
        $this->help->setDescription($message);
        //THEN
        $this->assertEquals($message, $this->help->getDescription());
    }

    public function testSetImage(): void
    {
        //GIVEN
        $url = "img.png";
        //WHEN
        $this->help->setImage($url);
        //THEN
        $this->assertEquals($url, $this->help->getImage());
    }
    protected function setUp(): void
    {
        $this->help = new Help();
    }
}