<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\NobleTileSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\RowSPL;
use App\Entity\Game\Splendor\TokenSPL;
use PHPUnit\Framework\TestCase;

class MainBoardSPLTest extends TestCase
{
    private MainBoardSPL $mainBoardSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertTrue($this->mainBoardSPL->getId() >= 0);
        $this->assertEmpty($this->mainBoardSPL->getRowsSPL());
        $this->assertEmpty($this->mainBoardSPL->getTokens());
        $this->assertEmpty($this->mainBoardSPL->getNobleTiles());
        $this->assertEmpty($this->mainBoardSPL->getDrawCards());
    }

    public function testAddRowSPLWhenNotOwned(): void
    {
        //GIVEN
        $row = new RowSPL();
        //WHEN
        $this->mainBoardSPL->addRowsSPL($row);
        //THEN
        $this->assertContains($row, $this->mainBoardSPL->getRowsSPL());
    }

    public function testAddRowWhenOwned(): void
    {
        //GIVEN
        $row = new RowSPL();
        $this->mainBoardSPL->addRowsSPL($row);
        $expectedLength = 1;
        //WHEN
        $this->mainBoardSPL->addRowsSPL($row);
        //THEN
        $this->assertContains($row, $this->mainBoardSPL->getRowsSPL());
        $this->assertSame($expectedLength, $this->mainBoardSPL->getRowsSPL()->count());
    }

    public function testRemoveRow(): void
    {
        //GIVEN
        $row = new RowSPL();
        $this->mainBoardSPL->addRowsSPL($row);
        //WHEN
        $this->mainBoardSPL->removeRowsSPL($row);
        //THEN
        $this->assertNotContains($row, $this->mainBoardSPL->getRowsSPL());
    }

    public function testAddTokenWhenNotOwned(): void
    {
        //GIVEN
        $token = new TokenSPL();
        //WHEN
        $this->mainBoardSPL->addToken($token);
        //THEN
        $this->assertContains($token, $this->mainBoardSPL->getTokens());
    }

    public function testAddTokenWhenOwned(): void
    {
        //GIVEN
        $token = new TokenSPL();
        $this->mainBoardSPL->addToken($token);
        $expectedLength = 1;
        //WHEN
        $this->mainBoardSPL->addToken($token);
        //THEN
        $this->assertContains($token, $this->mainBoardSPL->getTokens());
        $this->assertSame($expectedLength, $this->mainBoardSPL->getTokens()->count());
    }

    public function testRemoveToken(): void
    {
        //GIVEN
        $token = new TokenSPL();
        $this->mainBoardSPL->addToken($token);
        //WHEN
        $this->mainBoardSPL->removeToken($token);
        //THEN
        $this->assertNotContains($token, $this->mainBoardSPL->getTokens());
    }

    public function testAddNobleTileWhenNotOwned(): void
    {
        //GIVEN
        $tile = new NobleTileSPL();
        //WHEN
        $this->mainBoardSPL->addNobleTile($tile);
        //THEN
        $this->assertContains($tile, $this->mainBoardSPL->getNobleTiles());
    }

    public function testAddNobleTileWhenOwned(): void
    {
        //GIVEN
        $tile = new NobleTileSPL();
        $this->mainBoardSPL->addNobleTile($tile);
        $expectedLength = 1;
        //WHEN
        $this->mainBoardSPL->addNobleTile($tile);
        //THEN
        $this->assertContains($tile, $this->mainBoardSPL->getNobleTiles());
        $this->assertSame($expectedLength, $this->mainBoardSPL->getNobleTiles()->count());
    }

    public function testRemoveNobleTile(): void
    {
        //GIVEN
        $tile = new NobleTileSPL();
        $this->mainBoardSPL->addNobleTile($tile);
        //WHEN
        $this->mainBoardSPL->removeNobleTile($tile);
        //THEN
        $this->assertNotContains($tile, $this->mainBoardSPL->getNobleTiles());
    }

    public function testAddDrawCardWhenNotOwned(): void
    {
        //GIVEN
        $drawCard = new DrawCardsSPL();
        //WHEN
        $this->mainBoardSPL->addDrawCard($drawCard);
        //THEN
        $this->assertContains($drawCard, $this->mainBoardSPL->getDrawCards());
    }

    public function testAddDrawCardWhenOwned(): void
    {
        //GIVEN
        $drawCard = new DrawCardsSPL();
        $this->mainBoardSPL->addDrawCard($drawCard);
        $expectedLength = 1;
        //WHEN
        $this->mainBoardSPL->addDrawCard($drawCard);
        //THEN
        $this->assertContains($drawCard, $this->mainBoardSPL->getDrawCards());
        $this->assertSame($expectedLength, $this->mainBoardSPL->getDrawCards()->count());
    }

    public function testRemoveDrawCard(): void
    {
        //GIVEN
        $drawCard = new DrawCardsSPL();
        $this->mainBoardSPL->addDrawCard($drawCard);
        //WHEN
        $this->mainBoardSPL->removeDrawCard($drawCard);
        //THEN
        $this->assertNotContains($drawCard, $this->mainBoardSPL->getDrawCards());
    }
    public function testSetGame(): void
    {
        //GIVEN
        $game = new GameSPL();
        //WHEN
        $this->mainBoardSPL->setGameSPL($game);
        //THEN
        $this->assertSame($game, $this->mainBoardSPL->getGameSPL());
        $this->assertSame($this->mainBoardSPL, $game->getMainBoard());
    }
    protected function setUp(): void
    {
        $this->mainBoardSPL = new MainBoardSPL();
    }
}


