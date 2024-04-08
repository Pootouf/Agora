<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\Myrmes\GardenWorkerMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Service\Game\Myrmes\WorkerMYRService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WorkerMYRServiceTest extends KernelTestCase
{

    private EntityManagerInterface $entityManager;
    private WorkerMYRService $workerMYRService;
    private TileMYRRepository $tileMYRRepository;

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->workerMYRService = static::getContainer()->get(WorkerMYRService::class);
        $this->tileMYRRepository = static::getContainer()->get(TileMYRRepository::class);
    }

    public function testTakeOutAntSuccessWithValidExitHole()
    {
        // GIVEN

        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($player);
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $ant->setWorkFloor(MyrmesParameters::NO_WORKFLOOR);
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $tile = new TileMYR();
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $hole = new AnthillHoleMYR();
        $hole->setMainBoardMYR($game->getMainBoardMYR());
        $hole->setPlayer($player);
        $hole->setTile($tile);
        $this->entityManager->persist($tile);
        $this->entityManager->persist($hole);
        $this->entityManager->persist($ant);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
        $gardenWorkerRepository = static::getContainer()->get(GardenWorkerMYRRepository::class);
        // WHEN
        $this->workerMYRService->takeOutAnt($player->getPersonalBoardMYR(), $hole);
        // THEN
        $gardenWorker = $gardenWorkerRepository->findOneBy(['player' => $player->getId()]);
        $this->assertNotNull($gardenWorker);
    }

    public function testTakeOutAntFailWithExitHoleOfOtherPlayer()
    {
        // GIVEN

        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($player);
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $ant->setWorkFloor(MyrmesParameters::NO_WORKFLOOR);
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $tile = new TileMYR();
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $hole = new AnthillHoleMYR();
        $hole->setMainBoardMYR($game->getMainBoardMYR());
        $hole->setPlayer($game->getPlayers()->last());
        $hole->setTile($tile);
        $this->entityManager->persist($tile);
        $this->entityManager->persist($hole);
        $this->entityManager->persist($ant);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->takeOutAnt($player->getPersonalBoardMYR(), $hole);
    }

    public function testTakeOutAntFailWithNoMoreFreeAnts()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($player);
        $tile = new TileMYR();
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $hole = new AnthillHoleMYR();
        $hole->setMainBoardMYR($game->getMainBoardMYR());
        $hole->setPlayer($player);
        $hole->setTile($tile);
        $this->entityManager->persist($tile);
        $this->entityManager->persist($hole);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->takeOutAnt($player->getPersonalBoardMYR(), $hole);
    }

    public function testTakeOutAntFailWithAntAlreadyAtTheExitHole()
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($player);
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $ant->setWorkFloor(MyrmesParameters::NO_WORKFLOOR);
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $tile = new TileMYR();
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $hole = new AnthillHoleMYR();
        $hole->setMainBoardMYR($game->getMainBoardMYR());
        $hole->setPlayer($player);
        $hole->setTile($tile);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($player);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $this->entityManager->persist($tile);
        $this->entityManager->persist($hole);
        $this->entityManager->persist($ant);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->takeOutAnt($player->getPersonalBoardMYR(), $hole);
    }

    public function testPlaceAntInAnthillSuccessWithValidFloorAndValidAnt()
    {
        // GIVEN

        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($player);
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $ant->setWorkFloor(MyrmesParameters::NO_WORKFLOOR);
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $this->entityManager->persist($ant);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
        $selectedFloor = 2;
        // WHEN
        $this->workerMYRService->placeAntInAnthill($player->getPersonalBoardMYR(), $selectedFloor);
        // THEN
        $this->assertEquals($selectedFloor, $ant->getWorkFloor());
    }

    public function testPlaceAntInAnthillFailWithInvalidFloor()
    {
        // GIVEN

        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($player);
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $ant->setWorkFloor(MyrmesParameters::NO_WORKFLOOR);
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $this->entityManager->persist($ant);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
        $selectedFloor = 3;
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placeAntInAnthill($player->getPersonalBoardMYR(), $selectedFloor);
    }

    public function testPlaceAntInAnthillFailWithNoMoreFreeAnts()
    {
        // GIVEN

        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($player);
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $ant->setWorkFloor(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $this->entityManager->persist($ant);
        $this->entityManager->persist($player->getPersonalBoardMYR());
        $this->entityManager->flush();
        $selectedFloor = 2;
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placeAntInAnthill($player->getPersonalBoardMYR(), $selectedFloor);
    }

    public function testPlaceAnthillHoleWhenPlaceIsAvailable()
    {
        // GIVEN
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $this->entityManager->flush();
        $player = $game->getPlayers()->first();
        // WHEN
        $this->workerMYRService->placeAnthillHole($player, $tile);
        // THEN
        $this->assertNotEmpty($player->getAnthillHoleMYRs());
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseThereIsAnthillHole()
    {
        // GIVEN
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
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
        $this->workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseTileIsWater()
    {
        // GIVEN
        $workerMYRService = static::getContainer()->get(WorkerMYRService::class);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::WATER_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $this->entityManager->flush();
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseThereIsAPheromone()
    {
        // GIVEN
        $workerMYRService = static::getContainer()->get(WorkerMYRService::class);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
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
        $this->workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation0()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation1()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(1);
        $tile->setCoordY(1);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(2);
        $newTile->setCoordY(0);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(1);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation2()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(2);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(0);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(2);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation3()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(1);
        $tile->setCoordY(1);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(0);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation4()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(1);
        $tile->setCoordY(1);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(4);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation5()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(5);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientationImpossible()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeZeroWhenTileContainPrey()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(5);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeOneWithOrientation0()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(-1);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeOneWithOrientation1()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(-1);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(1);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeOneWithOrientation2()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(2);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeOneWithOrientationImpossible()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeOneWhenTileContainPrey()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation0()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(-1);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation1()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(-2);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(1);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation2()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(-2);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(2);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation3()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation4()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(4);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation5()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(5);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientationImpossible()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeTwoWhenTileContainPrey()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation0()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
        $newTile3->setCoordY(3);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation1()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(2);
        $newTile3->setCoordY(0);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(1);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation2()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(-3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
        $newTile3->setCoordY(-2);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(2);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation3()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(-3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(-1);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation4()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-2);
        $newTile->setCoordY(0);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(-1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(4);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation5()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(5);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientationImpossible()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeThreeWhenTileContainPrey()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeFourWithOrientation0()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(-1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(2);
        $newTile3->setCoordY(2);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation1()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(-1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(2);
        $newTile3->setCoordY(-2);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(1);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation2()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-4);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(-1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
        $newTile3->setCoordY(-2);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(2);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation3()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(-1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-2);
        $newTile3->setCoordY(-2);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation4()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-2);
        $newTile3->setCoordY(2);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(4);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation5()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(4);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(5);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation6()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(2);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation7()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(-1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(2);
        $newTile3->setCoordY(-2);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(7);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation8()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(-2);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
        $newTile3->setCoordY(-4);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(8);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation9()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(-1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-2);
        $newTile3->setCoordY(-2);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(9);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation10()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-2);
        $newTile3->setCoordY(2);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(10);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation11()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
        $newTile3->setCoordY(4);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(11);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientationImpossible()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(12);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeFourWhenTileContainPrey()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation0()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(1);
        $newTile4->setCoordY(-1);
        $this->entityManager->persist($newTile4);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation1()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(2);
        $newTile2->setCoordY(0);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(-1);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(0);
        $newTile4->setCoordY(-2);
        $this->entityManager->persist($newTile4);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(1);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation2()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(-3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
        $newTile3->setCoordY(-2);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(-1);
        $newTile4->setCoordY(-1);
        $this->entityManager->persist($newTile4);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(2);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation3()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(-3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(-1);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(-1);
        $newTile4->setCoordY(1);
        $this->entityManager->persist($newTile4);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation4()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(-2);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(0);
        $newTile4->setCoordY(2);
        $this->entityManager->persist($newTile4);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(4);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation5()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
        $newTile3->setCoordY(2);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(1);
        $newTile4->setCoordY(1);
        $this->entityManager->persist($newTile4);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(5);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientationImpossible()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(-1);
        $newTile4->setCoordY(1);
        $this->entityManager->persist($newTile4);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeFiveWhenTileContainPrey()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(-1);
        $newTile4->setCoordY(1);
        $this->entityManager->persist($newTile4);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeSixWithOrientation0()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(2);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(2);
        $newTile3->setCoordY(0);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(2);
        $newTile4->setCoordY(-2);
        $this->entityManager->persist($newTile4);
        $newTile5 = new TileMYR();
        $newTile5->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile5->setCoordX(1);
        $newTile5->setCoordY(-1);
        $this->entityManager->persist($newTile5);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientation1()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(2);
        $newTile2->setCoordY(-2);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
        $newTile3->setCoordY(-3);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(0);
        $newTile4->setCoordY(-4);
        $this->entityManager->persist($newTile4);
        $newTile5 = new TileMYR();
        $newTile5->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile5->setCoordX(0);
        $newTile5->setCoordY(-2);
        $this->entityManager->persist($newTile5);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(1);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientation2()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(-2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(-4);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(-3);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(-2);
        $newTile4->setCoordY(-2);
        $this->entityManager->persist($newTile4);
        $newTile5 = new TileMYR();
        $newTile5->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile5->setCoordX(-1);
        $newTile5->setCoordY(-1);
        $this->entityManager->persist($newTile5);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(2);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientation3()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-2);
        $newTile2->setCoordY(-2);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-2);
        $newTile3->setCoordY(0);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(-2);
        $newTile4->setCoordY(2);
        $this->entityManager->persist($newTile4);
        $newTile5 = new TileMYR();
        $newTile5->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile5->setCoordX(-1);
        $newTile5->setCoordY(1);
        $this->entityManager->persist($newTile5);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientation4()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(-1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-2);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
        $newTile3->setCoordY(2);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(0);
        $newTile4->setCoordY(4);
        $this->entityManager->persist($newTile4);
        $newTile5 = new TileMYR();
        $newTile5->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile5->setCoordX(-1);
        $newTile5->setCoordY(3);
        $this->entityManager->persist($newTile5);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(4);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientation5()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(4);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
        $newTile3->setCoordY(3);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(2);
        $newTile4->setCoordY(2);
        $this->entityManager->persist($newTile4);
        $newTile5 = new TileMYR();
        $newTile5->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile5->setCoordX(1);
        $newTile5->setCoordY(1);
        $this->entityManager->persist($newTile5);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(5);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientationImpossible()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(-1);
        $newTile4->setCoordY(1);
        $this->entityManager->persist($newTile4);
        $newTile5 = new TileMYR();
        $newTile5->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile5->setCoordX(1);
        $newTile5->setCoordY(1);
        $this->entityManager->persist($newTile5);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeSixWhenTileContainPrey()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(-1);
        $newTile4->setCoordY(1);
        $this->entityManager->persist($newTile4);
        $newTile5 = new TileMYR();
        $newTile5->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile5->setCoordX(1);
        $newTile5->setCoordY(1);
        $this->entityManager->persist($newTile5);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfUnknownType()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->entityManager->persist($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(-1);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneButNotEnoughLevel()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $this->entityManager->persist($tile);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(4);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
        $newTile3->setCoordY(3);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(2);
        $newTile4->setCoordY(2);
        $this->entityManager->persist($newTile4);
        $newTile5 = new TileMYR();
        $newTile5->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile5->setCoordX(1);
        $newTile5->setCoordY(1);
        $this->entityManager->persist($newTile5);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(5);
        $this->entityManager->persist($tileType);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testGetAllAvailablePositionsWithTileTypeZeroAndOrientationZero() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $chosenTile = $this->tileMYRRepository->findOneBy(["coord_X" => $chosenX, "coord_Y" => $chosenY]);
        $adjacentTile = $this->tileMYRRepository->findOneBy(["coord_X" => $chosenX + 1, "coord_Y" => $chosenY + 1]);
        $pivotMinusOne = $this->tileMYRRepository->findOneBy(["coord_X" => $chosenX - 1, "coord_Y" => $chosenY - 1]);
        $adjacentTileMinusOne = $this->tileMYRRepository->findOneBy(["coord_X" => $chosenX, "coord_Y" => $chosenY]);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($chosenTile);
        $gardenWorker->setPlayer($player);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        $expectedList1 = new ArrayCollection([$chosenTile, $adjacentTile]);
        $expectedList2 = new ArrayCollection([$pivotMinusOne, $adjacentTileMinusOne]);
        $expectedSize = 2;
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $chosenTile, $tileType);
        //THEN
        $boardTile1 = $result->first()->first()->getTile();
        $boardTile2 = $result->first()->last()->getTile();
        $boardTile3 = $result->last()->first()->getTile();
        $boardTile4 = $result->last()->last()->getTile();
        $resultList1 = new ArrayCollection([$boardTile1, $boardTile2]);
        $resultList2 = new ArrayCollection([$boardTile3, $boardTile4]);
        $this->assertSame($expectedSize, $result->count());
        $i = 0;
        foreach ($expectedList1 as $item) {
            $this->assertSame($item, $resultList1->get($i));
            ++$i;
        }
        $i = 0;
        foreach ($expectedList2 as $item) {
            $this->assertSame($item, $resultList2->get($i));
            ++$i;
        }
    }

    private function createGame(int $numberOfPlayers) : GameMYR
    {
        if($numberOfPlayers < MyrmesParameters::MIN_NUMBER_OF_PLAYER ||
            $numberOfPlayers > MyrmesParameters::MAX_NUMBER_OF_PLAYER) {
            throw new \Exception("TOO MUCH PLAYERS ON CREATE GAME");
        }
        $game = new GameMYR();
        for ($i = 0; $i < $numberOfPlayers; $i += 1) {
            $player = new PlayerMYR('test', $game);
            $game->addPlayer($player);
            $player->setGameMyr($game);
            $player->setColor("");
            $player->setPhase(MyrmesParameters::PHASE_EVENT);
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
            $this->entityManager->persist($player);
            $this->entityManager->persist($personalBoard);
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
        $this->entityManager->persist($season);
        $game->setMainBoardMYR($mainBoard);
        $game->setGameName("test");
        $game->setLaunched(true);
        $game->setGamePhase(MyrmesParameters::PHASE_INVALID);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $game;
    }

}