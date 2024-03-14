<?php

namespace App\Tests\Game\Glenmore\Unit\Service;

use App\Entity\Game\DTO\Glenmore\BoardBoxGLM;
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
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Service\Game\AbstractGameManagerService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use App\Service\Game\Glenmore\DataManagementGLMService;
class DataManagementGLMServiceTest extends TestCase
{
    private DataManagementGLMService $dataManagementGLMService;
    protected function setUp(): void
    {
        $playerTileRepository = $this->createMock(PlayerTileGLMRepository::class);
        $this->dataManagementGLMService = new DataManagementGLMService($playerTileRepository);
    }

    public function testWhiskyCount() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $amount = 45;
        $amount2 = 47;
        $players = $game->getPlayers();
        $personalBoard = $players[0]->getPersonalBoard();
        $tile = new PlayerTileGLM();
        $tileResource = new PlayerTileResourceGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$WHISKY_RESOURCE);
        $tileResource->setResource($resource);
        $tileResource->setQuantity($amount);
        $tileResource->setPlayerTileGLM($tile);
        $tile->addPlayerTileResource($tileResource);
        $tile->setPersonalBoard($personalBoard);
        $personalBoard->addPlayerTile($tile);
        $tile = new PlayerTileGLM();
        $tileResource = new PlayerTileResourceGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$WHISKY_RESOURCE);
        $tileResource->setResource($resource);
        $tileResource->setQuantity($amount2);
        $tileResource->setPlayerTileGLM($tile);
        $tile->addPlayerTileResource($tileResource);
        $tile->setPersonalBoard($personalBoard);
        $personalBoard->addPlayerTile($tile);
        $expectedResult = $amount + $amount2;
        //WHEN
        $result = $this->dataManagementGLMService->getWhiskyCount($players[0]);
        //THEN
        $this->assertSame($result, $expectedResult);
    }

    public function testCreateBoardBoxes() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $expectedLength = 14;
        $expectedResult = new ArrayCollection();
        for ($i = 0; $i < 2; ++$i) {
            $pawn = $game->getPlayers()->get($i)->getPawn();
            try {
                $box = new BoardBoxGLM(null, $pawn);
            } catch (\Exception $e) {
            }
            $expectedResult->add($box);
        }
        for ($i = 2; $i < GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD - 1; ++$i) {
            $tile = $game->getMainBoard()->getBoardTiles()->get($i - 2);
            try {
                $box = new BoardBoxGLM($tile, null);
            } catch (\Exception $e) {
            }
            $expectedResult->add($box);
        }
        $box = new BoardBoxGLM(null, null);
        $expectedResult->add($box);
        //WHEN
        $result = $this->dataManagementGLMService->createBoardBoxes($game);
        //THEN
        $this->assertEquals($expectedResult, $result);
        $this->assertSame($expectedLength, $result->count());
    }

    public function testOrganizeMainBoardRows() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $boxes = $this->dataManagementGLMService->createBoardBoxes($game);
        $expectedResult = new ArrayCollection();
        $expectedRow = new ArrayCollection();
        $expectedRow->add($boxes->get(0));
        $expectedRow->add($boxes->get(1));
        $expectedRow->add($boxes->get(2));
        $expectedRow->add($boxes->get(3));
        $expectedRow->add($boxes->get(4));
        $expectedResult->add($expectedRow);
        $expectedRow = new ArrayCollection();
        $expectedRow->add($boxes->get(13));
        $expectedRow->add($boxes->get(5));
        $expectedResult->add($expectedRow);
        $expectedRow = new ArrayCollection();
        $expectedRow->add($boxes->get(12));
        $expectedRow->add($boxes->get(6));
        $expectedResult->add($expectedRow);
        $expectedRow = new ArrayCollection();
        $expectedRow->add($boxes->get(11));
        $expectedRow->add($boxes->get(10));
        $expectedRow->add($boxes->get(9));
        $expectedRow->add($boxes->get(8));
        $expectedRow->add($boxes->get(7));
        $expectedResult->add($expectedRow);

        //WHEN
        $result = $this->dataManagementGLMService->organizeMainBoardRows($boxes);

        //THEN
        $this->assertEquals($expectedResult, $result);
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
            $mainBoard->addPawn($pawn);
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

        for ($i = $nbOfPlayers; $i < GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD - 1; ++$i) {
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