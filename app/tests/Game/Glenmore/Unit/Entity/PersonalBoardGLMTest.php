<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\CardGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerCardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use PHPUnit\Framework\TestCase;

class PersonalBoardGLMTest extends TestCase
{
    private PersonalBoardGLM $personalBoardGLM;

    public function testInit() : void
    {
        // WHEN
        // Is triggered by setUp method

        // THEN
        $this->assertTrue($this->personalBoardGLM->getId() >= 0);
        $this->assertNotNull($this->personalBoardGLM->getPlayerCardGLM());
        $this->assertNotNull($this->personalBoardGLM->getPlayerTiles());
    }

    public function testSetLeaderCount() : void
    {
        // GIVEN

        $leaderCount = 7;

        // WHEN

        $this->personalBoardGLM->setLeaderCount($leaderCount);

        // THEN

        $this->assertSame($leaderCount, $this->personalBoardGLM->getLeaderCount());
    }

    public function testSetMoney() : void
    {
        // GIVEN

        $money = 6;

        // WHEN

        $this->personalBoardGLM->setMoney($money);

        // THEN

        $this->assertSame($money, $this->personalBoardGLM->getMoney());
    }

    public function testAddPlayerTileNotYetAdded() : void
    {
        // GIVEN

        $playerTile = new PlayerTileGLM();

        // WHEN

        $this->personalBoardGLM->addPlayerTile($playerTile);

        // THEN

        $this->assertContains($playerTile, $this->personalBoardGLM->getPlayerTiles());
        $this->assertSame($this->personalBoardGLM, $playerTile->getPersonalBoard());
    }

    public function testAddPlayerTileAlreadyAdded() : void
    {
        // GIVEN

        $playerTile = new PlayerTileGLM();
        $this->personalBoardGLM->addPlayerTile($playerTile);
        $length = $this->personalBoardGLM->getPlayerTiles()->count();

        // WHEN

        $this->personalBoardGLM->addPlayerTile($playerTile);

        // THEN

        $this->assertSame($length, $this->personalBoardGLM->getPlayerTiles()->count());
    }

    public function testRemovePlayerTileNotYetRemoved() : void
    {
        // GIVEN

        $playerTile = new PlayerTileGLM();
        $this->personalBoardGLM->addPlayerTile($playerTile);

        // WHEN

        $this->personalBoardGLM->removePlayerTile($playerTile);

        // THEN

        $this->assertNotContains($playerTile, $this->personalBoardGLM->getPlayerTiles());
        $this->assertNull($playerTile->getPersonalBoard());
    }


    public function testRemovePlayerTileAlreadyRemoved() : void
    {
        // GIVEN

        $playerTile = new PlayerTileGLM();
        $this->personalBoardGLM->addPlayerTile($playerTile);
        $this->personalBoardGLM->removePlayerTile($playerTile);
        $length = $this->personalBoardGLM->getPlayerTiles()->count();

        // WHEN

        $this->personalBoardGLM->removePlayerTile($playerTile);

        // THEN

        $this->assertSame($length, $this->personalBoardGLM->getPlayerTiles()->count());
    }

    public function testSetPlayer() : void
    {
        // GIVEN

        $player = new PlayerGLM("user", new GameGLM());

        // WHEN

        $this->personalBoardGLM->setPlayerGLM($player);

        // THEN

        $this->assertSame($player, $this->personalBoardGLM->getPlayerGLM());
        $this->assertSame($this->personalBoardGLM, $player->getPersonalBoard());
    }

    public function testAddPlayerCardNotYetAdded() : void
    {
        // GIVEN

        $playerCard = new PlayerCardGLM($this->personalBoardGLM, new CardGLM());

        // WHEN

        $this->personalBoardGLM->addPlayerCardGLM($playerCard);

        // THEN

        $this->assertContains($playerCard, $this->personalBoardGLM->getPlayerCardGLM());
        $this->assertSame($this->personalBoardGLM, $playerCard->getPersonalBoard());
    }

    public function testAddPlayerCardAlreadyAdded() : void
    {
        // GIVEN

        $playerCard = new PlayerCardGLM($this->personalBoardGLM, new CardGLM());
        $this->personalBoardGLM->addPlayerCardGLM($playerCard);
        $length = $this->personalBoardGLM->getPlayerCardGLM()->count();

        // WHEN

        $this->personalBoardGLM->addPlayerCardGLM($playerCard);

        // THEN

        $this->assertSame($length, $this->personalBoardGLM->getPlayerCardGLM()->count());
    }

    public function testRemovePlayerCardNotYetRemoved() : void
    {
        // GIVEN

        $playerCard = new PlayerCardGLM($this->personalBoardGLM, new CardGLM());
        $this->personalBoardGLM->addPlayerCardGLM($playerCard);

        // WHEN

        $this->personalBoardGLM->removePlayerCardGLM($playerCard);

        // THEN
        $this->assertNotContains($playerCard, $this->personalBoardGLM->getPlayerCardGLM());
    }

    public function testRemovePlayerCardAlreadyRemoved() : void
    {
        // GIVEN

        $playerCard = new PlayerCardGLM($this->personalBoardGLM, new CardGLM());
        $this->personalBoardGLM->addPlayerCardGLM($playerCard);
        $this->personalBoardGLM->removePlayerCardGLM($playerCard);
        $length = $this->personalBoardGLM->getPlayerCardGLM()->count();

        // WHEN

        $this->personalBoardGLM->removePlayerCardGLM($playerCard);

        // THEN
        $this->assertSame($length, $this->personalBoardGLM->getPlayerCardGLM()->count());
    }

    protected function setUp(): void
    {
        $this->personalBoardGLM = new PersonalBoardGLM();
    }
}