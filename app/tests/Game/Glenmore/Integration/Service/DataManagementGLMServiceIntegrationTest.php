<?php

namespace App\Tests\Game\Glenmore\Integration\Service;

use App\Entity\Game\DTO\Glenmore\PersonalBoardBoxGLM;
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
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use App\Repository\Game\Glenmore\ResourceGLMRepository;
use App\Repository\Game\Glenmore\TileGLMRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Glenmore\DataManagementGLMService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DataManagementGLMServiceIntegrationTest extends KernelTestCase
{

    public function testOrganizePersonalBoardRowsShouldReturnCollectionOfSize2By3() : void
    {
        //GIVEN
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $dataManagementGLMService = static::getContainer()->get(DataManagementGLMService::class);

        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoard();

        $startTile = new PlayerTileGLM();
        $tile = new TileGLM();
        $tile->setLevel(0);
        $tile->setName("");
        $tile->setType("");
        $tile->setContainingRiver(false);
        $tile->setContainingRoad(false);
        $startTile->setTile($tile);
        $startTile->setCoordX(0);
        $startTile->setCoordY(0);
        $personalBoard->addPlayerTile($startTile);
        $entityManager->persist($tile);
        $entityManager->persist($startTile);

        $tile1 = new PlayerTileGLM();
        $tile = new TileGLM();
        $tile->setLevel(0);
        $tile->setName("");
        $tile->setType("");
        $tile->setContainingRiver(false);
        $tile->setContainingRoad(false);
        $tile1->setTile($tile);
        $tile1->setCoordX(0);
        $tile1->setCoordY(2);
        $personalBoard->addPlayerTile($tile1);
        $entityManager->persist($tile);
        $entityManager->persist($tile1);

        $tile2 = new PlayerTileGLM();
        $tile = new TileGLM();
        $tile->setLevel(0);
        $tile->setName("");
        $tile->setType("");
        $tile->setContainingRiver(false);
        $tile->setContainingRoad(false);
        $tile2->setTile($tile);
        $tile2->setCoordX(-1);
        $tile2->setCoordY(1);
        $personalBoard->addPlayerTile($tile2);
        $entityManager->persist($tile);
        $entityManager->persist($tile2);
        $entityManager->persist($personalBoard);

        $row0 = new ArrayCollection([
            new PersonalBoardBoxGLM(null, $tile2->getCoordX() - 1, $tile2->getCoordY() - 2),
            new PersonalBoardBoxGLM(null, $tile2->getCoordX() - 1, $tile2->getCoordY() - 1),
            new PersonalBoardBoxGLM(null, $tile2->getCoordX() - 1, $tile2->getCoordY()),
            new PersonalBoardBoxGLM(null, $tile2->getCoordX() - 1, $tile2->getCoordY() + 1),
            new PersonalBoardBoxGLM(null, $tile2->getCoordX() - 1, $tile2->getCoordY() + 2)
        ]);
        $row1 = new ArrayCollection([
            new PersonalBoardBoxGLM(null, $tile2->getCoordX(), $tile2->getCoordY() - 2),
            new PersonalBoardBoxGLM(null, $tile2->getCoordX(), $tile2->getCoordY() - 1),
            new PersonalBoardBoxGLM($tile2, $tile2->getCoordX(), $tile2->getCoordY()),
            new PersonalBoardBoxGLM(null, $tile2->getCoordX(), $tile2->getCoordY() + 1),
            new PersonalBoardBoxGLM(null, $tile2->getCoordX(), $tile2->getCoordY() + 2)
        ]);
        $row2 = new ArrayCollection([
            new PersonalBoardBoxGLM(null, $startTile->getCoordX(), $startTile->getCoordY() - 1),
            new PersonalBoardBoxGLM($startTile, $startTile->getCoordX(), $startTile->getCoordY()),
            new PersonalBoardBoxGLM(null, $startTile->getCoordX(), $startTile->getCoordY() + 1),
            new PersonalBoardBoxGLM($tile1, $tile1->getCoordX(), $tile1->getCoordY()),
            new PersonalBoardBoxGLM(null, $tile1->getCoordX(), $tile1->getCoordY() + 1)
        ]);
        $row3 = new ArrayCollection([
            new PersonalBoardBoxGLM(null, $tile1->getCoordX() + 1, $tile1->getCoordY() - 3),
            new PersonalBoardBoxGLM(null, $tile1->getCoordX() + 1, $tile1->getCoordY() - 2),
            new PersonalBoardBoxGLM(null, $tile1->getCoordX() + 1, $tile1->getCoordY() - 1),
            new PersonalBoardBoxGLM(null, $tile1->getCoordX() + 1, $tile1->getCoordY()),
            new PersonalBoardBoxGLM(null, $tile1->getCoordX() + 1, $tile1->getCoordY() + 1)
        ]);
        $entityManager->flush();
        //WHEN
        $result = $dataManagementGLMService->organizePersonalBoardRows($firstPlayer);
        //THEN
        for($i = 0; $i < $row0->count(); $i++) {
            $this->assertEquals($row0->get($i)->getPlayerTile(), $result->get(0)->get($i)->getPlayerTile());
            $this->assertEquals($row0->get($i)->getCoordX(), $result->get(0)->get($i)->getCoordX());
            $this->assertEquals($row0->get($i)->getCoordY(), $result->get(0)->get($i)->getCoordY());
        }
        for($i = 0; $i < $row1->count(); $i++) {
            $this->assertEquals($row1->get($i)->getPlayerTile(), $result->get(1)->get($i)->getPlayerTile());
            $this->assertEquals($row0->get($i)->getCoordX(), $result->get(0)->get($i)->getCoordX());
            $this->assertEquals($row0->get($i)->getCoordY(), $result->get(0)->get($i)->getCoordY());
        }
        for($i = 0; $i < $row2->count(); $i++) {
            $this->assertEquals($row2->get($i)->getPlayerTile(), $result->get(2)->get($i)->getPlayerTile());
            $this->assertEquals($row0->get($i)->getCoordX(), $result->get(0)->get($i)->getCoordX());
            $this->assertEquals($row0->get($i)->getCoordY(), $result->get(0)->get($i)->getCoordY());
        }
        for($i = 0; $i < $row3->count(); $i++) {
            $this->assertEquals($row3->get($i)->getPlayerTile(), $result->get(3)->get($i)->getPlayerTile());
            $this->assertEquals($row0->get($i)->getCoordX(), $result->get(0)->get($i)->getCoordX());
            $this->assertEquals($row0->get($i)->getCoordY(), $result->get(0)->get($i)->getCoordY());
        }
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
            $playerTileResource->setPlayer($player);
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