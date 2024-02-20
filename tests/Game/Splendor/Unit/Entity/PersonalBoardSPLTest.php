<?php

namespace App\Tests\Game\Splendor\Unit\Entity;

use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\NobleTileSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerCardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\SelectedTokenSPL;
use App\Entity\Game\Splendor\TokenSPL;
use PHPUnit\Framework\TestCase;

class PersonalBoardSPLTest extends TestCase
{
    private PersonalBoardSPL $personalBoardSPL;

    public function testInit(): void
    {
        //GIVEN

        //WHEN
        // is triggered by setUp()
        //THEN
        $this->assertTrue($this->personalBoardSPL->getId() >= 0);
        $this->assertEmpty($this->personalBoardSPL->getTokens());
        $this->assertEmpty($this->personalBoardSPL->getNobleTiles());
        $this->assertEmpty($this->personalBoardSPL->getPlayerCards());
        $this->assertEmpty($this->personalBoardSPL->getSelectedTokens());
    }

    public function testAddTokenWhenNotOwned(): void
    {
        //GIVEN
        $token = new TokenSPL();
        //WHEN
        $this->personalBoardSPL->addToken($token);
        //THEN
        $this->assertContains($token, $this->personalBoardSPL->getTokens());
    }

    public function testAddTokenWhenOwned(): void
    {
        //GIVEN
        $token = new TokenSPL();
        $this->personalBoardSPL->addToken($token);
        $expectedLength = 1;
        //WHEN
        $this->personalBoardSPL->addToken($token);
        //THEN
        $this->assertContains($token, $this->personalBoardSPL->getTokens());
        $this->assertEquals($expectedLength, $this->personalBoardSPL->getTokens()->count());
    }

    public function testRemoveToken(): void
    {
        //GIVEN
        $token = new TokenSPL();
        $this->personalBoardSPL->addToken($token);
        //WHEN
        $this->personalBoardSPL->removeToken($token);
        //THEN
        $this->assertNotContains($token, $this->personalBoardSPL->getTokens());
    }

    public function testAddNobleTileWhenNotOwned(): void
    {
        //GIVEN
        $nobleTile = new NobleTileSPL();
        //WHEN
        $this->personalBoardSPL->addNobleTile($nobleTile);
        //THEN
        $this->assertContains($nobleTile, $this->personalBoardSPL->getNobleTiles());
    }

    public function testAddNobleTileWhenOwned(): void
    {
        //GIVEN
        $nobleTile = new NobleTileSPL();
        $this->personalBoardSPL->addNobleTile($nobleTile);
        $expectedLength = 1;
        //WHEN
        $this->personalBoardSPL->addNobleTile($nobleTile);
        //THEN
        $this->assertContains($nobleTile, $this->personalBoardSPL->getNobleTiles());
        $this->assertEquals($expectedLength, $this->personalBoardSPL->getNobleTiles()->count());
    }

    public function testRemoveNobleTile(): void
    {
        //GIVEN
        $nobleTile = new NobleTileSPL();
        $this->personalBoardSPL->addNobleTile($nobleTile);
        //WHEN
        $this->personalBoardSPL->removeNobleTile($nobleTile);
        //THEN
        $this->assertNotContains($nobleTile, $this->personalBoardSPL->getNobleTiles());
    }

    public function testAddPlayerCardWhenNotOwned(): void
    {
        //GIVEN
        $playerCard = new PlayerCardSPL();
        //WHEN
        $this->personalBoardSPL->addPlayerCard($playerCard);
        //THEN
        $this->assertContains($playerCard, $this->personalBoardSPL->getPlayerCards());
        $this->assertSame($this->personalBoardSPL, $playerCard->getPersonalBoardSPL());
    }

    public function testAddPlayerCardWhenOwned(): void
    {
        //GIVEN
        $playerCard = new PlayerCardSPL();
        $this->personalBoardSPL->addPlayerCard($playerCard);
        $expectedLength = 1;
        //WHEN
        $this->personalBoardSPL->addPlayerCard($playerCard);
        //THEN
        $this->assertContains($playerCard, $this->personalBoardSPL->getPlayerCards());
        $this->assertSame($expectedLength, $this->personalBoardSPL->getPlayerCards()->count());
    }

    public function testRemovePlayerCard(): void
    {
        //GIVEN
        $playerCard = new PlayerCardSPL();
        $this->personalBoardSPL->addPlayerCard($playerCard);
        //WHEN
        $this->personalBoardSPL->removePlayerCard($playerCard);
        //THEN
        $this->assertNotContains($playerCard, $this->personalBoardSPL->getPlayerCards());
    }

    public function testSetPlayer(): void
    {
        //GIVEN
        $game = new GameSPL();
        $player = new PlayerSPL('test', $game);
        //WHEN
        $this->personalBoardSPL->setPlayerSPL($player);
        //THEN
        $this->assertSame($player, $this->personalBoardSPL->getPlayerSPL());
        $this->assertSame($this->personalBoardSPL, $player->getPersonalBoard());
    }

    public function testAddSelectedTokenWhenNotOwned(): void
    {
        //GIVEN
        $selectedToken = new SelectedTokenSPL();
        //WHEN
        $this->personalBoardSPL->addSelectedToken($selectedToken);
        //THEN
        $this->assertContains($selectedToken, $this->personalBoardSPL->getSelectedTokens());
    }

    public function testAddSelectedTokenWhenOwned(): void
    {
        //GIVEN
        $selectedToken = new SelectedTokenSPL();
        $this->personalBoardSPL->addSelectedToken($selectedToken);
        $expectedLength = 1;
        //WHEN
        $this->personalBoardSPL->addSelectedToken($selectedToken);
        //THEN
        $this->assertContains($selectedToken, $this->personalBoardSPL->getSelectedTokens());
        $this->assertEquals($expectedLength, $this->personalBoardSPL->getSelectedTokens()->count());
    }

    public function testRemoveSelectedToken(): void
    {
        //GIVEN
        $selectedToken = new SelectedTokenSPL();
        $this->personalBoardSPL->addSelectedToken($selectedToken);
        //WHEN
        $this->personalBoardSPL->removeSelectedToken($selectedToken);
        //THEN
        $this->assertNotContains($selectedToken, $this->personalBoardSPL->getSelectedTokens());
    }

    protected function setUp(): void
    {
        $this->personalBoardSPL = new PersonalBoardSPL();
    }
}


