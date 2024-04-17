<?php

namespace App\Tests\Game\Glenmore\Integration\Service;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\CardGLM;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerCardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\SelectedResourceGLM;
use App\Entity\Game\Glenmore\TileActivationBonusGLM;
use App\Entity\Game\Glenmore\TileActivationCostGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use App\Repository\Game\Glenmore\CardGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use App\Repository\Game\Glenmore\WarehouseLineGLMRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TileGLMServiceIntegrationTest extends KernelTestCase
{
    private TileGLMService $service;

    private EntityManagerInterface $entityManager;

    private TileGLMRepository $tileGLMRepository;

    private CardGLMRepository $cardGLMRepository;

    private WarehouseLineGLMRepository $warehouseLineGLMRepository;

    private ResourceGLMRepository $resourceGLMRepository;

   /* public function testCanPlaceTileSuccessWithValidPlacement() : void
    {
        //GIVEN
        $tileGLMService = static::getContainer()->get(TileGLMService::class);
        $nbPlayer = 4;
        $game = $this->createGame($nbPlayer);
        $player = $game->getPlayers()->first();
        $tile = new TileGLM();
        $tile->setContainingRoad(true);
        $tile->setContainingRiver(false);
        //WHEN
        $result = $tileGLMService->canPlaceTile(1, 0, $tile, $player);
        //THEN
        $this->assertTrue($result);
    }*/

    public function testGetAmountOfTilesToReplace() : void
    {
        //GIVEN
        $game = $this->createGame(3);
        $player = $game->getPlayers()->first();
        $player->setTurnOfPlayer(false);
        $this->entityManager->persist($player);
        $lastPlayer = $game->getPlayers()->last();
        $lastPlayer->setTurnOfPlayer(true);
        $this->entityManager->persist($lastPlayer);
        $this->entityManager->flush();
        $expectedResult = 1;
        //WHEN
        $result = $this->service->getAmountOfTileToReplace($game->getMainBoard());
        //THEN
        $this->assertSame($expectedResult, $result);
    }

    public function testHasActivationCostWhenNoCost() : void
    {
        //GIVEN
        $game = $this->createGame(3);
        $player = $game->getPlayers()->first();
        $tile = $player->getPersonalBoard()->getPlayerTiles()->first();
        //WHEN
        $result = $this->service->hasActivationCost($tile);
        //THEN
        $this->assertFalse($result);
    }

    public function testHasActivationCostWhenHasCost() : void
    {
        //GIVEN
        $game = $this->createGame(3);
        $player = $game->getPlayers()->first();
        $tile = $this->tileGLMRepository->findOneBy(["id" => 49]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setCoordX(0)
                   ->setCoordY(0)
                   ->setActivated(false)
                   ->setPersonalBoard($player->getPersonalBoard());
        $this->entityManager->persist($playerTile);
        $this->entityManager->flush();
        //WHEN
        $result = $this->service->hasActivationCost($playerTile);
        //THEN
        $this->assertTrue($result);
    }

    public function testHasBuyCostWithLochOichShouldBeTrue() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setPosition(0)
            ->setMainBoardGLM($game->getMainBoard());
        $this->entityManager->persist($boardTile);
        $this->entityManager->flush();
        //WHEN
        $result = $this->service->hasBuyCost($boardTile);
        //THEN
        $this->assertTrue($result);
    }

    public function testHasBuyCostWithDistilleryShouldBeFalse() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        /** @var TileGLM $tile */
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$TILE_NAME_TAVERN]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setPosition(0)
                  ->setMainBoardGLM($game->getMainBoard());
        $this->entityManager->persist($boardTile);
        $this->entityManager->flush();
        //WHEN
        $result = $this->service->hasBuyCost($boardTile);
        //THEN
        $this->assertFalse($result);
    }

    public function testHasBuyCostWhenNoBuyCost() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $tile = $this->tileGLMRepository->findOneBy(["id" => 1]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setPosition(0)
                  ->setMainBoardGLM($game->getMainBoard());
        $this->entityManager->persist($boardTile);
        $this->entityManager->flush();
        //WHEN
        $result = $this->service->hasBuyCost($boardTile);
        //THEN
        $this->assertFalse($result);
    }

    public function testHasBuyCostWhenBuyCost() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_IONA_ABBEY]);
        $boardTile = new BoardTileGLM();
        $boardTile->setTile($tile);
        $boardTile->setPosition(0)
                  ->setMainBoardGLM($game->getMainBoard());
        $this->entityManager->persist($boardTile);
        $this->entityManager->flush();
        //WHEN
        $result = $this->service->hasBuyCost($boardTile);
        //THEN
        $this->assertTrue($result);
    }
    public function testGiveBuyBonusWithSimpleProductionTile() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static::getContainer()->get(TileGLMService::class);
        $tileRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(4);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoard();
        $tile = $tileRepository->findOneBy(["id" => 11]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setActivated(false);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setTile($tile);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setPersonalBoard($personalBoard);
        $entityManager->persist($playerTile);
        $personalBoard->addPlayerTile($playerTile);
        $entityManager->persist($personalBoard);
        $entityManager->flush();
        $expectedAmount = 1;
        $expectedType = GlenmoreParameters::$WHISKY_RESOURCE;
        //WHEN
        $tileGLMService->giveBuyBonus($playerTile);
        //THEN
        $amount = 0;
        $type = null;
        foreach ($personalBoard->getPlayerTiles() as $newTile) {
            if ($newTile === $playerTile) {
                $playerTileResources = $newTile->getPlayerTileResource();
                foreach ($playerTileResources as $playerTileResource) {
                    $amount += $playerTileResource->getQuantity();
                    $type = $playerTileResource->getResource()->getType();
                }
            }
        }
        $this->assertEquals($expectedAmount, $amount);
        $this->assertSame($expectedType, $type);
    }

    public function testGiveBuyBonusWithSimpleProductionTileWhenNoBuyBonus() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static::getContainer()->get(TileGLMService::class);
        $tileRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(4);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoard();
        $tile = $tileRepository->findOneBy(["id" => 1]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setActivated(false);
        $playerTile->setTile($tile);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setPersonalBoard($personalBoard);
        $entityManager->persist($playerTile);
        $personalBoard->addPlayerTile($playerTile);
        $entityManager->persist($personalBoard);
        $entityManager->flush();
        $expectedAmount = 0;
        //WHEN
        $tileGLMService->giveBuyBonus($playerTile);
        //THEN
        $amount = 0;
        $type = null;
        foreach ($personalBoard->getPlayerTiles() as $newTile) {
            if ($newTile === $playerTile) {
                $playerTileResources = $newTile->getPlayerTileResource();
                foreach ($playerTileResources as $playerTileResource) {
                    $amount += $playerTileResource->getQuantity();
                    $type = $playerTileResource->getResource()->getType();
                }
            }
        }
        $this->assertEquals($expectedAmount, $amount);
        $this->assertNull($type);
    }

    public function testGiveBuyBonusWithCard() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static::getContainer()->get(TileGLMService::class);
        $tileRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(4);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoard();
        $tile = $tileRepository->findOneBy(["id" => 51]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setActivated(false);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setTile($tile);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setPersonalBoard($personalBoard);
        $entityManager->persist($playerTile);
        $personalBoard->addPlayerTile($playerTile);
        $entityManager->persist($personalBoard);
        $entityManager->flush();
        $expectedAmountVillager = 1;
        $expectedAmountHat = 3;
        $expectedTypeVillager = GlenmoreParameters::$VILLAGER_RESOURCE;
        $expectedTypeHat = GlenmoreParameters::$HAT_RESOURCE;
        //WHEN
        $tileGLMService->giveBuyBonus($playerTile);
        //THEN
        $amountVillager = 0;
        $amountHat = 0;
        $typeVillager = null;
        $typeHat = null;
        foreach ($personalBoard->getPlayerTiles() as $newTile) {
            if ($newTile === $playerTile) {
                $playerTileResources = $newTile->getPlayerTileResource();
                foreach ($playerTileResources as $playerTileResource) {
                    if ($playerTileResource->getResource()->getType() === $expectedTypeVillager) {
                        $amountVillager += $playerTileResource->getQuantity();
                        $typeVillager = $playerTileResource->getResource()->getType();
                    }
                }
            }
        }
        foreach ($personalBoard->getPlayerCardGLM() as $playerCard) {
            $actualCard = $playerCard->getCard();
            if ($tile->getCard() === $actualCard) {
                $typeHat = $actualCard->getBonus()->getResource()->getType();
                if ($typeHat === $expectedTypeHat) {
                    $amountHat += $actualCard->getBonus()->getAmount();
                }
            }
        }
        $this->assertEquals($expectedAmountVillager, $amountVillager);
        $this->assertSame($expectedTypeVillager, $typeVillager);
        $this->assertEquals($expectedAmountHat, $amountHat);
        $this->assertSame($expectedTypeHat, $typeHat);
    }

    public function testGiveBuyCastleStalker() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static::getContainer()->get(TileGLMService::class);
        $tileRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(4);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoard();
        $tile = $tileRepository->findOneBy(["id" => 27]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setActivated(false);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setTile($tile);
        $playerTile->setPersonalBoard($personalBoard);
        $entityManager->persist($playerTile);
        $personalBoard->addPlayerTile($playerTile);
        $entityManager->persist($personalBoard);
        $entityManager->flush();
        $expectedAmountVillager = 2;
        $expectedAmountHat = 1;
        $expectedTypeVillager = GlenmoreParameters::$VILLAGER_RESOURCE;
        $expectedTypeHat = GlenmoreParameters::$HAT_RESOURCE;
        //WHEN
        $tileGLMService->giveBuyBonus($playerTile);
        //THEN
        $amountVillager = 0;
        $amountHat = 0;
        $typeVillager = null;
        $typeHat = null;
        foreach ($personalBoard->getPlayerTiles() as $newTile) {
            if ($newTile === $playerTile) {
                $playerTileResources = $newTile->getPlayerTileResource();
                foreach ($playerTileResources as $playerTileResource) {
                    if ($playerTileResource->getResource()->getType() === $expectedTypeVillager) {
                        $amountVillager += $playerTileResource->getQuantity();
                        $typeVillager = $playerTileResource->getResource()->getType();
                    }
                }
            }
        }
        foreach ($personalBoard->getPlayerCardGLM() as $playerCard) {
            $actualCard = $playerCard->getCard();
            if ($tile->getCard() === $actualCard) {
                if ($actualCard->getBonus()->getResource()->getType() === $expectedTypeHat) {
                    $amountHat += $actualCard->getBonus()->getAmount();
                    $typeHat = $actualCard->getBonus()->getResource()->getType();
                }
            }
        }
        $this->assertEquals($expectedAmountVillager, $amountVillager);
        $this->assertSame($expectedTypeVillager, $typeVillager);
        $this->assertEquals($expectedAmountHat, $amountHat);
        $this->assertSame($expectedTypeHat, $typeHat);
    }

    public function testCanPlaceTileFailWithInvalidPlacement() : void
    {
        //GIVEN
        $tileGLMService = static::getContainer()->get(TileGLMService::class);
        $nbPlayer = 4;
        $game = $this->createGame($nbPlayer);
        $player = $game->getPlayers()->first();
        $tile = new TileGLM();
        $tile->setContainingRoad(true);
        $tile->setContainingRiver(false);
        //WHEN
        $result = $tileGLMService->canPlaceTile(2, 0, $tile, $player);
        //THEN
        $this->assertFalse($result);
    }

    public function testGetAmountOfTileToReplaceWhenChainIsNotBroken()
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(2);
        $boardTiles = $game->getMainBoard()->getBoardTiles();
        foreach ($boardTiles as $boardTile){
            if($boardTile->getPosition() == 12){
                $game->getMainBoard()->removeBoardTile($boardTile);
            }
        }
        $entityManager->persist($game->getMainBoard());
        $entityManager->flush();
        $expectedResult = 1;
        // WHEN
        $result = $tileGLMService->getAmountOfTileToReplace($game->getMainBoard());
        // THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetAmountOfTileToReplaceWhenChainIsBroken()
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(2);
        $boardTiles = $game->getMainBoard()->getBoardTiles();
        foreach ($boardTiles as $boardTile){
            if($boardTile->getPosition() == 10){
                $boardTile->setPosition(13);
                $entityManager->persist($boardTile);
                $entityManager->persist($game->getMainBoard());
            }
        }
        $entityManager->flush();
        $expectedResult = 3;
        // WHEN
        $result = $tileGLMService->getAmountOfTileToReplace($game->getMainBoard());
        // THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetActiveDrawTileAtTheBeginningShouldBeLevelOne() : void
    {
        //GIVEN
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(5);
        $expectedLevel = 1;
        //WHEN
        $drawTile = $tileGLMService->getActiveDrawTile($game);
        //THEN
        $this->assertSame($expectedLevel, $drawTile->getLevel());
    }

    public function testGetActiveDrawTileShouldBeLevelTwo() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(5);
        $expectedLevel = 2;
        $drawLevelOne = $game->getMainBoard()->getDrawTiles()->get(1);
        $drawLevelOne->getTiles()->clear();
        $entityManager->persist($drawLevelOne);
        $entityManager->persist($game->getMainBoard());
        $entityManager->flush();
        //WHEN
        $drawTile = $tileGLMService->getActiveDrawTile($game);
        //THEN
        $this->assertSame($expectedLevel, $drawTile->getLevel());
    }

    public function testGetActiveDrawWhenLevelTwoIsEmptyButNotLevelOne() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(5);
        $expectedLevel = 1;
        $drawLevelTwo = $game->getMainBoard()->getDrawTiles()->get(2);
        $drawLevelTwo->getTiles()->clear();
        $entityManager->persist($drawLevelTwo);
        $entityManager->persist($game->getMainBoard());
        $entityManager->flush();
        //WHEN
        $drawTile = $tileGLMService->getActiveDrawTile($game);
        //THEN
        $this->assertSame($expectedLevel, $drawTile->getLevel());
    }

    public function testGetActiveDrawShouldBeThree() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(5);
        $expectedLevel = 3;
        $drawLevelOne = $game->getMainBoard()->getDrawTiles()->get(1);
        $drawLevelOne->getTiles()->clear();
        $entityManager->persist($drawLevelOne);
        $entityManager->persist($game->getMainBoard());
        $drawLevelTwo = $game->getMainBoard()->getDrawTiles()->get(2);
        $drawLevelTwo->getTiles()->clear();
        $entityManager->persist($drawLevelTwo);
        $entityManager->persist($game->getMainBoard());
        $entityManager->flush();
        //WHEN
        $drawTile = $tileGLMService->getActiveDrawTile($game);
        //THEN
        $this->assertSame($expectedLevel, $drawTile->getLevel());
    }

    public function testGetActiveDrawShouldBeNull() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(5);
        $drawLevelOne = $game->getMainBoard()->getDrawTiles()->get(1);
        $drawLevelOne->getTiles()->clear();
        $entityManager->persist($drawLevelOne);
        $entityManager->persist($game->getMainBoard());
        $drawLevelTwo = $game->getMainBoard()->getDrawTiles()->get(2);
        $drawLevelTwo->getTiles()->clear();
        $entityManager->persist($drawLevelTwo);
        $entityManager->persist($game->getMainBoard());
        $drawLevelThree = $game->getMainBoard()->getDrawTiles()->get(3);
        $drawLevelThree->getTiles()->clear();
        $entityManager->persist($drawLevelThree);
        $entityManager->persist($game->getMainBoard());
        $entityManager->flush();
        //WHEN
        $drawTile = $tileGLMService->getActiveDrawTile($game);
        //THEN
        $this->assertNull($drawTile);
    }

    public function testGetActivableTilesWithTwoAdjacentTilesNotActivatedWithBonus() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = $tileGLMRepository->findOneBy(["id" => 1]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setActivated(false);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $playerTile2 = new PlayerTileGLM();
        $tile = $tileGLMRepository->findOneBy(["id" => 2]);
        $playerTile2->setTile($tile);
        $playerTile2->setActivated(false);
        $playerTile2->setCoordX(0);
        $playerTile2->setCoordY(0);
        $playerTile2->setPersonalBoard($firstPlayer->getPersonalBoard());
        $playerTile2->addAdjacentTile($playerTile, 0);
        $entityManager->persist($playerTile2);
        $playerTile->addAdjacentTile($playerTile2, 0);
        $entityManager->persist($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile2);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        $expectedResult = new ArrayCollection([$playerTile2, $playerTile]);
        //WHEN
        $result = $tileGLMService->getActivableTiles($playerTile2);
        //THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetActivableTilesWithTwoAdjacentTilesFirstActivatedWithBonus() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = $tileGLMRepository->findOneBy(["id" => 1]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setActivated(true);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $playerTile2 = new PlayerTileGLM();
        $tile = $tileGLMRepository->findOneBy(["id" => 2]);
        $playerTile2->setTile($tile);
        $playerTile2->setActivated(false);
        $playerTile2->setCoordX(0);
        $playerTile2->setCoordY(0);
        $playerTile2->setPersonalBoard($firstPlayer->getPersonalBoard());
        $playerTile2->addAdjacentTile($playerTile, 0);
        $entityManager->persist($playerTile2);
        $playerTile->addAdjacentTile($playerTile2, 0);
        $entityManager->persist($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile2);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        $expectedResult = new ArrayCollection([$playerTile2]);
        //WHEN
        $result = $tileGLMService->getActivableTiles($playerTile2);
        //THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetActivableTilesWithTwoAdjacentTilesNotActivatedNewHasNoBonus() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = $tileGLMRepository->findOneBy(["id" => 1]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setActivated(false);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $playerTile2 = new PlayerTileGLM();
        $tile = $tileGLMRepository->findOneBy(["id" => 37]);
        $playerTile2->setTile($tile);
        $playerTile2->setActivated(false);
        $playerTile2->setCoordX(0);
        $playerTile2->setCoordY(0);
        $playerTile2->setPersonalBoard($firstPlayer->getPersonalBoard());
        $playerTile2->addAdjacentTile($playerTile, 0);
        $entityManager->persist($playerTile2);
        $playerTile->addAdjacentTile($playerTile2, 0);
        $entityManager->persist($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile2);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        $expectedResult = new ArrayCollection([$playerTile]);
        //WHEN
        $result = $tileGLMService->getActivableTiles($playerTile2);
        //THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetActivableTilesWithTilesNotAdjacentNotActivatedWithBonus() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = $tileGLMRepository->findOneBy(["id" => 1]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setActivated(false);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $playerTile2 = new PlayerTileGLM();
        $tile = $tileGLMRepository->findOneBy(["id" => 2]);
        $playerTile2->setTile($tile);
        $playerTile2->setActivated(false);
        $playerTile2->setCoordX(0);
        $playerTile2->setCoordY(0);
        $playerTile2->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile2);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile2);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        $expectedResult = new ArrayCollection([$playerTile2]);
        //WHEN
        $result = $tileGLMService->getActivableTiles($playerTile2);
        //THEN
        $this->assertEquals($expectedResult, $result);
    }


    public function testGetActivableTilesWithTilesNotAdjacentNotActivatedWithBonusAndPlayerBoughtLochOichInLast() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $startVillage = $firstPlayer->getPersonalBoard()->getPlayerTiles()->first();
        $tile = $tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        $oich = new PlayerTileGLM();
        $oich->setTile($tile);
        $oich->setActivated(false);
        $oich->setCoordX(0);
        $oich->setCoordY(0);
        $oich->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($oich);
        $card = $this->cardGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        $card = new PlayerCardGLM($firstPlayer->getPersonalBoard(), $card);
        $this->entityManager->persist($card);
        $firstPlayer->getPersonalBoard()->addPlayerCardGLM($card);
        $tile = $tileGLMRepository->findOneBy(["id" => 1]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setActivated(false);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $playerTile2 = new PlayerTileGLM();
        $tile = $tileGLMRepository->findOneBy(["id" => 2]);
        $playerTile2->setTile($tile);
        $playerTile2->setActivated(false);
        $playerTile2->setCoordX(0);
        $playerTile2->setCoordY(0);
        $playerTile2->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile2);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile2);
        $firstPlayer->getPersonalBoard()->addPlayerTile($oich);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        $expectedResult = new ArrayCollection([$startVillage, $playerTile, $playerTile2]);
        //WHEN
        $result = $tileGLMService->getActivableTiles($playerTile2);
        //THEN
        foreach ($result as $item) {
            $this->assertContains($item, $expectedResult);
        }
    }

    public function testGetActivableTilesWithTilesNotAdjacentNotActivatedWithBonusAndPlayerBoughtLochOichNotLast() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = $tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        $card = $this->cardGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        $card = new PlayerCardGLM($firstPlayer->getPersonalBoard(), $card);
        $this->entityManager->persist($card);
        $firstPlayer->getPersonalBoard()->addPlayerCardGLM($card);
        $oich = new PlayerTileGLM();
        $oich->setTile($tile);
        $oich->setActivated(false);
        $oich->setCoordX(0);
        $oich->setCoordY(0);
        $oich->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($oich);
        $tile = $tileGLMRepository->findOneBy(["id" => 1]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setActivated(false);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $playerTile2 = new PlayerTileGLM();
        $tile = $tileGLMRepository->findOneBy(["id" => 2]);
        $playerTile2->setTile($tile);
        $playerTile2->setActivated(false);
        $playerTile2->setCoordX(0);
        $playerTile2->setCoordY(0);
        $playerTile2->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile2);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($oich);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile2);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        $expectedResult = new ArrayCollection([$playerTile2]);
        //WHEN
        $result = $tileGLMService->getActivableTiles($playerTile2);
        //THEN
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetActivableTilesWithTilesAdjacentActivatedWithBonus() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = $tileGLMRepository->findOneBy(["id" => 1]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setActivated(true);
        $playerTile->setCoordX(0);
        $playerTile->setCoordY(0);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $playerTile2 = new PlayerTileGLM();
        $tile = $tileGLMRepository->findOneBy(["id" => 2]);
        $playerTile2->setTile($tile);
        $playerTile2->setActivated(true);
        $playerTile2->setCoordX(0);
        $playerTile2->setCoordY(0);
        $playerTile2->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile2);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile2);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        //WHEN
        $result = $tileGLMService->getActivableTiles($playerTile2);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetMovementPointsWithTwoTilesOfOne() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $glmService = static::getContainer()->get(GLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile1 = $tileGLMRepository->findOneBy(["id" => 68]);
        $tile2 = $tileGLMRepository->findOneBy(["id" => 69]);
        $tiles = [$tile1, $tile2];
        for ($i = 0; $i < 2; ++$i) {
            $playerTile = new PlayerTileGLM();
            $playerTile->setTile($tiles[$i]);
            $playerTile->setCoordX($i);
            $playerTile->setCoordY($i);
            $playerTile->setActivated(false);
            $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
            $entityManager->persist($playerTile);
            $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
            $entityManager->persist($firstPlayer->getPersonalBoard());
            $entityManager->flush();
            $tileGLMService->giveBuyBonus($playerTile);
        }
        $expectedResult = 2;
        //WHEN
        $result = $tileGLMService->getMovementPoints($firstPlayer);
        //THEN
        $this->assertSame($expectedResult, $result);
    }

    public function testGetMovementPointsWithSameTile() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $glmService = static::getContainer()->get(GLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile1 = $tileGLMRepository->findOneBy(["id" => 68]);
        $tiles = [$tile1];
        for ($i = 0; $i < 2; ++$i) {
            $playerTile = new PlayerTileGLM();
            $playerTile->setTile($tiles[0]);
            $playerTile->setCoordX($i);
            $playerTile->setCoordY($i);
            $playerTile->setActivated(false);
            $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
            $entityManager->persist($playerTile);
            if ($i == 0) {
                $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
            }
            $entityManager->persist($firstPlayer->getPersonalBoard());
            $entityManager->flush();
            $tileGLMService->giveBuyBonus($playerTile);
        }
        $expectedResult = 1;
        //WHEN
        $result = $tileGLMService->getMovementPoints($firstPlayer);
        //THEN
        $this->assertSame($expectedResult, $result);
    }

    public function testGetMovementPointsWithTwoTilesOfDifferentAndOneNoMovement() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $glmService = static::getContainer()->get(GLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile1 = $tileGLMRepository->findOneBy(["id" => 68]);
        $tile2 = $tileGLMRepository->findOneBy(["id" => 69]);
        $tile3 = $tileGLMRepository->findOneBy(["id" => 67]);

        $tiles = [$tile1, $tile2, $tile3];
        for ($i = 0; $i <= 2; ++$i) {
            $playerTile = new PlayerTileGLM();
            $playerTile->setTile($tiles[$i]);
            $playerTile->setCoordX($i);
            $playerTile->setCoordY($i);
            $playerTile->setActivated(false);
            $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
            $entityManager->persist($playerTile);
            $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
            $entityManager->persist($firstPlayer->getPersonalBoard());
            $entityManager->flush();
            $tileGLMService->giveBuyBonus($playerTile);
        }
        $expectedResult = 2;
        //WHEN
        $result = $tileGLMService->getMovementPoints($firstPlayer);
        //THEN
        $this->assertSame($expectedResult, $result);
    }

    public function testGetMovementPointsWithTwoTilesAdjacent() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $glmService = static::getContainer()->get(GLMService::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile1 = $tileGLMRepository->findOneBy(["id" => 68]);
        $tile2 = $tileGLMRepository->findOneBy(["id" => 69]);
        $tiles = [$tile1, $tile2];
        for ($i = 1; $i <= 2; ++$i) {
            $playerTile = new PlayerTileGLM();
            $playerTile->setTile($tiles[$i - 1]);
            $playerTile->setCoordX($i);
            $playerTile->setCoordY($i);
            $playerTile->setActivated(false);
            $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
            $entityManager->persist($playerTile);
            if ($i == 2) {
                $playerTile->addAdjacentTile($firstPlayer->getPersonalBoard()->getPlayerTiles()->get(1), 0);
                $firstPlayer->getPersonalBoard()->getPlayerTiles()->get(1)->addAdjacentTile($playerTile, 4);
            }
            $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
            $entityManager->persist($firstPlayer->getPersonalBoard());
            $entityManager->flush();
            $tileGLMService->giveBuyBonus($playerTile);
        }
        $expectedResult = 2;
        //WHEN
        $result = $tileGLMService->getMovementPoints($firstPlayer);
        //THEN
        $this->assertSame($expectedResult, $result);
    }

    public function testActivateTileWhenNotEnoughResources() : void
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileGLM();
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $tile->setType(GlenmoreParameters::$TILE_TYPE_GREEN);
        $tile->setName("TEST");
        $tile->setContainingRoad(false);
        $tile->setContainingRiver(false);
        $tile->setLevel(GlenmoreParameters::$TILE_LEVEL_ONE);
        $playerTile->setCoordY(0);
        $playerTile->setCoordX(0);
        $playerTile->setActivated(false);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $resourcePrice = new ResourceGLM();
        $resourcePrice->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $resourcePrice->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($resourcePrice);
        $activationPrice = new TileActivationCostGLM();
        $activationPrice->setResource($resourcePrice);
        $activationPrice->setPrice(2);
        $entityManager->persist($activationPrice);
        $tile->addActivationPrice($activationPrice);
        $entityManager->persist($tile);
        $entityManager->flush();
        // THEN
        $this->expectException("Exception");
        // WHEN
        $collection = new ArrayCollection((array)$tileGLMService->getActivableTiles($firstPlayer->getPersonalBoard()->getPlayerTiles()->last()));
        $tileGLMService->activateBonus($playerTile, $firstPlayer, $collection);
    }

    public function testActivateTileWhenNotEnoughResourcesOfGoodType() : void
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileGLM();
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $tile->setType(GlenmoreParameters::$TILE_TYPE_GREEN);
        $tile->setName("TEST2");
        $tile->setContainingRoad(false);
        $tile->setContainingRiver(false);
        $tile->setLevel(GlenmoreParameters::$TILE_LEVEL_TWO);
        $playerTile->setCoordY(0);
        $playerTile->setCoordX(1);
        $playerTile->setActivated(false);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $resourcePrice = new ResourceGLM();
        $resourcePrice->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $resourcePrice->setColor(GlenmoreParameters::$COLOR_GREEN);
        $activationPrice = new TileActivationCostGLM();
        $activationPrice->setResource($resourcePrice);
        $activationPrice->setPrice(2);
        $tile->addActivationPrice($activationPrice);
        $playerResource = new ResourceGLM();
        $playerResource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerResource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($playerResource);
        $selectedResource->setQuantity(1);
        $selectedResource->setPlayerTile($playerTile);
        $firstPlayer->getPersonalBoard()->addSelectedResource($selectedResource);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->persist($tile);
        $entityManager->persist($playerTile);
        $entityManager->persist($resourcePrice);
        $entityManager->persist($activationPrice);
        $entityManager->persist($selectedResource);
        $entityManager->persist($playerResource);
        $entityManager->flush();
        // THEN
        $this->expectException("Exception");
        // WHEN
        $collection = new ArrayCollection((array)$tileGLMService->getActivableTiles($firstPlayer->getPersonalBoard()->getPlayerTiles()->last()));
        $tileGLMService->activateBonus($playerTile, $firstPlayer, $collection);
    }

    public function testActivateTileWhenEnoughResourcesButWrongType() : void
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileGLM();
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $tile->setType(GlenmoreParameters::$TILE_TYPE_GREEN);
        $tile->setName("TEST2");
        $tile->setContainingRoad(false);
        $tile->setContainingRiver(false);
        $tile->setLevel(GlenmoreParameters::$TILE_LEVEL_TWO);
        $playerTile->setCoordY(0);
        $playerTile->setCoordX(1);
        $playerTile->setActivated(false);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $resourcePrice = new ResourceGLM();
        $resourcePrice->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $resourcePrice->setColor(GlenmoreParameters::$COLOR_GREEN);
        $activationPrice = new TileActivationCostGLM();
        $activationPrice->setResource($resourcePrice);
        $activationPrice->setPrice(2);
        $tile->addActivationPrice($activationPrice);
        $playerResource = new ResourceGLM();
        $playerResource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerResource->setColor(GlenmoreParameters::$COLOR_BROWN);
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($playerResource);
        $selectedResource->setQuantity(2);
        $selectedResource->setPlayerTile($playerTile);
        $firstPlayer->getPersonalBoard()->addSelectedResource($selectedResource);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->persist($tile);
        $entityManager->persist($playerTile);
        $entityManager->persist($resourcePrice);
        $entityManager->persist($activationPrice);
        $entityManager->persist($selectedResource);
        $entityManager->persist($playerResource);
        $entityManager->flush();
        // THEN
        $this->expectException("Exception");
        // WHEN
        $collection = new ArrayCollection((array)$tileGLMService->getActivableTiles($firstPlayer->getPersonalBoard()->getPlayerTiles()->last()));
        $tileGLMService->activateBonus($playerTile, $firstPlayer, $collection);
    }

    public function testActivateTileWhenTooMuchResourcesOnTile() : void
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileGLM();
        $playerTile = new PlayerTileGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $resource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($resource);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setPlayer($firstPlayer);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setQuantity(GlenmoreParameters::$MAX_RESOURCES_PER_TILE);
        $entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);

        $playerTile->setTile($tile);
        $tile->setType(GlenmoreParameters::$TILE_TYPE_GREEN);
        $tile->setName("TEST2");
        $tile->setContainingRoad(false);
        $tile->setContainingRiver(false);
        $tile->setLevel(GlenmoreParameters::$TILE_LEVEL_TWO);
        $playerTile->setCoordY(0);
        $playerTile->setCoordX(1);
        $playerTile->setActivated(false);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $resourcePrice = new ResourceGLM();
        $resourcePrice->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $resourcePrice->setColor(GlenmoreParameters::$COLOR_GREEN);
        $activationPrice = new TileActivationCostGLM();
        $activationPrice->setResource($resourcePrice);
        $activationPrice->setPrice(2);
        $tile->addActivationPrice($activationPrice);
        $playerResource = new ResourceGLM();
        $playerResource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerResource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($playerResource);
        $selectedResource->setQuantity(1);
        $selectedResource->setPlayerTile($playerTile);
        $firstPlayer->getPersonalBoard()->addSelectedResource($selectedResource);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->persist($tile);
        $entityManager->persist($playerTile);
        $entityManager->persist($resourcePrice);
        $entityManager->persist($activationPrice);
        $entityManager->persist($selectedResource);
        $entityManager->persist($playerResource);
        $entityManager->flush();
        // THEN
        $this->expectException("Exception");
        // WHEN
        $collection = new ArrayCollection((array)$tileGLMService->getActivableTiles($firstPlayer->getPersonalBoard()->getPlayerTiles()->last()));
        $tileGLMService->activateBonus($playerTile, $firstPlayer, $collection);
    }

    /*public function testActivateTileWhenTileNotOfTypeBrownAndNeedNoResources() : void
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileGLM();
        $tile->setType(GlenmoreParameters::$TILE_TYPE_GREEN);
        $tileBonus = new TileActivationBonusGLM();
        $bonusResource = new ResourceGLM();
        $bonusResource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $bonusResource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($bonusResource);
        $tileBonus->setResource($bonusResource);
        $tileBonus->setAmount(1);
        $entityManager->persist($tileBonus);
        $tile->addActivationBonus($tileBonus);
        $entityManager->persist($tile);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $tile->setName("TEST");
        $tile->setContainingRoad(false);
        $tile->setContainingRiver(false);
        $tile->setLevel(GlenmoreParameters::$TILE_LEVEL_ONE);
        $entityManager->persist($tile);
        $playerTile->setCoordY(0);
        $playerTile->setCoordX(1);
        $playerTile->setActivated(false);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        // WHEN
        $collection = new ArrayCollection((array)$tileGLMService->getActivableTiles($firstPlayer->getPersonalBoard()->getPlayerTiles()->last()));
        $tileGLMService->activateBonus($playerTile, $firstPlayer, $collection);
        //THEN
        $resourceArray = new ArrayCollection();
        foreach ($playerTile->getPlayerTileResource() as $resource){
            $resourceArray->add($resource->getResource());
        }
        $this->assertContainsEquals($bonusResource ,$resourceArray);
        $this->assertTrue($playerTile->isActivated());
    }

    public function testActivateTileWhenTileNotOfTypeBrownAndNeedResources() : void
    {
        // GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileGLM();
        $tile->setType(GlenmoreParameters::$TILE_TYPE_GREEN);
        $tileBonus = new TileActivationBonusGLM();
        $bonusResource = new ResourceGLM();
        $bonusResource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $bonusResource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($bonusResource);
        $tileBonus->setResource($bonusResource);
        $tileBonus->setAmount(1);
        $entityManager->persist($tileBonus);
        $tile->addActivationBonus($tileBonus);
        $resourcePrice = new ResourceGLM();
        $resourcePrice->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $resourcePrice->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($resourcePrice);
        $activationPrice = new TileActivationCostGLM();
        $activationPrice->setResource($resourcePrice);
        $activationPrice->setPrice(2);
        $entityManager->persist($activationPrice);
        $tile->addActivationPrice($activationPrice);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $tile->setName("TEST");
        $tile->setContainingRoad(false);
        $tile->setContainingRiver(false);
        $tile->setLevel(GlenmoreParameters::$TILE_LEVEL_ONE);
        $entityManager->persist($tile);
        $playerResource = new ResourceGLM();
        $playerResource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerResource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($playerResource);
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($playerResource);
        $selectedResource->setQuantity(2);
        $selectedResource->setPlayerTile($playerTile);
        $entityManager->persist($selectedResource);
        $firstPlayer->getPersonalBoard()->addSelectedResource($selectedResource);
        $playerTile->setCoordY(0);
        $playerTile->setCoordX(0);
        $playerTile->setActivated(false);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        $expectedResourcesCount = $selectedResource->getQuantity() - $activationPrice->getPrice();
        // WHEN
        $collection = new ArrayCollection((array)$tileGLMService->getActivableTiles($firstPlayer->getPersonalBoard()->getPlayerTiles()->last()));
        $tileGLMService->activateBonus($playerTile, $firstPlayer, $collection);
        //THEN
        $resourceArray = new ArrayCollection();
        foreach ($playerTile->getPlayerTileResource() as $resource){
            $resourceArray->add($resource->getResource());
        }
        $this->assertContainsEquals($bonusResource ,$resourceArray);
        $this->assertEquals($expectedResourcesCount, $selectedResource->getQuantity());
        $this->assertTrue($playerTile->isActivated());
    }

    public function testActivateTileWhenTileOfTypeBrownWithOneCase() : void
    {
        // GIVEN
        $pointsToGive = 5;
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileGLM();
        $tile->setType(GlenmoreParameters::$TILE_TYPE_BROWN);
        $tileBonus = new TileActivationBonusGLM();
        $bonusResource = new ResourceGLM();
        $bonusResource->setType(GlenmoreParameters::$POINT_RESOURCE);
        $bonusResource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($bonusResource);
        $tileBonus->setResource($bonusResource);
        $tileBonus->setAmount($pointsToGive);
        $entityManager->persist($tileBonus);
        $tile->addActivationBonus($tileBonus);
        $resourcePrice = new ResourceGLM();
        $resourcePrice->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $resourcePrice->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($resourcePrice);
        $activationPrice = new TileActivationCostGLM();
        $activationPrice->setResource($resourcePrice);
        $activationPrice->setPrice(2);
        $entityManager->persist($activationPrice);
        $tile->addActivationPrice($activationPrice);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $tile->setName("TEST");
        $tile->setContainingRoad(false);
        $tile->setContainingRiver(false);
        $tile->setLevel(GlenmoreParameters::$TILE_LEVEL_ONE);
        $entityManager->persist($tile);
        $playerResource = new ResourceGLM();
        $playerResource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerResource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($playerResource);
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($playerResource);
        $selectedResource->setQuantity(2);
        $selectedResource->setPlayerTile($playerTile);
        $entityManager->persist($selectedResource);
        $firstPlayer->getPersonalBoard()->addSelectedResource($selectedResource);
        $playerTile->setCoordY(0);
        $playerTile->setCoordX(0);
        $playerTile->setActivated(false);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        $expectedPoints = $pointsToGive;
        // WHEN
        $collection = new ArrayCollection((array)$tileGLMService->getActivableTiles($firstPlayer->getPersonalBoard()->getPlayerTiles()->last()));
        $tileGLMService->activateBonus($playerTile, $firstPlayer, $collection);
        //THEN
        $this->assertEquals($expectedPoints, $firstPlayer->getPoints());
        $this->assertTrue($playerTile->isActivated());
    }*/

    /*public function testActivateTileWhenTileOfTypeBrownWithMultipleCase() : void
    {
        // GIVEN
        $pointsToGive = 3;
        $pointsToGive2 = 5;
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMService = static ::getContainer()->get(TileGLMService::class);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileGLM();
        $tile->setType(GlenmoreParameters::$TILE_TYPE_BROWN);
        $tileBonus = new TileActivationBonusGLM();
        $bonusResource = new ResourceGLM();
        $bonusResource->setType(GlenmoreParameters::$POINT_RESOURCE);
        $bonusResource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($bonusResource);
        $tileBonus->setResource($bonusResource);
        $tileBonus->setAmount($pointsToGive);
        $entityManager->persist($tileBonus);
        $tile->addActivationBonus($tileBonus);
        $tileBonus2 = new TileActivationBonusGLM();
        $bonusResource2 = new ResourceGLM();
        $bonusResource2->setType(GlenmoreParameters::$POINT_RESOURCE);
        $bonusResource2->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($bonusResource2);
        $tileBonus2->setResource($bonusResource2);
        $tileBonus2->setAmount($pointsToGive2);
        $entityManager->persist($tileBonus2);
        $tile->addActivationBonus($tileBonus2);
        $resourcePrice = new ResourceGLM();
        $resourcePrice->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $resourcePrice->setColor(GlenmoreParameters::$COLOR_GREEN);
        $entityManager->persist($resourcePrice);
        $activationPrice = new TileActivationCostGLM();
        $activationPrice->setResource($resourcePrice);
        $activationPrice->setPrice(0);
        $entityManager->persist($activationPrice);
        $tile->addActivationPrice($activationPrice);
        $resourcePrice2 = new ResourceGLM();
        $resourcePrice2->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $resourcePrice2->setColor(GlenmoreParameters::$COLOR_BROWN);
        $entityManager->persist($resourcePrice2);
        $activationPrice2 = new TileActivationCostGLM();
        $activationPrice2->setResource($resourcePrice2);
        $activationPrice2->setPrice(0);
        $entityManager->persist($activationPrice2);
        $tile->addActivationPrice($activationPrice2);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $tile->setName("TEST");
        $tile->setContainingRoad(false);
        $tile->setContainingRiver(false);
        $tile->setLevel(GlenmoreParameters::$TILE_LEVEL_ONE);
        $entityManager->persist($tile);
        $playerResource = new ResourceGLM();
        $playerResource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerResource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $playerResource2 = new ResourceGLM();
        $playerResource2->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerResource2->setColor(GlenmoreParameters::$COLOR_BROWN);
        $entityManager->persist($playerResource2);
        $entityManager->persist($playerResource);
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($playerResource);
        $selectedResource->setQuantity(2);
        $selectedResource->setPlayerTile($playerTile);
        $entityManager->persist($selectedResource);
        $selectedResource2 = new SelectedResourceGLM();
        $selectedResource2->setResource($playerResource);
        $selectedResource2->setQuantity(2);
        $selectedResource2->setPlayerTile($playerTile);
        $entityManager->persist($selectedResource2);
        $firstPlayer->getPersonalBoard()->addSelectedResource($selectedResource);
        $firstPlayer->getPersonalBoard()->addSelectedResource($selectedResource2);
        $playerTile->setCoordY(0);
        $playerTile->setCoordX(0);
        $playerTile->setActivated(false);
        $playerTile->setPersonalBoard($firstPlayer->getPersonalBoard());
        $entityManager->persist($playerTile);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);
        $entityManager->persist($firstPlayer->getPersonalBoard());
        $entityManager->flush();
        $expectedPoints = $pointsToGive;
        // WHEN
 $collection = new ArrayCollection((array)$tileGLMService->getActivableTiles($firstPlayer->getPersonalBoard()->getPlayerTiles()->last()));
        $tileGLMService->activateBonus($playerTile, $firstPlayer, $collection);
            //THEN
        $this->assertEquals($expectedPoints, $firstPlayer->getPoints());
        $this->assertTrue($playerTile->isActivated());
    }*/

    public function testCanBuyTileWhenPlayerHasMoneyAndResourceAvailable() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_IONA_ABBEY]);
        //WHEN
        $result = $this->service->canBuyTile($tile, $player);
        //THEN
        $this->assertTrue($result);
    }

    public function testCanBuyTileWhenPlayerHasNotEnoughMoneyAndResourceAvailable() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoard()->setMoney(5);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_IONA_ABBEY]);
        //WHEN
        $result = $this->service->canBuyTile($tile, $player);
        //THEN
        $this->assertFalse($result);
    }

    public function testCanBuyTileWhenPlayerHasEnoughMoneyButResourceNotAvailable() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $resource = $this->resourceGLMRepository->findOneBy(["color" => GlenmoreParameters::$COLOR_WHITE]);
        $line = $this->warehouseLineGLMRepository->findOneBy(
            ["warehouseGLM" => $game->getMainBoard()->getWarehouse(), "resource" => $resource]
        );
        $line->setQuantity(3);
        $this->entityManager->persist($line);
        $this->entityManager->flush();
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_IONA_ABBEY]);
        //WHEN
        $result = $this->service->canBuyTile($tile, $player);
        //THEN
        $this->assertFalse($result);
    }

    public function testCanBuyLochOichWithMoneyAndNoResources() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        //WHEN
        $result = $this->service->canBuyLochOich($tile, $player);
        //THEN
        $this->assertTrue($result);
    }

    public function testCanBuyLochOichWithMoneyAndOneResource() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $resource = $game->getMainBoard()->getWarehouse()->getWarehouseLine()->first()->getResource();
        $player = $game->getPlayers()->first();
        $playerTile = $player->getPersonalBoard()->getPlayerTiles()->first();
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setPlayer($player);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setResource($resource);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $this->entityManager->flush();
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        //WHEN
        $result = $this->service->canBuyLochOich($tile, $player);
        //THEN
        $this->assertTrue($result);
    }

    public function testCanBuyLochOichWithMoneyAndOneResourceOfQUantityZero() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $resource = $game->getMainBoard()->getWarehouse()->getWarehouseLine()->first()->getResource();
        $player = $game->getPlayers()->first();
        $playerTile = $player->getPersonalBoard()->getPlayerTiles()->first();
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setPlayer($player);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setQuantity(0);
        $playerTileResource->setResource($resource);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $this->entityManager->flush();
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        //WHEN
        $result = $this->service->canBuyLochOich($tile, $player);
        //THEN
        $this->assertTrue($result);
    }


    public function testCanBuyLochOichWhenPlayerHasNotEnoughMoneyAndResourceAvailable() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoard()->setMoney(0);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        //WHEN
        $result = $this->service->canBuyLochOich($tile, $player);
        //THEN
        $this->assertFalse($result);
    }

    public function testCanBuyLochOichWhenPlayerHasEnoughMoneyButResourcesNotAvailable() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $resource = $this->resourceGLMRepository->findOneBy(["color" => GlenmoreParameters::$COLOR_WHITE]);
        $playerTile = $player->getPersonalBoard()->getPlayerTiles()->first();
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setPlayer($player);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setResource($resource);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        foreach ($game->getMainBoard()->getWarehouse()->getWarehouseLine() as $line) {
            $line->setQuantity(3);
            $this->entityManager->persist($line);
        }
        $this->entityManager->flush();
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        //WHEN
        $result = $this->service->canBuyLochOich($tile, $player);
        //THEN
        $this->assertFalse($result);
    }

    public function testCanBuyLochOichWhenPlayerHasMultipleDifferentResources() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $resource = $this->resourceGLMRepository->findOneBy(["color" => GlenmoreParameters::$COLOR_WHITE]);
        $playerTile = $player->getPersonalBoard()->getPlayerTiles()->first();
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setPlayer($player);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setResource($resource);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $resource = $this->resourceGLMRepository->findOneBy(["color" => GlenmoreParameters::$COLOR_BROWN]);
        $playerTile = $player->getPersonalBoard()->getPlayerTiles()->first();
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setPlayer($player);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setResource($resource);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $this->entityManager->flush();
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        //WHEN
        $result = $this->service->canBuyLochOich($tile, $player);
        //THEN
        $this->assertTrue($result);
    }

    public function testCanBuyLochOichWhenPlayerHasMultipleSameResources() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $resource = $this->resourceGLMRepository->findOneBy(["color" => GlenmoreParameters::$COLOR_WHITE]);
        $playerTile = $player->getPersonalBoard()->getPlayerTiles()->first();
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setPlayer($player);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setResource($resource);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $playerTile = $player->getPersonalBoard()->getPlayerTiles()->first();
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setPlayer($player);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setResource($resource);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $this->entityManager->flush();
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        //WHEN
        $result = $this->service->canBuyLochOich($tile, $player);
        //THEN
        $this->assertTrue($result);
    }

    public function testCanBuyTileWithSelectedResourcesWhenTwoResourcesAndLochOich() : void
    {
        //GIVEN
        $game = $this->createGame(1);
        $lochOich = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        $player = $game->getPlayers()->first();
        for ($i = 1; $i <= 2; ++$i) {
            $resource = $this->resourceGLMRepository->findOneBy(["id" => $i]);
            $selectedResource = new SelectedResourceGLM();
            $selectedResource->setResource($resource);
            $selectedResource->setQuantity(1);
            $selectedResource->setPersonalBoardGLM($player->getPersonalBoard());
            $selectedResource->setPlayerTile($player->getPersonalBoard()->getPlayerTiles()->first());
            $this->entityManager->persist($selectedResource);
            $player->getPersonalBoard()->addSelectedResource($selectedResource);
            $this->entityManager->persist($player->getPersonalBoard());
        }
        $this->entityManager->flush();
        //WHEN
        $result = $this->service->canBuyTileWithSelectedResources($player, $lochOich);
        //THEN
        $this->assertTrue($result);
    }

    public function testCanBuyTileWithSelectedResourcesWhenOneResourceAndLochOich() : void
    {
        //GIVEN
        $game = $this->createGame(1);
        $lochOich = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_LOCH_OICH]);
        $player = $game->getPlayers()->first();
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($resource);
        $selectedResource->setQuantity(1);
        $selectedResource->setPersonalBoardGLM($player->getPersonalBoard());
        $selectedResource->setPlayerTile($player->getPersonalBoard()->getPlayerTiles()->first());
        $this->entityManager->persist($selectedResource);
        $player->getPersonalBoard()->addSelectedResource($selectedResource);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        //WHEN
        $result = $this->service->canBuyTileWithSelectedResources($player, $lochOich);
        //THEN
        $this->assertFalse($result);
    }

    public function testCanBuyTileWithSelectedResourcesWhenNoResource() : void
    {
        //GIVEN
        $game = $this->createGame(1);
        $tile = $this->tileGLMRepository->findOneBy(["name" => GlenmoreParameters::$CARD_IONA_ABBEY]);
        $player = $game->getPlayers()->first();
        //WHEN
        $result = $this->service->canBuyTileWithSelectedResources($player, $tile);
        //THEN
        $this->assertFalse($result);
    }

    public function testCanBuyTileWithSelectedResourcesWhenGoodResource() : void
    {
        //GIVEN
        $game = $this->createGame(1);
        $tile = $this->tileGLMRepository->findOneBy(["id" => 55]);
        $player = $game->getPlayers()->first();
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($resource);
        $selectedResource->setQuantity(1);
        $selectedResource->setPersonalBoardGLM($player->getPersonalBoard());
        $selectedResource->setPlayerTile($player->getPersonalBoard()->getPlayerTiles()->first());
        $this->entityManager->persist($selectedResource);
        $player->getPersonalBoard()->addSelectedResource($selectedResource);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        //WHEN
        $result = $this->service->canBuyTileWithSelectedResources($player, $tile);
        //THEN
        $this->assertTrue($result);
    }

    public function testCanBuyTileWithSelectedResourcesWhenBadResource() : void
    {
        //GIVEN
        $game = $this->createGame(1);
        $tile = $this->tileGLMRepository->findOneBy(["id" => 55]);
        $player = $game->getPlayers()->first();
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 2]);
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($resource);
        $selectedResource->setQuantity(1);
        $selectedResource->setPersonalBoardGLM($player->getPersonalBoard());
        $selectedResource->setPlayerTile($player->getPersonalBoard()->getPlayerTiles()->first());
        $this->entityManager->persist($selectedResource);
        $player->getPersonalBoard()->addSelectedResource($selectedResource);
        $this->entityManager->persist($player->getPersonalBoard());
        $this->entityManager->flush();
        //WHEN
        $result = $this->service->canBuyTileWithSelectedResources($player, $tile);
        //THEN
        $this->assertFalse($result);
    }

    public function testActivateBonusWithIonaAbbey() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$CARD_IONA_ABBEY);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testActivateBonusWhenNoBonus() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$CARD_LOCH_NESS);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testActivateBonusWhenFairAndNoResourceSelected() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_FAIR);
        $activableTiles = new ArrayCollection([$playerTile]);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
    }

    public function testActivateBonusWhenFairAndOneResourceSelected() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_FAIR);
        $this->selectResource($playerTile);
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testActivateBonusWhenFairAndTwoResourceSelected() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_FAIR);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testActivateBonusWhenFairAndThreeResourceSelected() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_FAIR);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testActivateBonusWhenFairAndFourResourceSelected() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_FAIR);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testActivateBonusWhenFairAndFiveResourceSelected() : void
    {
        //GIVEN
        $game = $this->createGame(3);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_FAIR);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }
    public function testActivateBonusWhenGrocerAndNoResourceSelected() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_GROCER);
        $this->selectResource($playerTile);
        $activableTiles = new ArrayCollection([$playerTile]);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
    }

    public function testActivateBonusWhenGrocerAndThreeResourceSelected() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_GROCER);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $resource = $this->resourceGLMRepository->findOneBy(["id" => 1]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $this->entityManager->flush();
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testActivateBonusBridge() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_BRIDGE);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $this->selectResource($playerTile);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testActivateCattleButFull() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_CATTLE);
        $resource = $this->resourceGLMRepository->findOneBy(["color" => GlenmoreParameters::$COLOR_BROWN]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(3);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $this->entityManager->flush();
        $activableTiles = new ArrayCollection([$playerTile]);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
    }

    public function testActivateCattleWhenExistsResourceOnTile() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_CATTLE);
        $resource = $this->resourceGLMRepository->findOneBy(["color" => GlenmoreParameters::$COLOR_BROWN]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $this->entityManager->flush();
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->assertSame(2, $playerTile->getPlayerTileResource()->first()->getQuantity());
    }

    public function testActivateCattleWhenNotExistsResourceOnTile() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_CATTLE);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->assertSame(1, $playerTile->getPlayerTileResource()->first()->getQuantity());
    }

    public function testActivateButcherWhenNoResourceSelected()
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_BUTCHER);
        $resource = $this->resourceGLMRepository->findOneBy(["color" => GlenmoreParameters::$COLOR_WHITE]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $this->entityManager->flush();
        $this->selectResource($playerTile, 3);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }

    public function testActivateDistilleryWhenNoResourceSelected()
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_DISTILLERY);
        $resource = $this->resourceGLMRepository->findOneBy(["color" => GlenmoreParameters::$COLOR_YELLOW]);
        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);
        $playerTileResource->setPlayer($player);
        $this->entityManager->persist($playerTileResource);
        $playerTile->addPlayerTileResource($playerTileResource);
        $this->entityManager->persist($playerTile);
        $this->entityManager->flush();
        $this->selectResource($playerTile, 6);
        $activableTiles = new ArrayCollection([$playerTile]);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
        //THEN
        $this->expectNotToPerformAssertions();
    }


    public function testActivateBonusWhenNotActivable() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerTile = $this->givePlayerTile($player, GlenmoreParameters::$TILE_NAME_FAIR);
        $this->selectResource($playerTile);
        $activableTiles = new ArrayCollection([]);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->service->activateBonus($playerTile, $player, $activableTiles);
    }

    private function givePlayerTile(PlayerGLM $player, String $type) : PlayerTileGLM
    {
        $tile = $this->tileGLMRepository->findOneBy(["name" => $type]);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setActivated(false);
        $playerTile->setCoordY(0);
        $playerTile->setCoordX(3);
        $playerTile->setPersonalBoard($player->getPersonalBoard());
        $this->entityManager->persist($playerTile);
        $player->getPersonalBoard()->addPlayerTile($playerTile);
        $this->entityManager->persist($player->getPersonalBoard());
        return $playerTile;
    }

    private function selectResource(PlayerTileGLM $playerTile, int $id = 1) : void
    {
        $resource = $this->resourceGLMRepository->findOneBy(["id" => $id]);
        $selectedResource = new SelectedResourceGLM();
        $selectedResource->setResource($resource);
        $selectedResource->setPlayerTile($playerTile);
        $selectedResource->setPersonalBoardGLM($playerTile->getPersonalBoard());
        $selectedResource->setQuantity(1);
        $this->entityManager->persist($selectedResource);
        $playerTile->addSelectedResource($selectedResource);
        $this->entityManager->persist($playerTile);
        $playerTile->getPersonalBoard()->addSelectedResource($selectedResource);
        $this->entityManager->persist($playerTile->getPersonalBoard());
    }

    private function createGame(int $nbOfPlayers) : GameGLM
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $this->warehouseLineGLMRepository = static::getContainer()->get(WarehouseLineGLMRepository::class);
        $this->tileGLMRepository = $tileGLMRepository;
        $this->cardGLMRepository = static::getContainer()->get(CardGLMRepository::class);
        $this->entityManager = $entityManager;
        $this->service = static::getContainer()->get(TileGLMService::class);
        $resourceGLMRepository = static::getContainer()->get(ResourceGLMRepository::class);
        $this->resourceGLMRepository = $resourceGLMRepository;
        $game = new GameGLM();
        $game->setGameName(AbstractGameManagerService::$GLM_LABEL);
        $mainBoard = new MainBoardGLM();
        $mainBoard->setGameGLM($game);
        $tilesLevelZero = $tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_ZERO]);
        $tilesLevelOne = $tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_ONE]);
        $tilesLevelTwo = $tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_TWO]);
        $tilesLevelThree = $tileGLMRepository->findBy(['level' => GlenmoreParameters::$TILE_LEVEL_THREE]);
        $drawArray = [$tilesLevelZero, $tilesLevelOne, $tilesLevelTwo, $tilesLevelThree];
        for ($i = GlenmoreParameters::$TILE_LEVEL_ZERO; $i <= GlenmoreParameters::$TILE_LEVEL_THREE; ++$i) {
            $draw = new DrawTilesGLM();
            $draw->setLevel($i);
            $draw->setMainBoardGLM($mainBoard);
            $tiles = $drawArray[$i];
            foreach ($tiles as $tile) {
                $draw->addTile($tile);
            }
            $entityManager->persist($draw);
            $mainBoard->addDrawTile($draw);
            $entityManager->persist($mainBoard);
            $warehouse = new WarehouseGLM();
            $warehouse->setMainBoardGLM($mainBoard);
            $entityManager->persist($warehouse);


            $green_cube = $resourceGLMRepository->findOneBy(
                ['type' => GlenmoreParameters::$PRODUCTION_RESOURCE, 'color' => GlenmoreParameters::$COLOR_GREEN]
            );
            $yellow_cube = $resourceGLMRepository->findOneBy(
                ['type' => GlenmoreParameters::$PRODUCTION_RESOURCE, 'color' => GlenmoreParameters::$COLOR_YELLOW]
            );
            $brown_cube = $resourceGLMRepository->findOneBy(
                ['type' => GlenmoreParameters::$PRODUCTION_RESOURCE, 'color' => GlenmoreParameters::$COLOR_BROWN]
            );
            $white_cube = $resourceGLMRepository->findOneBy(
                ['type' => GlenmoreParameters::$PRODUCTION_RESOURCE, 'color' => GlenmoreParameters::$COLOR_WHITE]
            );
            $grey_cube = $resourceGLMRepository->findOneBy(
                ['type' => GlenmoreParameters::$PRODUCTION_RESOURCE, 'color' => GlenmoreParameters::$COLOR_GREY]
            );
            $numberOfCoin = 0;
            if ($game->getPlayers()->count() != GlenmoreParameters::$MAX_NUMBER_OF_PLAYER - 1) {
                $numberOfCoin = 1;
            }
            $warehouse = $game->getMainBoard()->getWarehouse();
            $this->addWarehouseLineToWarehouse($warehouse, $green_cube, $numberOfCoin);
            $this->addWarehouseLineToWarehouse($warehouse, $yellow_cube, $numberOfCoin);
            $this->addWarehouseLineToWarehouse($warehouse, $brown_cube, $numberOfCoin);
            $this->addWarehouseLineToWarehouse($warehouse, $white_cube, $numberOfCoin);
            $this->addWarehouseLineToWarehouse($warehouse, $grey_cube, $numberOfCoin);

            $this->entityManager->persist($warehouse);
            $entityManager->persist($mainBoard);
        }

        for ($i = $nbOfPlayers; $i < GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD; ++$i) {
            $drawTiles = $mainBoard->getDrawTiles();
            $level = 0;
            for ($j = GlenmoreParameters::$TILE_LEVEL_ZERO; $j <= GlenmoreParameters::$TILE_LEVEL_THREE; ++$j) {
                if ($drawTiles->get($j)->getTiles()->isEmpty()) {
                    ++$level;
                } else {
                    break;
                }
            }
            $draw = $drawTiles->get($level);
            $tile = $draw->getTiles()->first();
            $mainBoardTile = new BoardTileGLM();
            $mainBoardTile->setTile($tile);
            $mainBoard->addBoardTile($mainBoardTile);
            $mainBoardTile->setMainBoardGLM($mainBoard);
            $mainBoardTile->setPosition($i);
            $entityManager->persist($mainBoardTile);
            $draw->removeTile($tile);
            $entityManager->persist($mainBoard);
            $entityManager->persist($draw);
        }

        for ($i = 0; $i < $nbOfPlayers; $i++) {
            $player = new PlayerGLM('test', $game);
            $player->setRoundPhase(0);
            $player->setGame($game);
            $player->setTurnOfPlayer(false);
            $player->setScore(0);
            $game->addPlayer($player);
            $personalBoard = new PersonalBoardGLM();
            $player->setPersonalBoard($personalBoard);
            $personalBoard->setPlayerGLM($player);
            $personalBoard->setLeaderCount(0);
            $personalBoard->setMoney(GlenmoreParameters::$START_MONEY);
            $pawn = new PawnGLM();
            $pawn->setColor(GlenmoreParameters::$COLOR_FROM_POSITION[$i]);
            $pawn->setPosition($i);
            $pawn->setMainBoardGLM($mainBoard);
            $player->setPawn($pawn);
            $entityManager->persist($pawn);
            $entityManager->persist($player);
            $startVillages = $tileGLMRepository->findBy(['name' => GlenmoreParameters::$TILE_NAME_START_VILLAGE]);
            $villager = $resourceGLMRepository->findOneBy(['type' => GlenmoreParameters::$VILLAGER_RESOURCE]);
            $playerTile = new PlayerTileGLM();
            $playerTile->setActivated(false);
            $playerTile->setCoordX(0);
            $playerTile->setCoordY(0);
            $playerTile->setTile($startVillages[$i]);
            $playerTile->setCoordX(0);
            $playerTile->setCoordY(0);
            $playerTile->setPersonalBoard($personalBoard);
            $entityManager->persist($playerTile);
            $playerTileResource = new PlayerTileResourceGLM();
            $playerTileResource->setPlayerTileGLM($playerTile);
            $playerTileResource->setPlayer($player);
            $playerTileResource->setResource($villager);
            $playerTileResource->setQuantity(1);
            $entityManager->persist($playerTileResource);
            $playerTile->addPlayerTileResource($playerTileResource);
            $entityManager->persist($playerTile);
            $personalBoard->addPlayerTile($playerTile);
            $entityManager->persist($personalBoard);
            $entityManager->persist($player);
            $entityManager->flush();
        }
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->setTurnOfPlayer(true);
        $entityManager->persist($firstPlayer);
        $entityManager->flush();
        return $game;
    }
    private function addWarehouseLineToWarehouse(WarehouseGLM $warehouse, ResourceGLM $resource, int $coinNumber): void
    {
        $warehouseLine = new WarehouseLineGLM();
        $warehouseLine->setWarehouseGLM($warehouse);
        $warehouseLine->setResource($resource);
        $warehouseLine->setCoinNumber($coinNumber);
        $quantity = $coinNumber == GlenmoreParameters::$COIN_NEEDED_FOR_RESOURCE_ONE ? 1 :
            ($coinNumber == GlenmoreParameters::$COIN_NEEDED_FOR_RESOURCE_TWO ? 2 :
                ($coinNumber == GlenmoreParameters::$COIN_NEEDED_FOR_RESOURCE_THREE ? 3 : 0));
        $warehouseLine->setQuantity($quantity);
        $this->entityManager->persist($warehouseLine);
        $warehouse->addWarehouseLine($warehouseLine);
        $this->entityManager->persist($warehouse);
    }

}