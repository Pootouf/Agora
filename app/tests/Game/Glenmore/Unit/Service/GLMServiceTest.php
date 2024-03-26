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
use App\Repository\Game\Glenmore\BoardTileGLMRepository;
use App\Repository\Game\Glenmore\CardGLMRepository;
use App\Repository\Game\Glenmore\DrawTilesGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Repository\Game\Glenmore\PlayerTileResourceGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\SelectedResourceGLMRepository;
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
use App\Service\Game\Glenmore\DataManagementGLMService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\Glenmore\TileGLMService;
use App\Service\Game\Glenmore\WarehouseGLMService;
use App\Service\Game\LogService;
use App\Service\Game\Splendor\SPLService;
use App\Service\Game\Splendor\TokenSPLService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use function Symfony\Component\Translation\t;

class GLMServiceTest extends TestCase
{
    private GLMService $GLMService;
    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $tileGLMRepository = $this->createMock(TileGLMRepository::class);
        $drawTilesGLMRepository = $this->createMock(DrawTilesGLMRepository::class);
        $resourceGLMRepository = $this->createMock(ResourceGLMRepository::class);
        $playerGLMRepository = $this->createMock(PlayerGLMRepository::class);
        $boardTileGLMRepository = $this->createMock(BoardTileGLMRepository::class);
        $playerTileResourceGLMRepository = $this->createMock(PlayerTileResourceGLMRepository::class);
        $playerTileGLMRepository = $this->createMock(PlayerTileGLMRepository::class);
        $cardGLMService = new CardGLMService($entityManager, $resourceGLMRepository);
        $selectedResGLMRepo = $this->createMock(SelectedResourceGLMRepository::class);
        $tileGLMService = new TileGLMService($entityManager, $resourceGLMRepository,
                                            $playerGLMRepository, $playerTileResourceGLMRepository,
                                            $playerTileGLMRepository, $cardGLMService, $selectedResGLMRepo);
        $logService = new LogService($entityManager);
        $this->GLMService = new GLMService($entityManager, $tileGLMRepository,
            $tileGLMService, $logService, $drawTilesGLMRepository,
            $resourceGLMRepository, $playerGLMRepository, $boardTileGLMRepository, $cardGLMService);
    }

    public function testDoNotSkipPlayerTurnWhenPlayerIsStillTheLastInChain()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $secondPlayer = $game->getPlayers()->last();
        $startTurnPosition = $firstPlayer->getPawn()->getPosition();
        $firstPlayer->getPawn()->setPosition(($startTurnPosition + 1) %
            GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD);
        $secondPlayer->getPawn()->setPosition(($startTurnPosition + 3) %
            GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD);
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
            GlenmoreParameters::$NUMBER_OF_BOXES_ON_BOARD);
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

    public function testGetWinnerWithOnlyOneWinnerWithoutResourceCount() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $lastPlayer = $game->getPlayers()->last();
        $firstPlayer->setPoints(12);
        $lastPlayer->setPoints(15);
        $expectedResult = new ArrayCollection([$lastPlayer]);
        //WHEN
        $winner = $this->GLMService->getWinner($game);
        //THEN
        $this->assertEquals($expectedResult, $winner);
    }

    public function testGetWinnerWithOnlyOneWinnerWithResourceCount() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $lastPlayer = $game->getPlayers()->last();
        $firstPlayer->setPoints(12);
        $lastPlayer->setPoints(12);
        $tile = new TileGLM();
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTileResource = new PlayerTileResourceGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(2);
        $playerTile->addPlayerTileResource($playerTileResource);
        $lastPlayer->getPersonalBoard()->addPlayerTile($playerTile);

        $tile = new TileGLM();
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTileResource = new PlayerTileResourceGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$WHISKY_RESOURCE);
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(3);
        $playerTile->addPlayerTileResource($playerTileResource);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);

        $expectedResult = new ArrayCollection([$lastPlayer]);
        //WHEN
        $winner = $this->GLMService->getWinner($game);
        //THEN
        $this->assertEquals($expectedResult, $winner);
    }

    public function testGetWinnerWithTwoWinners() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $lastPlayer = $game->getPlayers()->last();
        $firstPlayer->setPoints(12);
        $lastPlayer->setPoints(12);
        $tile = new TileGLM();
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTileResource = new PlayerTileResourceGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(2);
        $playerTile->addPlayerTileResource($playerTileResource);
        $lastPlayer->getPersonalBoard()->addPlayerTile($playerTile);

        $tile = new TileGLM();
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTileResource = new PlayerTileResourceGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTile->addPlayerTileResource($playerTileResource);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);

        $tile = new TileGLM();
        $playerTile = new PlayerTileGLM();
        $playerTile->setTile($tile);
        $playerTileResource = new PlayerTileResourceGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$PRODUCTION_RESOURCE);
        $playerTileResource->setResource($resource);
        $playerTileResource->setQuantity(1);
        $playerTile->addPlayerTileResource($playerTileResource);
        $firstPlayer->getPersonalBoard()->addPlayerTile($playerTile);

        $expectedResult = new ArrayCollection([$firstPlayer, $lastPlayer]);
        //WHEN
        $winner = $this->GLMService->getWinner($game);
        //THEN
        $this->assertEquals(2, $winner->count());
        $this->assertEquals($expectedResult, $winner);
    }

    public function testCalculatePointsAtEndOfLevelWithWhiskyDifference() : void
    {
        // GIVEN
        $game = $this->createGame(5);
        $players = $game->getPlayers();
        for ($i = 0; $i < 5; ++$i) {
            $personalBoard = $players[$i]->getPersonalBoard();
            $tile = new PlayerTileGLM();
            $tileResource = new PlayerTileResourceGLM();
            $resource = new ResourceGLM();
            $resource->setType(GlenmoreParameters::$WHISKY_RESOURCE);
            $tileResource->setResource($resource);
            $tileResource->setQuantity($i);
            $tileResource->setPlayerTileGLM($tile);
            $tile->addPlayerTileResource($tileResource);
            $tile->setPersonalBoard($personalBoard);
            $personalBoard->addPlayerTile($tile);
        }
        $expectedResult = [0, 1, 2, 3, 5];
        //WHEN
        $this->GLMService->calculatePointsAtEndOfLevel($game);
        //THEN
        $result = [$players[0]->getPoints(), $players[1]->getPoints(), $players[2]->getPoints(),
            $players[3]->getPoints(), $players[4]->getPoints()];
        $this->assertSame($expectedResult, $result);
    }

    public function testCalculatePointsAtEndOfLevelWithCastleOfMey() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $players = $game->getPlayers();
        $personalBoard = $players[1]->getPersonalBoard();
        $tile = new PlayerTileGLM();
        $tileResource = new PlayerTileResourceGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$WHISKY_RESOURCE);
        $tileResource->setResource($resource);
        $tileResource->setQuantity(0);
        $tileResource->setPlayerTileGLM($tile);
        $tile->addPlayerTileResource($tileResource);
        $tile->setPersonalBoard($personalBoard);
        $personalBoard->addPlayerTile($tile);

        $tile = new PlayerTileGLM();
        $tileResource = new PlayerTileResourceGLM();
        $resource = new ResourceGLM();
        $resource->setType(GlenmoreParameters::$HAT_RESOURCE);
        $tileResource->setResource($resource);
        $tileResource->setQuantity(1);
        $tileResource->setPlayerTileGLM($tile);
        $tile->addPlayerTileResource($tileResource);
        $personalBoard->addPlayerTile($tile);
        $personalBoard->setLeaderCount(2);

        $card = new CardGLM();
        $card->setName(GlenmoreParameters::$CARD_CASTLE_OF_MEY);
        $playerCard = new PlayerCardGLM($personalBoard, $card);
        $personalBoard->addPlayerCardGLM($playerCard);
        $tile->setPersonalBoard($personalBoard);

        $expectedResult = [0, 9];
        //WHEN
        $this->GLMService->calculatePointsAtEndOfLevel($game);
        //THEN
        $result = [$players[0]->getPoints(), $players[1]->getPoints()];
        $this->assertSame($expectedResult, $result);
    }

    public function testCalculatePointsAtEndOfGameWithOnlyMoney() : void
    {
        // GIVEN
        $game = $this->createGame(5);
        $players = $game->getPlayers();
        for ($i = 0; $i < 5; ++$i) {
            $personalBoard = $players->get($i)->getPersonalBoard();
            $personalBoard->setMoney($i);
        }
        $expectedResult = [0, 1, 2, 3, 4];
        //WHEN
        $this->GLMService->calculatePointsAtEndOfGame($game);
        //THEN
        $result = [$players->get(0)->getPoints(),
            $players->get(1)->getPoints(),
            $players->get(2)->getPoints(),
            $players->get(3)->getPoints(),
            $players->get(4)->getPoints()];
        $this->assertSame($expectedResult, $result);
    }

    public function testCalculatePointsAtEndOfGameWithMoneyAndDifferencesOfTiles() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $players = $game->getPlayers();

        $personalBoard = $players->get(0)->getPersonalBoard();
        $personalBoard->setMoney(15);
        $players->get(1)->getPersonalBoard()->setMoney(0);
        for ($i = 0; $i < 4; ++$i) {
            $playerTile = new PlayerTileGLM();
            $personalBoard->addPlayerTile($playerTile);
        }

        $expectedResult = [3, 0];
        //WHEN
        $this->GLMService->calculatePointsAtEndOfGame($game);
        //THEN
        $result = [$players->get(0)->getPoints(),
            $players->get(1)->getPoints()];
        $this->assertSame($expectedResult, $result);
    }

    public function testManageEndOfRoundShouldReturnExceptionBecauseTooHigh() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $drawLevel = 4;
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->GLMService->calculatePoints($game, $drawLevel);
    }

    public function testManageEndOfRoundShouldReturnExceptionBecauseTooLow() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $drawLevel = 0;
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->GLMService->calculatePoints($game, $drawLevel);
    }

    public function testManageEndOfRoundForLevelTwo() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $drawLevel = 2;
        //THEN
        $this->expectNotToPerformAssertions();
        //WHEN
        $this->GLMService->calculatePoints($game, $drawLevel);
    }

    public function testManageEndOfRoundForLevelThree() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $drawLevel = 3;
        //THEN
        $this->expectNotToPerformAssertions();
        //WHEN
        $this->GLMService->calculatePoints($game, $drawLevel);
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