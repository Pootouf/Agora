<?php

namespace App\Tests\Game\Glenmore\Unit\Entity;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\CardGLM;
use App\Entity\Game\Glenmore\CreatedResourceGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerCardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\SelectedResourceGLM;
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
        $this->assertNotNull($this->personalBoardGLM->getSelectedResources());
        $this->assertNotNull($this->personalBoardGLM->getCreatedResources());
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

        $this->assertContains($playerTile, $this->personalBoardGLM->getPlayerTiles());
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

        $this->assertNotContains($playerTile, $this->personalBoardGLM->getPlayerTiles());
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

        $this->assertContains($playerCard, $this->personalBoardGLM->getPlayerCardGLM());
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
        $this->assertNull($playerCard->getPersonalBoard());
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

        $this->assertNotContains($playerCard, $this->personalBoardGLM->getPlayerCardGLM());
        $this->assertSame($length, $this->personalBoardGLM->getPlayerCardGLM()->count());
    }

    public function testSetBuyingTile() : void
    {
        // GIVEN

        $selectedTile = new BoardTileGLM();

        // WHEN

        $this->personalBoardGLM->setBuyingTile($selectedTile);

        // THEN

        $this->assertSame($selectedTile, $this->personalBoardGLM->getBuyingTile());
    }

    public function testAddSelectedResourceNotYetAdded() : void
    {
        // GIVEN

        $selectedResource = new SelectedResourceGLM();

        // WHEN

        $this->personalBoardGLM->addSelectedResource($selectedResource);

        // THEN

        $this->assertContains($selectedResource, $this->personalBoardGLM->getSelectedResources());
        $this->assertSame($this->personalBoardGLM, $selectedResource->getPersonalBoardGLM());
    }

    public function testAddSelectedResourceAlreadyAdded() : void
    {
        // GIVEN

        $selectedResource = new SelectedResourceGLM();
        $this->personalBoardGLM->addSelectedResource($selectedResource);
        $length = $this->personalBoardGLM->getSelectedResources()->count();

        // WHEN

        $this->personalBoardGLM->addSelectedResource($selectedResource);

        // THEN

        $this->assertContains($selectedResource, $this->personalBoardGLM->getSelectedResources());
        $this->assertSame($this->personalBoardGLM->getSelectedResources()->count(), $length);
    }

    public function testRemoveSelectedResourceNotYetRemoved() : void
    {
        // GIVEN

        $selectedResource = new SelectedResourceGLM();
        $this->personalBoardGLM->addSelectedResource($selectedResource);


        // WHEN

        $this->personalBoardGLM->removeSelectedResource($selectedResource);

        // THEN

        $this->assertNotContains($selectedResource, $this->personalBoardGLM->getSelectedResources());
        $this->assertNull($selectedResource->getPersonalBoardGLM());
    }

    public function testRemoveSelectedResourceAlreadyRemoved() : void
    {
        // GIVEN

        $selectedResource = new SelectedResourceGLM();
        $this->personalBoardGLM->addSelectedResource($selectedResource);
        $this->personalBoardGLM->removeSelectedResource($selectedResource);
        $length = $this->personalBoardGLM->getSelectedResources()->count();

        // WHEN

        $this->personalBoardGLM->removeSelectedResource($selectedResource);

        // THEN

        $this->assertNotContains($selectedResource, $this->personalBoardGLM->getSelectedResources());
        $this->assertSame($length, $this->personalBoardGLM->getSelectedResources()->count());
    }

    public function testAddCreatedResourceYetNotAdded() : void
    {
        // GIVEN

        $createdResource = new CreatedResourceGLM();

        // WHEN

        $this->personalBoardGLM->addCreatedResource($createdResource);

        // THEN

        $this->assertContains($createdResource, $this->personalBoardGLM->getCreatedResources());
        $this->assertSame($this->personalBoardGLM, $createdResource->getPersonalBoardGLM());
    }

    public function testAddCreatedResourceAlreadyAdded() : void
    {
        // GIVEN

        $createdResource = new CreatedResourceGLM();
        $this->personalBoardGLM->addCreatedResource($createdResource);
        $length = $this->personalBoardGLM->getCreatedResources()->count();

        // WHEN

        $this->personalBoardGLM->addCreatedResource($createdResource);

        // THEN

        $this->assertContains($createdResource, $this->personalBoardGLM->getCreatedResources());
        $this->assertSame($length, $this->personalBoardGLM->getCreatedResources()->count());
    }

    public function testRemoveCreatedResourceYetNotRemoved() : void
    {
        // GIVEN

        $createdResource = new CreatedResourceGLM();
        $this->personalBoardGLM->addCreatedResource($createdResource);

        // WHEN

        $this->personalBoardGLM->removeCreatedResource($createdResource);

        // THEN

        $this->assertNotContains($createdResource, $this->personalBoardGLM->getCreatedResources());
        $this->assertNull($createdResource->getPersonalBoardGLM());
    }

    public function testRemoveCreatedResourceAlreadyRemoved() : void
    {
        // GIVEN

        $createdResource = new CreatedResourceGLM();
        $this->personalBoardGLM->addCreatedResource($createdResource);
        $this->personalBoardGLM->removeCreatedResource($createdResource);
        $length = $this->personalBoardGLM->getCreatedResources()->count();

        // WHEN

        $this->personalBoardGLM->removeCreatedResource($createdResource);

        // THEN

        $this->assertNotContains($createdResource, $this->personalBoardGLM->getCreatedResources());
        $this->assertSame($length, $this->personalBoardGLM->getCreatedResources()->count());
    }

    protected function setUp(): void
    {
        $this->personalBoardGLM = new PersonalBoardGLM();
    }
}