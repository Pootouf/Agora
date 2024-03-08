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
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use App\Repository\Game\Glenmore\DrawTilesGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileResourceGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Glenmore\CardGLMService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TileGLMServiceTest extends TestCase
{

    private TileGLMService $tileGLMService;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $tileGLMRepository = $this->createMock(TileGLMRepository::class);
        $drawTilesGLMRepository = $this->createMock(DrawTilesGLMRepository::class);
        $resourceGLMRepository = $this->createMock(ResourceGLMRepository::class);
        $playerGLMRepository = $this->createMock(PlayerGLMRepository::class);
        $playerTileGLMRepository = $this->createMock(PlayerTileGLMRepository::class);
        $playerTileResourceGLMRepository = $this->createMock(PlayerTileResourceGLMRepository::class);
        $cardGLMService = new CardGLMService($entityManager, $resourceGLMRepository);
        $GLMService = new GLMService($entityManager, $tileGLMRepository, $drawTilesGLMRepository,
            $resourceGLMRepository, $playerGLMRepository, $cardGLMService);
        $this->tileGLMService = new TileGLMService($entityManager, $GLMService
            , $resourceGLMRepository, $playerTileResourceGLMRepository, $playerTileGLMRepository,
            $cardGLMService);
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
            $lastPosition += GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD;
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
            $array = [GlenmoreParameters::$COLOR_BROWN, GlenmoreParameters::$COLOR_GREEN, GlenmoreParameters::$COLOR_WHITE,
                GlenmoreParameters::$COLOR_YELLOW, GlenmoreParameters::$COLOR_GREY];
            for ($j = 0; $j < 5; ++$j) {
                $warehouseLine = new WarehouseLineGLM();
                $resource = new ResourceGLM();
                $resource->setColor($array[$j]);
                $resource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
                $warehouseLine->setResource($resource);
                $warehouseLine->setQuantity(0);
                $warehouse->addWarehouseLine($warehouseLine);
            }
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
            $mainBoard->addBoardTile($mainBoardTile);
            $draw->removeTile($tile);
        }
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->setTurnOfPlayer(true);
        return $game;
    }
}