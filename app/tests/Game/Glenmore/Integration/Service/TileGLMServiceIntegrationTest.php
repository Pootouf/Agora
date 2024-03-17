<?php

namespace App\Tests\Game\Glenmore\Integration\Service;

use App\Entity\Game\Glenmore\BoardTileGLM;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\PlayerTileGLM;
use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use App\Entity\Game\Glenmore\ResourceGLM;
use App\Entity\Game\Glenmore\SelectedResourceGLM;
use App\Entity\Game\Glenmore\TileActivationBonusGLM;
use App\Entity\Game\Glenmore\TileActivationCostGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TileGLMServiceIntegrationTest extends KernelTestCase
{


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
            $glmService->endRoundOfPlayer($game, $firstPlayer, 0);
        }
        $expectedResult = 1;
        //WHEN
        $result = $tileGLMService->getMovementPoints($firstPlayer->getPersonalBoard()->getPlayerTiles()->last());
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
            $glmService->endRoundOfPlayer($game, $firstPlayer, 0);
        }
        $expectedResult = 1;
        //WHEN
        $result = $tileGLMService->getMovementPoints($firstPlayer->getPersonalBoard()->getPlayerTiles()->last());
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
            $glmService->endRoundOfPlayer($game, $firstPlayer, 0);
        }
        $expectedResult = 0;
        //WHEN
        $result = $tileGLMService->getMovementPoints($firstPlayer->getPersonalBoard()->getPlayerTiles()->last());
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
            $glmService->endRoundOfPlayer($game, $firstPlayer, 0);
        }
        $expectedResult = 2;
        //WHEN
        $result = $tileGLMService->getMovementPoints($firstPlayer->getPersonalBoard()->getPlayerTiles()->last());
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

    private function createGame(int $nbOfPlayers) : GameGLM
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $tileGLMRepository = static::getContainer()->get(TileGLMRepository::class);
        $resourceGLMRepository = static::getContainer()->get(ResourceGLMRepository::class);
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
            $entityManager->persist($mainBoard);
        }

        for ($i = 0; $i < $nbOfPlayers; $i++) {
            $player = new PlayerGLM('test', $game);
            $player->setRoundPhase(0);
            $player->setGameGLM($game);
            $player->setTurnOfPlayer(false);
            $player->setPoints(0);
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
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->setTurnOfPlayer(true);
        $entityManager->persist($firstPlayer);
        $entityManager->flush();
        return $game;
    }

}