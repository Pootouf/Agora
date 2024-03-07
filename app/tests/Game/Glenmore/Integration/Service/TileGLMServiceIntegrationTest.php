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
use App\Entity\Game\Glenmore\TileGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Glenmore\TileGLMService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TileGLMServiceIntegrationTest extends KernelTestCase
{


    public function testCanPlaceTileSuccessWithValidPlacement() : void
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
            $playerTileResource->setResource($villager);
            $playerTileResource->setQuantity(1);
            $entityManager->persist($playerTileResource);
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
            $mainBoardTile->setMainBoardGLM($mainBoard);
            $mainBoardTile->setPosition($i);
            $entityManager->persist($mainBoardTile);
            $draw->removeTile($tile);
            $entityManager->persist($draw);
        }
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->setTurnOfPlayer(true);
        $entityManager->persist($firstPlayer);
        $entityManager->flush();
        return $game;
    }

}