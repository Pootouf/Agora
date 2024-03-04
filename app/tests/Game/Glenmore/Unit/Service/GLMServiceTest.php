<?php

namespace App\Tests\Game\Glenmore\Unit\Service;

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
use App\Entity\Game\Glenmore\TileBuyBonusGLM;
use App\Entity\Game\Glenmore\TileGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Entity\Game\Glenmore\WarehouseResourceGLM;
use App\Repository\Game\Glenmore\CardGLMRepository;
use App\Repository\Game\Glenmore\DrawTilesGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use App\Repository\Game\Splendor\DrawCardsSPLRepository;
use App\Repository\Game\Splendor\NobleTileSPLRepository;
use App\Repository\Game\Splendor\PlayerCardSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Repository\Game\Splendor\RowSPLRepository;
use App\Repository\Game\Splendor\TokenSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Glenmore\CardGLMService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use App\Service\Game\Glenmore\WarehouseGLMService;
use App\Service\Game\Splendor\SPLService;
use App\Service\Game\Splendor\TokenSPLService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GLMServiceTest extends TestCase
{
    private GLMService $GLMService;
    private WarehouseGLMService $warehouseGLMService;
    private TileGLMService $tileGLMService;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $tileGLMRepository = $this->createMock(TileGLMRepository::class);
        $drawTilesGLMRepository = $this->createMock(DrawTilesGLMRepository::class);
        $resourceGLMRepository = $this->createMock(ResourceGLMRepository::class);
        $playerGLMRepository = $this->createMock(PlayerGLMRepository::class);
        $cardGLMService = $this->createMock(CardGLMService::class);
        $this->GLMService = new GLMService($entityManager, $tileGLMRepository, $drawTilesGLMRepository,
            $resourceGLMRepository, $playerGLMRepository, $cardGLMService);
        $this->tileGLMService = new TileGLMService($entityManager, $this->GLMService, $playerGLMRepository);
        $this->warehouseGLMService = new WarehouseGLMService($entityManager, $this->GLMService, $playerGLMRepository);
    }

    public function testDoNotSkipPlayerTurnWhenPlayerIsStillTheLastInChain()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $secondPlayer = $game->getPlayers()->last();
        $startTurnPosition = $firstPlayer->getPawn()->getPosition();
        $firstPlayer->getPawn()->setPosition(($startTurnPosition + 1) %
            GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD);
        $secondPlayer->getPawn()->setPosition(($startTurnPosition + 3) %
            GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD);
        // WHEN
        $this->GLMService->endRoundOfPlayer($game, $firstPlayer, $startTurnPosition);
        // THEN
        $this->assertTrue($firstPlayer->isTurnOfPlayer());
    }

    public function testSkipTurnOfPlayerWhenPlayerIsNoLongerTheLastInChain()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $secondPlayer = $game->getPlayers()->last();
        $startTurnPosition = $firstPlayer->getPawn()->getPosition();
        $firstPlayer->getPawn()->setPosition(($startTurnPosition + 3) %
            GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD);
        // WHEN
        $this->GLMService->endRoundOfPlayer($game, $firstPlayer, $startTurnPosition);
        // THEN
        $this->assertFalse($firstPlayer->isTurnOfPlayer());
        $this->assertTrue($secondPlayer->isTurnOfPlayer());
    }

    public function testIsGameEndedShouldReturnTrue() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $drawTiles = $game->getMainBoard()->getDrawTiles();
        foreach ($drawTiles as $drawTile) {
            $drawTile->getTiles()->clear();
        }
        //WHEN
        $result = $this->GLMService->isGameEnded($game);
        //THEN
        $this->assertTrue($result);
    }

    public function testIsGameEndedShouldReturnFalse() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $drawTiles = $game->getMainBoard()->getDrawTiles();
        foreach ($drawTiles as $drawTile) {
            if ($drawTile->getLevel() == GlenmoreParameters::$TILE_LEVEL_THREE) {
                break;
            }
            $drawTile->getTiles()->clear();
        }
        $drawLevelThree = $drawTiles->get(GlenmoreParameters::$TILE_LEVEL_THREE);
        $tiles = $drawLevelThree->getTiles();
        for ($i = 0; $i < $tiles->count() - 1; ++$i) {
            $drawLevelThree->removeTile($tiles->get($i));
        }
        //WHEN
        $result = $this->GLMService->isGameEnded($game);
        //THEN
        $this->assertFalse($result);
    }

    public function testGiveBuyBonusWithSimpleProductionTile() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoard();
        $tile = new TileGLM();
        $tile->setName(GlenmoreParameters::$TILE_NAME_FOREST);
        $tile->setType(GlenmoreParameters::$TILE_TYPE_GREEN);
        $buyBonus = new TileBuyBonusGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $resource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $buyBonus->setResource($resource);
        $buyBonus->setAmount(1);
        $tile->addBuyBonus($buyBonus);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setPersonalBoard($personalBoard);
        $personalBoard->addPlayerTile($playerTile);
        $expectedAmount = 1;
        $expectedType = GlenmoreParameters::$PRODUCTION_RESOURCE;
        $expectedColor = GlenmoreParameters::$COLOR_GREEN;
        //WHEN
        $this->tileGLMService->giveBuyBonus($playerTile);
        //THEN
        $amount = 0;
        $type = "";
        $color = "";
        foreach ($personalBoard->getPlayerTiles() as $tile) {
            if ($tile === $playerTile) {
                $playerTileResources = $tile->getPlayerTileResource();
                foreach ($playerTileResources as $playerTileResource) {
                    $amount = $playerTileResource->getQuantity();
                    $type = $playerTileResource->getResource()->getType();
                    $color = $playerTileResource->getResource()->getColor();
                }
            }
        }
        $this->assertEquals($expectedAmount, $amount);
        $this->assertSame($expectedType, $type);
        $this->assertSame($expectedColor, $color);
    }

    public function testGiveBuyBonusWithSimpleProductionTileWhenNoBuyBonus() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoard();
        $tile = new TileGLM();
        $tile->setName(GlenmoreParameters::$TILE_NAME_FOREST);
        $tile->setType(GlenmoreParameters::$TILE_TYPE_GREEN);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setPersonalBoard($personalBoard);
        $personalBoard->addPlayerTile($playerTile);
        $expectedAmount = 0;
        //WHEN
        $this->tileGLMService->giveBuyBonus($playerTile);
        //THEN
        $amount = 0;
        $type = null;
        $color = null;
        foreach ($personalBoard->getPlayerTiles() as $tile) {
            if ($tile === $playerTile) {
                $playerTileResources = $tile->getPlayerTileResource();
                foreach ($playerTileResources as $playerTileResource) {
                    $amount = $playerTileResource->getQuantity();
                    $type = $playerTileResource->getResource()->getType();
                    $color = $playerTileResource->getResource()->getColor();
                }
            }
        }
        $this->assertEquals($expectedAmount, $amount);
        $this->assertNull($type);
        $this->assertNull($color);
    }

    public function testGiveBuyBonusWithCard() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoard();
        $tile = new TileGLM();
        $tile->setName(GlenmoreParameters::$CARD_CAWDOR_CASTLE);
        $tile->setType(GlenmoreParameters::$TILE_TYPE_CASTLE);
        $buyBonus = new TileBuyBonusGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$VILLAGER_RESOURCE);
        $resource->setColor(GlenmoreParameters::$COLOR_BLACK);
        $buyBonus->setResource($resource);
        $buyBonus->setAmount(1);
        $tile->addBuyBonus($buyBonus);
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setPersonalBoard($personalBoard);
        $personalBoard->addPlayerTile($playerTile);
        $card = new CardGLM();
        $card->setName(GlenmoreParameters::$CARD_CAWDOR_CASTLE);
        $card->setValue(0);
        $cardBonus = new TileBuyBonusGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$HAT_RESOURCE);
        $resource->setColor(GlenmoreParameters::$COLOR_BROWN);
        $cardBonus->setResource($resource);
        $cardBonus->setAmount(3);
        $card->setBonus($cardBonus);
        $tile->setCard($card);
        $playerCard = new PlayerCardGLM($personalBoard, $card);
        $personalBoard->addPlayerCardGLM($playerCard);
        $expectedAmountVillager = 1;
        $expectedAmountHat = 3;
        $expectedTypeVillager = GlenmoreParameters::$VILLAGER_RESOURCE;
        $expectedTypeHat = GlenmoreParameters::$HAT_RESOURCE;
        //WHEN
        $this->tileGLMService->giveBuyBonus($playerTile);
        //THEN
        $amountVillager = 0;
        $amountHat = 0;
        $typeVillager = null;
        $typeHat = null;
        foreach ($personalBoard->getPlayerTiles() as $newTile) {
            if ($newTile === $playerTile) {
                $playerTileResources = $newTile->getPlayerTileResource();
                foreach ($playerTileResources as $playerTileResource) {
                    $typeVillager = $playerTileResource->getResource()->getType();
                    if ($typeVillager === $expectedTypeVillager) {
                        $amountVillager = $playerTileResource->getQuantity();
                        break;
                    }
                }
            }
        }
        foreach ($personalBoard->getPlayerCardGLM() as $playerCard) {
            $actualCard = $playerCard->getCard();
            if ($tile->getCard() === $actualCard) {
                $typeHat = $actualCard->getBonus()->getResource()->getType();
                if ($typeHat === $expectedTypeHat) {
                    $amountHat = $actualCard->getBonus()->getAmount();
                    break;
                }
            }
        }
        $this->assertEquals($expectedAmountVillager, $amountVillager);
        $this->assertSame($expectedTypeVillager, $typeVillager);
        $this->assertEquals($expectedAmountHat, $amountHat);
        $this->assertSame($expectedTypeHat, $typeHat);
    }

    public function testPlaceNewTileOnMainBoard() : void
    {
        // GIVEN

        $nbOfPlayers = 4;
        $game = $this->createGame($nbOfPlayers);
        $mainBoard = $game->getMainBoard();
        $firstPlayer = $game->getPlayers()->first();
        $secondPlayer = $game->getPlayers()->get(2);
        $boardTile = $mainBoard->getBoardTiles()->last();

        $lastPosition = $this->tileGLMService->assignTileToPlayer($boardTile, $firstPlayer);
        $lastPosition -= 1;
        if ($lastPosition < 0) {
            $lastPosition += GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD;
        }
        // WHEN

        $this->tileGLMService->placeNewTile($secondPlayer,
            $mainBoard->getDrawTiles()->get(GlenmoreParameters::$TILE_LEVEL_ONE));

        // THEN

        $this->assertNotContains($boardTile, $mainBoard->getBoardTiles());
        $this->assertEquals($lastPosition, $mainBoard->getBoardTiles()->last()->getPosition());
    }

    public function testSuccessTileAllocation() : void
    {
        // GIVEN

        $nbOfPlayers = 4;
        $game = $this->createGame($nbOfPlayers);
        $mainBoard = $game->getMainBoard();
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoard();
        $boardTile = $mainBoard->getBoardTiles()->last();

        // WHEN

        $lastPosition = $this->tileGLMService->assignTileToPlayer($boardTile, $player);

        // THEN

        $this->assertEquals($boardTile->getTile(),
            $personalBoard->getPlayerTiles()->last()->getTile());
        $this->assertNotContains($boardTile, $mainBoard->getBoardTiles());
        $this->assertNotEquals($player->getPawn()->getPosition(), $lastPosition);
    }

    /*
    public function testTileAllocationWhenPlayerHaveNotAllRequiredResources() : void
    {
        // GIVEN
        $nbOfPlayers = 4;
        $game = $this->createGame($nbOfPlayers);
        $player = $game->getPlayers()->first();
        $mainBoard = $game->getMainBoard();
        $personalBoard = $player->getPersonalBoard();
        $boardTiles = $mainBoard->getBoardTiles();

        $level = 2;
        for ($i = $nbOfPlayers; $i < GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD; ++$i)
        {
            $drawTiles = $mainBoard->getDrawTiles();
            $draw = $drawTiles->get($level);

            if ($draw->getTiles()->count() == 0) {
                $draw = $drawTiles->get($level + 1);
            }

            $tile = $draw->getTiles()->last();
            $mainBoardTile = new BoardTileGLM();
            $mainBoardTile->setTile($tile);
            $mainBoardTile->setMainBoardGLM($mainBoard);
            $mainBoardTile->setPosition($i);
            $mainBoard->addBoardTile($mainBoardTile);
            $draw->removeTile($tile);
        }

        $boardTile = null;
        $resource = null;
        foreach ($boardTiles as $tile)
        {
            $buy = $tile->getTile()->getBuyPrice();
            if ($buy->count() != 0)
            {
                $r = $buy->first();
                $boardTile = $tile;
                $resource = new ResourceGLM();
                $resource->setColor($r->getResource()->getColor());
                $resource->setType($r->getResource()->getType());
            }
            if ($resource != null && $boardTile != null)
            {
                break;
            }
        }

        $personalBoard->setMoney(0);

        // WHEN

        $this->expectException(\Exception::class);

        // THEN

        $this->TileGLMService->assignTileToPlayer($player, $boardTile);
    }


    public function testTileAllocationWhenPlayerCanNotPlaceTileInPersonalBoard() : void
    {
        // GIVEN
        $nbOfPlayers = 4;
        $game = $this->createGame($nbOfPlayers);
        $player = $game->getPlayers()->first();
        $mainBoard = $game->getMainBoard();
        $personalBoard = $player->getPersonalBoard();

        $boardTiles = $mainBoard->getBoardTiles();

        // TODO provoquer l'erreur de placement

        // WHEN

        $this->expectException(\Exception::class);

        // THEN

        $this->TileGLMService->assignTileToPlayer($player, $boardTile);
    }
    */

    public function testResourceSaleWhenPlayerHaveNotThisResource() : void
    {
        // GIVEN

        $nbPlayer = 4;
        $game = $this->createGame($nbPlayer);
        $mainBoard = $game->getMainBoard();
        $player = $game->getPlayers()->first();
        $resource = new ResourceGLM();
        $resource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $warehouseResource = new WarehouseResourceGLM(
            $mainBoard->getWarehouse(),
            $resource
        );
        $mainBoard->getWarehouse()->addWarehouseResource($warehouseResource);

        // WHEN

        $this->expectException(\Exception::class);

        // THEN

        $this->warehouseGLMService->sellResource($player, $resource);
    }

    public function testResourceSaleWhenWarehouseHaveNotMoneyForThisResource() : void
    {
        // GIVEN

        $nbPlayer = 4;
        $game = $this->createGame($nbPlayer);
        $player = $game->getPlayers()->first();
        $resource = new ResourceGLM();
        $resource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $personalBoard = $player->getPersonalBoard();
        $mainBoard = $game->getMainBoard();
        $tile = $mainBoard->getDrawTiles()->last()->getTiles()->last();
        $mainBoard->getDrawTiles()->last()->removeTile($tile);

        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTile->setPersonalBoard($personalBoard);

        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);

        $playerTile->addPlayerTileResource($playerTileResource);
        $personalBoard->addPlayerTile($playerTile);

        $mainBoard->getWarehouse()->getWarehouseResource()->clear();

        // WHEN

        $this->expectException(\Exception::class);

        // THEN

        $this->warehouseGLMService->sellResource($player, $resource);
    }

    public function testSuccessResourceSale() : void
    {
        // GIVEN

        $nbPlayer = 4;
        $game = $this->createGame($nbPlayer);
        $mainBoard = $game->getMainBoard();
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoard();

        $resourceWarehouse = new ResourceGLM();
        $resourceWarehouse->setColor(GlenmoreParameters::$COLOR_GREEN);
        $warehouseResource = new WarehouseResourceGLM(
            $mainBoard->getWarehouse(),
            $resourceWarehouse
        );
        $mainBoard->getWarehouse()->addWarehouseResource($warehouseResource);

        $resourcePlayer = new ResourceGLM();
        $resourcePlayer->setColor(GlenmoreParameters::$COLOR_GREEN);

        $playerTile = new PlayerTileGLM();
        $tile = $mainBoard->getDrawTiles()->last()->getTiles()->last();
        $playerTile->setTile($tile);
        $playerTile->setPersonalBoard($personalBoard);

        $playerTileResource = new PlayerTileResourceGLM();
        $playerTileResource->setResource($resourcePlayer);
        $playerTileResource->setQuantity(1);
        $playerTileResource->setPlayerTileGLM($playerTile);

        $playerTile->addPlayerTileResource($playerTileResource);
        $personalBoard->addPlayerTile($playerTile);

        $lastMoney = $personalBoard->getMoney();

        // WHEN

        $this->warehouseGLMService->sellResource($player, $resourcePlayer);

        // THEN

        $this->assertNull($this->warehouseGLMService->getResourceOnPersonalBoard(
            $personalBoard,
            $resourcePlayer)
        );
        $this->assertNotEquals($personalBoard->getMoney(), $lastMoney);
    }

    private function createGame(int $nbOfPlayers): GameGLM
    {
        $game = new GameGLM();
        $game->setGameName(AbstractGameManagerService::$GLM_LABEL);
        $mainBoard = new MainBoardGLM();
        $mainBoard->setGameGLM($game);

        for ($i = GlenmoreParameters::$TILE_LEVEL_ZERO; $i <= GlenmoreParameters::$TILE_LEVEL_THREE; ++$i) {
            $draw = new DrawTilesGLM();
            $draw->setLevel($i);
            $draw->setMainBoardGLM($mainBoard);
            for ($j = 1; $j <= 15; ++$j) {
                $tile = new TileGLM();
                $tile->setLevel($j);
                if ($j % 5 == 0) {
                    $tile->setType(GlenmoreParameters::$TILE_TYPE_GREEN);
                    $tile->setName(GlenmoreParameters::$TILE_NAME_FOREST);
                } else if ($j % 5 == 1) {
                    $tile->setType(GlenmoreParameters::$TILE_TYPE_YELLOW);
                    $tile->setName(GlenmoreParameters::$TILE_NAME_FIELD);
                } else if ($j % 5 == 2) {
                    $tile->setType(GlenmoreParameters::$TILE_TYPE_BROWN);
                    $tile->setName(GlenmoreParameters::$TILE_NAME_CATTLE);
                } else if ($j % 5 == 3) {
                    $tile->setType(GlenmoreParameters::$TILE_TYPE_GREEN);
                    $tile->setName(GlenmoreParameters::$TILE_NAME_PASTURE);
                } else if ($j % 5 == 4) {
                    $tile->setType(GlenmoreParameters::$TILE_TYPE_VILLAGE);
                    $tile->setName(GlenmoreParameters::$TILE_NAME_VILLAGE);
                }
                $draw->addTile($tile);
            }
            $mainBoard->addDrawTile($draw);
            $warehouse = new WarehouseGLM();
            $warehouse->setMainBoardGLM($mainBoard);
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
            $playerTile = new PlayerTileGLM();
            $startTile = new TileGLM();
            $startTile->setName(GlenmoreParameters::$TILE_NAME_START_VILLAGE);
            $startTile->setType(GlenmoreParameters::$TILE_TYPE_VILLAGE);
            $playerTile->setTile($startTile);
            $playerTile->setPersonalBoard($personalBoard);
            $playerTileResource = new PlayerTileResourceGLM();
            $playerTileResource->setPlayerTileGLM($playerTile);
            $villager = new ResourceGLM();
            $villager->setType(GlenmoreParameters::$VILLAGER_RESOURCE);
            $playerTileResource->setResource($villager);
            $playerTileResource->setQuantity(1);
        }

        for ($i = $nbOfPlayers; $i < GlenmoreParameters::$NUMBER_OF_TILES_ON_BOARD; ++$i) {
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
            $mainBoard->addBoardTile($mainBoardTile);
            $draw->removeTile($tile);
        }
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->setTurnOfPlayer(true);
        return $game;
    }
}