<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Service\Game\Myrmes\WorkerMYRService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WorkerMYRServiceTest extends KernelTestCase
{

    private EntityManagerInterface $entityManager;
    private WorkerMYRService $workerMYRService;

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->workerMYRService = static::getContainer()->get(WorkerMYRService::class);
    }

    public function testPlaceAnthillHoleWhenPlaceIsAvailable()
    {
        // GIVEN
        $workerMYRService = static::getContainer()->get(WorkerMYRService::class);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $this->entityManager->flush();
        $player = $game->getPlayers()->first();
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
        // THEN
        $this->assertNotEmpty($player->getAnthillHoleMYRs());
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseThereIsAnthillHole()
    {
        // GIVEN
        $workerMYRService = static::getContainer()->get(WorkerMYRService::class);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $this->entityManager->flush();
        $player = $game->getPlayers()->first();
        $anthillHole = new AnthillHoleMYR();
        $anthillHole->setPlayer($player);
        $anthillHole->setTile($tile);
        $anthillHole->setMainBoardMYR($game->getMainBoardMYR());
        $this->entityManager->persist($anthillHole);
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseTileIsWater()
    {
        // GIVEN
        $workerMYRService = static::getContainer()->get(WorkerMYRService::class);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$WATER_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $this->entityManager->flush();
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseThereIsAPheromone()
    {
        // GIVEN
        $workerMYRService = static::getContainer()->get(WorkerMYRService::class);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $player = $game->getPlayers()->first();
        $tileType = new TileTypeMYR();
        $tileType->setType(-1);
        $tileType->setOrientation(-1);
        $this->entityManager->persist($tileType);
        $pheromon = new PheromonMYR();
        $pheromon->setType($tileType);
        $pheromon->setPlayer($player);
        $pheromon->setHarvested(false);
        $this->entityManager->persist($pheromon);
        $resource = new ResourceMYR();
        $resource->setDescription("TEST");
        $this->entityManager->persist($resource);
        $pheromonTile = new PheromonTileMYR();
        $pheromonTile->setPheromonMYR($pheromon);
        $pheromonTile->setMainBoard($game->getMainBoardMYR());
        $pheromonTile->setTile($tile);
        $pheromonTile->setResource($resource);
        $this->entityManager->persist($pheromonTile);
        $pheromon->addPheromonTile($pheromonTile);
        $this->entityManager->persist($pheromon);
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation0()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::$GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::$PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($firstPlayer);
        $pheromone->setHarvested(false);
        $this->entityManager->persist($pheromone);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $pheromone);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation1()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(1);
        $tile->setCoordY(1);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::$GRASS_TILE_TYPE);
        $newTile->setCoordX(2);
        $newTile->setCoordY(0);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::$PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(1);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($firstPlayer);
        $pheromone->setHarvested(false);
        $this->entityManager->persist($pheromone);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $pheromone);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation2()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(2);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::$GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(0);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::$PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(2);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($firstPlayer);
        $pheromone->setHarvested(false);
        $this->entityManager->persist($pheromone);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $pheromone);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation3()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(1);
        $tile->setCoordY(1);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::$GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(0);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::$PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($firstPlayer);
        $pheromone->setHarvested(false);
        $this->entityManager->persist($pheromone);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $pheromone);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation4()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(1);
        $tile->setCoordY(1);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::$GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::$PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(4);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($firstPlayer);
        $pheromone->setHarvested(false);
        $this->entityManager->persist($pheromone);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $pheromone);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation5()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::$GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::$PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(5);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($firstPlayer);
        $pheromone->setHarvested(false);
        $this->entityManager->persist($pheromone);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $pheromone);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientationImpossible()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::$GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::$PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($firstPlayer);
        $pheromone->setHarvested(false);
        $this->entityManager->persist($pheromone);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $pheromone);
    }

    public function testPlacePheromoneOfTypeZeroWhenTileContainPrey()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::$DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::$GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::$PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(5);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($firstPlayer);
        $pheromone->setHarvested(false);
        $this->entityManager->persist($pheromone);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $this->entityManager->persist($game);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $prey->setType("tests");
        $prey->setMainBoardMYR($game->getMainBoardMYR());
        $this->entityManager->persist($prey);
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $pheromone);
    }

    private function createGame(int $numberOfPlayers) : GameMYR
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        if($numberOfPlayers < MyrmesParameters::$MIN_NUMBER_OF_PLAYER ||
            $numberOfPlayers > MyrmesParameters::$MAX_NUMBER_OF_PLAYER) {
            throw new \Exception("TOO MUCH PLAYERS ON CREATE GAME");
        }
        $game = new GameMYR();
        for ($i = 0; $i < $numberOfPlayers; $i += 1) {
            $player = new PlayerMYR('test', $game);
            $game->addPlayer($player);
            $player->setGameMyr($game);
            $player->setColor("");
            $player->setPhase(MyrmesParameters::$PHASE_EVENT);
            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setLarvaCount(0);
            $personalBoard->setSelectedEventLarvaeAmount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setBonus(0);
            $player->setPersonalBoardMYR($personalBoard);
            $player->setScore(0);
            $player->setGoalLevel(0);
            $player->setRemainingHarvestingBonus(0);
            $entityManager->persist($player);
            $entityManager->persist($personalBoard);
        }
        $mainBoard = new MainBoardMYR();
        $mainBoard->setYearNum(0);
        $mainBoard->setGame($game);
        $season = new SeasonMYR();
        $season->setName("Spring");
        $season->setDiceResult(1);
        $season->setActualSeason(true);
        $season->setMainBoard($mainBoard);
        $mainBoard->addSeason($season);
        $entityManager->persist($season);
        $game->setMainBoardMYR($mainBoard);
        $game->setGameName("test");
        $game->setLaunched(true);
        $entityManager->persist($mainBoard);
        $entityManager->persist($game);
        $entityManager->flush();

        return $game;
    }

}