<?php

namespace App\Tests\Game\Glenmore\Unit\Service;

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
use App\Entity\Game\Glenmore\TileGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Entity\Game\Glenmore\WarehouseLineGLM;
use App\Repository\Game\Glenmore\DrawTilesGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Glenmore\CardGLMService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use App\Service\Game\Glenmore\WarehouseGLMService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class WarehouseGLMServiceTest extends TestCase
{
    private WarehouseGLMService $warehouseGLMService;
    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $tileGLMService = $this->createMock(TileGLMService::class);
        $this->warehouseGLMService = new WarehouseGLMService($entityManager, $tileGLMService);
    }

    public function testResourceSaleWhenPlayerHaveNotThisResource() : void
    {
        // GIVEN

        $nbPlayer = 4;
        $game = $this->createGame($nbPlayer);
        $mainBoard = $game->getMainBoard();
        $player = $game->getPlayers()->first();
        $resource = new ResourceGLM();
        $resource->setColor(GlenmoreParameters::$COLOR_GREEN);
        $mainBoard->getWarehouse()->getWarehouseLine()->first()->setResource($resource);
        $mainBoard->getWarehouse()->getWarehouseLine()->first()->setQuantity(2);
        $mainBoard->getWarehouse()->getWarehouseLine()->first()->setCoinNumber(1);

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

        $mainBoard->getWarehouse()->getWarehouseLine()->clear();

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
        $mainBoard->getWarehouse()->getWarehouseLine()->get(1)->setQuantity(2);
        $mainBoard->getWarehouse()->getWarehouseLine()->get(1)->setCoinNumber(1);

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
        $this->assertGreaterThan($lastMoney, $personalBoard->getMoney());
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
            $playerTileResource->setPlayer($player);
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