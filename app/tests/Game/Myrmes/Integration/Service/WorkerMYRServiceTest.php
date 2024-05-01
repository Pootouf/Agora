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
use App\Entity\Game\Myrmes\PlayerResourceMYR;
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
        $player->getPersonalBoardMYR()->setAnthillLevel(
            MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($player);
        $ant = new AnthillWorkerMYR();
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
        $gardenWorkerRepository = static::getContainer()
            ->get(GardenWorkerMYRRepository::class);

        // WHEN

        $this->workerMYRService->takeOutAnt(
            $player->getPersonalBoardMYR(), $hole);

        // THEN

        $gardenWorker = $gardenWorkerRepository->findOneBy(['player' => $player->getId()]);
        $this->assertNotNull($gardenWorker);
    }

    public function testTakeOutAntFailWithExitHoleOfOtherPlayer()
    {
        // GIVEN

        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setAnthillLevel(
            MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($player);
        $ant = new AnthillWorkerMYR();
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
        $player->getPersonalBoardMYR()->setBonus(-1);
        $this->entityManager->persist($player);
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $this->entityManager->persist($player);
        $ant = new AnthillWorkerMYR();
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 1, 1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 1, 1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile->setCoordX(1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile->setCoordY(1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile->setCoordX(2);
        $newTile->setCoordY(0);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
        $newTile3->setCoordY(-1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile2->setCoordY(-3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
        $newTile3->setCoordY(-2);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile->setCoordX(-1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile->setCoordX(-2);
        $newTile->setCoordY(0);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(3);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile2->setCoordX(2);
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile2->setCoordX(1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile->setCoordX(-2);
        $newTile->setCoordY(-2);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile2->setCoordX(-1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
        $newTile3->setCoordY(4);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile->setCoordX(-1);
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(0);
        $newTile2->setCoordY(-4);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile3->setCoordY(-2);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile->setCoordX(0);
        $newTile->setCoordY(4);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile->setCoordX(1);
        $newTile->setCoordY(-1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile3->setCoordX(1);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $newTile2->setCoordY(2);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(-1);
        $newTile3->setCoordY(1);
        $this->entityManager->persist($newTile3);
        $newTile4 = new TileMYR();
        $newTile4->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile4->setCoordX(-2);
        $newTile4->setCoordY(0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
    }

    public function testPlaceSpecialTileFarmWhenPlayerHaveEnoughResources()
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
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_FARM);
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
        $playerResources = $firstPlayer->getPersonalBoardMYR()->getPlayerResourceMYRs();
        foreach ($playerResources as $playerResourceMYR) {
            if($playerResourceMYR->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE) {
                $playerResourceMYR->setQuantity(12);
                $this->entityManager->persist($playerResourceMYR);
            }
        }
        $this->entityManager->flush();
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlaceSpecialTileFarmButPlayerDoNotHaveEnoughResources()
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
        $tileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_FARM);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
    }

    public function testPlaceSpecialTileQuarryWhenPlayerHaveEnoughResources()
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
        $newTile->setCoordY(-1);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY);
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
        $playerResources = $firstPlayer->getPersonalBoardMYR()->getPlayerResourceMYRs();
        foreach ($playerResources as $playerResourceMYR) {
            if($playerResourceMYR->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS) {
                $playerResourceMYR->setQuantity(12);
                $this->entityManager->persist($playerResourceMYR);
            }
        }
        $this->entityManager->flush();
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlaceSpecialTileQuarryButPlayerDoNotHaveEnoughResources()
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
        $tileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
    }

    public function testPlaceSpecialTileSubAnthillWhenPlayerHaveEnoughResources()
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
        $newTile->setCoordX(2);
        $newTile->setCoordY(0);
        $this->entityManager->persist($newTile);
        $newTile2 = new TileMYR();
        $newTile2->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile2->setCoordX(1);
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
        $newTile3->setCoordY(-1);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $playerResources = $firstPlayer->getPersonalBoardMYR()->getPlayerResourceMYRs();
        foreach ($playerResources as $playerResourceMYR) {
            if($playerResourceMYR->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_GRASS) {
                $playerResourceMYR->setQuantity(12);
                $this->entityManager->persist($playerResourceMYR);
            }
        }
        foreach ($playerResources as $playerResourceMYR) {
            if($playerResourceMYR->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_STONE) {
                $playerResourceMYR->setQuantity(12);
                $this->entityManager->persist($playerResourceMYR);
            }
        }
        foreach ($playerResources as $playerResourceMYR) {
            if($playerResourceMYR->getResource()->getDescription() == MyrmesParameters::RESOURCE_TYPE_DIRT) {
                $playerResourceMYR->setQuantity(12);
                $this->entityManager->persist($playerResourceMYR);
            }
        }
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlaceSpecialTileSubAnthillWhenPlayerDoNotHaveEnoughResources()
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
        $newTile2->setCoordY(1);
        $this->entityManager->persist($newTile2);
        $newTile3 = new TileMYR();
        $newTile3->setType(MyrmesParameters::STONE_TILE_TYPE);
        $newTile3->setCoordX(1);
        $newTile3->setCoordY(3);
        $this->entityManager->persist($newTile3);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setTile($tile);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(0);
        $this->entityManager->persist($gardenWorker);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $playerResources = $firstPlayer->getPersonalBoardMYR()->getPlayerResourceMYRs();
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
    }

    public function testGetAllAvailablePositionsWithTileTypeZeroAndOrientationZero() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $result = $this->giveExpectedResultForGiveAllAvailablePositionsForTypeZeroAndOrientationZero
                ($game, $player, $chosenX, $chosenY, true);
        $expectedList1 = $result->first();
        $expectedList2 = $result->last();
        $expectedSize = 2;
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
        7, 12, []);
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

    public function testGetAllAvailablePositionsWithTileTypeZeroAndOrientationZeroShouldReturnOnlyOneList() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 9;
        $chosenY = 18;
        $result = $this->giveExpectedResultForGiveAllAvailablePositionsForTypeZeroAndOrientationZero
                ($game, $player, $chosenX, $chosenY, true);
        $expectedList1 = $result->first();
        $expectedSize = 1;
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
            9, 18, []);
        //THEN
        $boardTile1 = $result->first()->first()->getTile();
        $boardTile2 = $result->first()->last()->getTile();
        $resultList1 = new ArrayCollection([$boardTile1, $boardTile2]);
        $this->assertSame($expectedSize, $result->count());
        $i = 0;
        foreach ($expectedList1 as $item) {
            $this->assertSame($item, $resultList1->get($i));
            ++$i;
        }
    }

    public function testGetAllAvailablePositionsWithTileTypeZeroAndOrientationZeroShouldReturnNothingBecauseNoAnt() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $tile = $this->tileMYRRepository->findOneBy(["coordX" => 7, "coordY" => 12]);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $tile, $tileType,
            45, 58, []);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailablePositionsForTileTypeZeroAndOrientationsOneToFiveShouldNotFail() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $result = $this->giveExpectedResultForGiveAllAvailablePositionsForTypeZeroAndOrientationZero
                ($game, $player, $chosenX, $chosenY, true);
        $expectedList1 = $result->first();
        for ($i = 1; $i <= 5; ++$i) {
            $tileType = new TileTypeMYR();
            $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
            $tileType->setOrientation($i);
            $this->entityManager->persist($tileType);
            $this->entityManager->flush();
            //WHEN
            $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
                $chosenX, $chosenY, []);
            //THEN
            $this->assertNotEmpty($result);
        }
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
            45, 58, []);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailablePositionsForTileTypeOneAndOrientationsOneToFiveShouldNotFail() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $result = $this->giveExpectedResultForGiveAllAvailablePositionsForTypeOneAndOrientationZero
        ($game, $player, $chosenX, $chosenY, true);
        $expectedList1 = $result->first();
        for ($i = 0; $i <= 2; ++$i) {
            $tileType = new TileTypeMYR();
            $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
            $tileType->setOrientation($i);
            $this->entityManager->persist($tileType);
            $this->entityManager->flush();
            //WHEN
            $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
                $chosenX, $chosenY, []);
            //THEN
            $this->assertNotEmpty($result);
        }
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(3);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
            $chosenX + 1, $chosenY, []);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailablePositionsForTileTypeTwoAndOrientationsOneToFiveShouldNotFail() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $result = $this->giveExpectedResultForGiveAllAvailablePositionsForTypeTwoAndOrientationZero
        ($game, $player, $chosenX, $chosenY, true);
        $expectedList1 = $result->first();
        for ($i = 0; $i <= 5; ++$i) {
            $tileType = new TileTypeMYR();
            $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
            $tileType->setOrientation($i);
            $this->entityManager->persist($tileType);
            $this->entityManager->flush();
            //WHEN
            $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
                $chosenX, $chosenY, []);
            //THEN
            $this->assertNotEmpty($result);
        }
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
            45, 58, []);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailablePositionsForTileTypeThreeAndOrientationsOneToFiveShouldNotFail() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $result = $this->giveExpectedResultForGiveAllAvailablePositionsForTypeThreeAndOrientationZero
        ($game, $player, $chosenX, $chosenY, true);
        $expectedList1 = $result->first();
        for ($i = 0; $i <= 5; ++$i) {
            $tileType = new TileTypeMYR();
            $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
            $tileType->setOrientation($i);
            $this->entityManager->persist($tileType);
            $this->entityManager->flush();
            //WHEN
            $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
                $chosenX, $chosenY, []);
            //THEN
            $this->assertNotEmpty($result);
        }
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
            45, 58, []);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailablePositionsForTileTypeFourAndOrientationsOneToFiveShouldNotFail() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $result = $this->giveExpectedResultForGiveAllAvailablePositionsForTypeFourAndOrientationZero
        ($game, $player, $chosenX, $chosenY, true);
        $expectedList1 = $result->first();
        for ($i = 0; $i <= 11; ++$i) {
            $tileType = new TileTypeMYR();
            $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
            $tileType->setOrientation($i);
            $this->entityManager->persist($tileType);
            $this->entityManager->flush();
            //WHEN
            $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
                $chosenX, $chosenY, []);
            //THEN
            $this->assertNotEmpty($result);
        }
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(12);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
            45, 58, []);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailablePositionsForTileTypeFiveAndOrientationsOneToFiveShouldNotFail() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $result = $this->giveExpectedResultForGiveAllAvailablePositionsForTypeFiveAndOrientationZero
        ($game, $player, $chosenX, $chosenY, true);
        $expectedList1 = $result->first();
        for ($i = 0; $i <= 5; ++$i) {
            $tileType = new TileTypeMYR();
            $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
            $tileType->setOrientation($i);
            $this->entityManager->persist($tileType);
            $this->entityManager->flush();
            //WHEN
            $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
                $chosenX, $chosenY, []);
            //THEN
            $this->assertNotEmpty($result);
        }
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
            45, 58, []);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailablePositionsForTileTypeSixAndOrientationsOneToFiveShouldNotFail() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $result = $this->giveExpectedResultForGiveAllAvailablePositionsForTypeSixAndOrientationZero
        ($game, $player, $chosenX, $chosenY, true);
        $expectedList1 = $result->first();
        for ($i = 0; $i <= 5; ++$i) {
            $tileType = new TileTypeMYR();
            $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
            $tileType->setOrientation($i);
            $this->entityManager->persist($tileType);
            $this->entityManager->flush();
            //WHEN
            $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
                $chosenX, $chosenY, []);
            //THEN
            $this->assertNotEmpty($result);
        }
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
            45, 58, []);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailablePositionsForTileNonExistingTile() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $result = $this->giveExpectedResultForGiveAllAvailablePositionsForTypeSixAndOrientationZero
        ($game, $player, $chosenX, $chosenY, true);
        $expectedList1 = $result->first();
        $tileType = new TileTypeMYR();
        $tileType->setType(-1);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllAvailablePositions($player, $expectedList1->first(), $tileType,
            45, 58, []);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailableCoordinatesForSpecialTileFarmShouldReturnEmptyArray() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $tile->setCoordY($chosenY);
        $tile->setCoordX($chosenX);
        $this->entityManager->persist($tile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_FARM);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllCoordinatesFromTileType($player, $tile, $tileType);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailableCoordinatesForSpecialTileQuarryShouldReturnEmptyArray() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $tile->setCoordY($chosenY);
        $tile->setCoordX($chosenX);
        $this->entityManager->persist($tile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllCoordinatesFromTileType($player, $tile, $tileType);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailableCoordinatesForSpecialTileSubAnthillShouldReturnEmptyArray() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $tile->setCoordY($chosenY);
        $tile->setCoordX($chosenX);
        $this->entityManager->persist($tile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllCoordinatesFromTileType($player, $tile, $tileType);
        //THEN
        $this->assertEmpty($result);
    }

    public function testGetAllAvailableCoordinatesForUnknownTileShouldReturnEmptyArray() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $chosenX = 7;
        $chosenY = 12;
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $tile->setCoordY($chosenY);
        $tile->setCoordX($chosenX);
        $this->entityManager->persist($tile);
        $tileType = new TileTypeMYR();
        $tileType->setType(-1);
        $tileType->setOrientation(6);
        $this->entityManager->persist($tileType);
        $this->entityManager->flush();
        //WHEN
        $result = $this->workerMYRService->getAllCoordinatesFromTileType($player, $tile, $tileType);
        //THEN
        $this->assertEmpty($result);
    }

    public function testPlaceTwoPheromoneOfTypeZero()
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
        $playerPheromone = new PheromonMYR();
        $playerPheromone->setPlayer($firstPlayer);
        $playerPheromone->setHarvested(false);
        $playerPheromone->setType($tileType);
        $this->entityManager->persist($playerPheromone);
        $firstPlayer->addPheromonMYR($playerPheromone);
        $this->entityManager->persist($firstPlayer);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneWhenPlayerHaveBonus()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_POINT);
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
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
        // THEN
        $this->assertEquals(MyrmesParameters::PHEROMONE_TYPE_LEVEL[MyrmesParameters::PHEROMONE_TYPE_ZERO] + 1,
            $firstPlayer->getScore());
    }

    public function testPlacePheromoneOnWater()
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
        $newTile->setType(MyrmesParameters::WATER_TILE_TYPE);
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
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType, 0, 0);
    }

    public function testGetAvailablePheromonesShouldReturnUnmodifiedTable()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $expected = new ArrayCollection();
        for ($i = MyrmesParameters::PHEROMONE_TYPE_ZERO; $i <= MyrmesParameters::PHEROMONE_TYPE_SIX; ++$i) {
            $remaining = MyrmesParameters::PHEROMONE_TYPE_AMOUNT[$i] ;
            if ($remaining > 0 && $i == 0) {
                $expected->add([$i, $remaining, MyrmesParameters::PHEROMONE_TYPE_ORIENTATIONS[$i]]);
            }
        }
        for ($i = MyrmesParameters::SPECIAL_TILE_TYPE_FARM; $i <= MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL; ++$i) {
            $remaining = MyrmesParameters::SPECIAL_TILE_TYPE_AMOUNT[$i];
            if ($remaining > 0 && $i == 0) {
                $expected->add([$i, $remaining, MyrmesParameters::SPECIAL_TILES_TYPE_ORIENTATIONS[$i]]);
            }
        }
        // WHEN
        $result = $this->workerMYRService->getAvailablePheromones($firstPlayer);
        // THEN
        $this->assertEquals($expected, $result);
    }

    public function testGetAvailablePheromonesShouldReturnModifiedTable()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $playerPheromone = new PheromonMYR();
        $playerPheromone->setPlayer($firstPlayer);
        $playerPheromone->setHarvested(false);
        $playerPheromone->setType($tileType);
        $this->entityManager->persist($playerPheromone);
        $firstPlayer->addPheromonMYR($playerPheromone);
        $expected = new ArrayCollection();
        $remaining = MyrmesParameters::PHEROMONE_TYPE_AMOUNT[MyrmesParameters::PHEROMONE_TYPE_ZERO] - 1;
        $expected->add([MyrmesParameters::PHEROMONE_TYPE_ZERO, $remaining, MyrmesParameters::PHEROMONE_TYPE_ORIENTATIONS[0]]);
        for ($i = MyrmesParameters::PHEROMONE_TYPE_ONE; $i <= MyrmesParameters::PHEROMONE_TYPE_SIX; ++$i) {
            $remaining = MyrmesParameters::PHEROMONE_TYPE_AMOUNT[$i] ;
            if ($remaining > 0 && $i == 0) {
                $expected->add([$i, $remaining, MyrmesParameters::PHEROMONE_TYPE_ORIENTATIONS[$i]]);
            }
        }
        for ($i = MyrmesParameters::SPECIAL_TILE_TYPE_FARM; $i <= MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL; ++$i) {
            $remaining = MyrmesParameters::SPECIAL_TILE_TYPE_AMOUNT[$i];
            if ($remaining > 0 && $i == 0) {
                $expected->add([$i, $remaining, MyrmesParameters::SPECIAL_TILES_TYPE_ORIENTATIONS[$i]]);
            }
        }
        // WHEN
        $result = $this->workerMYRService->getAvailablePheromones($firstPlayer);
        // THEN
        $this->assertEquals($expected, $result);
    }

    public function testMoveWorkerOnWaterTileShouldThrowExcpetion()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();

        $workerTile = new TileMYR();
        $workerTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $workerTile->setCoordY(0);
        $workerTile->setCoordX(0);
        $this->entityManager->persist($workerTile);

        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(1);
        $gardenWorker->setTile($workerTile);
        $this->entityManager->persist($gardenWorker);

        $waterTile = new TileMYR();
        $waterTile->setType(MyrmesParameters::WATER_TILE_TYPE);
        $waterTile->setCoordX(1);
        $waterTile->setCoordY(1);
        $this->entityManager->persist($waterTile);

        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workerMYRService->workerMove($firstPlayer, $gardenWorker, MyrmesParameters::DIRECTION_SOUTH_EAST);
    }

    public function testMoveWorkerOnPreyShouldThrowExcpetionBecauseNotEnoughWarriorsToBeat()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();

        $workerTile = new TileMYR();
        $workerTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $workerTile->setCoordY(0);
        $workerTile->setCoordX(0);
        $this->entityManager->persist($workerTile);

        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(1);
        $gardenWorker->setTile($workerTile);
        $this->entityManager->persist($gardenWorker);

        $dirtTile = new TileMYR();
        $dirtTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $dirtTile->setCoordX(1);
        $dirtTile->setCoordY(1);
        $this->entityManager->persist($dirtTile);

        $prey = new PreyMYR();
        $prey->setType(MyrmesParameters::LADYBUG_TYPE);
        $prey->setMainBoardMYR($game->getMainBoardMYR());
        $prey->setTile($dirtTile);
        $this->entityManager->persist($prey);

        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workerMYRService->workerMove($firstPlayer, $gardenWorker, MyrmesParameters::DIRECTION_SOUTH_EAST);
    }

    public function testMoveWorkerOnEnemyPheromoneShouldThrowExcpetionBecauseNotEnoughWarriorsToBeat()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();

        $workerTile = new TileMYR();
        $workerTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $workerTile->setCoordY(0);
        $workerTile->setCoordX(0);
        $this->entityManager->persist($workerTile);

        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(1);
        $gardenWorker->setTile($workerTile);
        $this->entityManager->persist($gardenWorker);

        $dirtTile = new TileMYR();
        $dirtTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $dirtTile->setCoordX(1);
        $dirtTile->setCoordY(1);
        $this->entityManager->persist($dirtTile);

        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($game->getPlayers()->last());
        $pheromone->setHarvested(false);

        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($dirtTile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);

        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);


        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->workerMYRService->workerMove($firstPlayer, $gardenWorker, MyrmesParameters::DIRECTION_SOUTH_EAST);
    }

    public function testMoveWorkerOnOtherTile()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();

        $workerTile = new TileMYR();
        $workerTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $workerTile->setCoordY(0);
        $workerTile->setCoordX(0);
        $this->entityManager->persist($workerTile);

        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(1);
        $gardenWorker->setTile($workerTile);
        $this->entityManager->persist($gardenWorker);

        $dirtTile = new TileMYR();
        $dirtTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $dirtTile->setCoordX(1);
        $dirtTile->setCoordY(1);
        $this->entityManager->persist($dirtTile);

        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);

        $this->entityManager->flush();
        //WHEN
        $this->workerMYRService->workerMove($firstPlayer, $gardenWorker, MyrmesParameters::DIRECTION_SOUTH_EAST);
        //THEN
        $this->assertEquals($dirtTile, $gardenWorker->getTile());
    }

    public function testMoveWorkerOnOtherTileWithPreyOnIt()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setWarriorsCount(1);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());

        $workerTile = new TileMYR();
        $workerTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $workerTile->setCoordY(0);
        $workerTile->setCoordX(0);
        $this->entityManager->persist($workerTile);

        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(1);
        $gardenWorker->setTile($workerTile);
        $this->entityManager->persist($gardenWorker);

        $dirtTile = new TileMYR();
        $dirtTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $dirtTile->setCoordX(1);
        $dirtTile->setCoordY(1);
        $this->entityManager->persist($dirtTile);

        $prey = new PreyMYR();
        $prey->setType(MyrmesParameters::LADYBUG_TYPE);
        $prey->setMainBoardMYR($game->getMainBoardMYR());
        $prey->setTile($dirtTile);
        $this->entityManager->persist($prey);

        $this->entityManager->flush();
        //WHEN
        $this->workerMYRService->workerMove($firstPlayer, $gardenWorker, MyrmesParameters::DIRECTION_SOUTH_EAST);
        //THEN
        $this->assertEquals($dirtTile, $gardenWorker->getTile());
        $this->assertEquals(0, $firstPlayer->getPersonalBoardMYR()->getWarriorsCount());
    }

    public function testMoveWorkerOnPlayerPheromone()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();

        $workerTile = $this->tileMYRRepository->findOneBy(["coordX" => 1, "coordY" => 16]);

        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(1);
        $gardenWorker->setTile($workerTile);
        $this->entityManager->persist($gardenWorker);

        $dirtTile = $this->tileMYRRepository->findOneBy(["coordX" => 2, "coordY" => 17]);

        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($firstPlayer);
        $pheromone->setHarvested(false);

        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($dirtTile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);

        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);


        $this->entityManager->flush();
        //WHEN
        $this->workerMYRService->workerMove($firstPlayer, $gardenWorker, MyrmesParameters::DIRECTION_SOUTH_EAST);
        // THEN
        $this->assertEquals($dirtTile, $gardenWorker->getTile());
    }

    public function testMoveWorkerOnEnemyPheromone()
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setWarriorsCount(1);
        $this->entityManager->persist($firstPlayer->getPersonalBoardMYR());

        $workerTile = new TileMYR();
        $workerTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $workerTile->setCoordY(0);
        $workerTile->setCoordX(0);
        $this->entityManager->persist($workerTile);

        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setPlayer($firstPlayer);
        $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
        $gardenWorker->setShiftsCount(1);
        $gardenWorker->setTile($workerTile);
        $this->entityManager->persist($gardenWorker);

        $dirtTile = new TileMYR();
        $dirtTile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $dirtTile->setCoordX(1);
        $dirtTile->setCoordY(1);
        $this->entityManager->persist($dirtTile);

        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $this->entityManager->persist($tileType);
        $pheromone = new PheromonMYR();
        $pheromone->setType($tileType);
        $pheromone->setPlayer($game->getPlayers()->last());
        $pheromone->setHarvested(false);

        $pheromoneTile = new PheromonTileMYR();
        $pheromoneTile->setTile($dirtTile);
        $pheromoneTile->setPheromonMYR($pheromone);
        $pheromoneTile->setMainBoard($game->getMainBoardMYR());
        $this->entityManager->persist($pheromoneTile);

        $pheromone->addPheromonTile($pheromoneTile);
        $this->entityManager->persist($pheromone);

        $this->entityManager->flush();
        //WHEN
        $this->workerMYRService->workerMove($firstPlayer, $gardenWorker, MyrmesParameters::DIRECTION_SOUTH_EAST);
        // THEN
        $this->assertEquals($dirtTile, $gardenWorker->getTile());
        $this->assertEquals(0, $firstPlayer->getPersonalBoardMYR()->getWarriorsCount());
    }

    private function createGame(int $numberOfPlayers) : GameMYR
    {
        if($numberOfPlayers < MyrmesParameters::MIN_NUMBER_OF_PLAYER ||
            $numberOfPlayers > MyrmesParameters::MAX_NUMBER_OF_PLAYER) {
            throw new \Exception("TOO MUCH PLAYERS ON CREATE GAME");
        }
        $game = new GameMYR();
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

            $resourceStone = new ResourceMYR();
            $resourceStone->setDescription(MyrmesParameters::RESOURCE_TYPE_STONE);
            $this->entityManager->persist($resourceStone);
            $playerResourceStone = new PlayerResourceMYR();
            $playerResourceStone->setResource($resourceStone);
            $playerResourceStone->setQuantity(0);
            $playerResourceStone->setPersonalBoard($personalBoard);
            $this->entityManager->persist($playerResourceStone);

            $resourceGrass = new ResourceMYR();
            $resourceGrass->setDescription(MyrmesParameters::RESOURCE_TYPE_GRASS);
            $this->entityManager->persist($resourceGrass);
            $playerResourceGrass = new PlayerResourceMYR();
            $playerResourceGrass->setResource($resourceGrass);
            $playerResourceGrass->setQuantity(0);
            $playerResourceGrass->setPersonalBoard($personalBoard);
            $this->entityManager->persist($playerResourceGrass);

            $resourceDirt = new ResourceMYR();
            $resourceDirt->setDescription(MyrmesParameters::RESOURCE_TYPE_DIRT);
            $this->entityManager->persist($resourceDirt);
            $playerResourceDirt = new PlayerResourceMYR();
            $playerResourceDirt->setResource($resourceDirt);
            $playerResourceDirt->setQuantity(0);
            $playerResourceDirt->setPersonalBoard($personalBoard);
            $this->entityManager->persist($playerResourceDirt);

            $player->getPersonalBoardMYR()->addPlayerResourceMYR($playerResourceStone);
            $player->getPersonalBoardMYR()->addPlayerResourceMYR($playerResourceGrass);
            $player->getPersonalBoardMYR()->addPlayerResourceMYR($playerResourceDirt);

            $this->entityManager->persist($player);
            $this->entityManager->persist($personalBoard);
            $this->entityManager->flush();
        }
        $this->entityManager->flush();

        return $game;
    }

    private function giveExpectedResultForGiveAllAvailablePositionsForTypeZeroAndOrientationZero
    (GameMYR $game, PlayerMYR $player, int $coordX, int $coordY, bool $hasAnt) : ArrayCollection
    {
        $chosenTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);
        $pivotMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $adjacentTileMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        if ($hasAnt) {
            $gardenWorker = new GardenWorkerMYR();
            $gardenWorker->setTile($chosenTile);
            $gardenWorker->setPlayer($player);
            $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
            $gardenWorker->setShiftsCount(0);
            $this->entityManager->persist($gardenWorker);
        }
        $expectedList1 = new ArrayCollection([$chosenTile, $adjacentTile]);
        $expectedList2 = new ArrayCollection([$pivotMinusOne, $adjacentTileMinusOne]);
        return new ArrayCollection([$expectedList1, $expectedList2]);
    }

    private function giveExpectedResultForGiveAllAvailablePositionsForTypeOneAndOrientationZero
    (GameMYR $game, PlayerMYR $player, int $coordX, int $coordY, bool $hasAnt) : ArrayCollection
    {
        $chosenTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);
        $adjacentTile2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $pivotMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $adjacentTileMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTileMinusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 2,
            "coordY" => $coordY - 2]);
        $pivotPlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);
        $adjacentTilePlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTilePlusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 2,
            "coordY" => $coordY + 2]);
        if ($hasAnt) {
            $gardenWorker = new GardenWorkerMYR();
            $gardenWorker->setTile($chosenTile);
            $gardenWorker->setPlayer($player);
            $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
            $gardenWorker->setShiftsCount(0);
            $this->entityManager->persist($gardenWorker);
        }
        $expectedList1 = new ArrayCollection([$chosenTile, $adjacentTile, $adjacentTile2]);
        $expectedList2 = new ArrayCollection([$pivotMinusOne, $adjacentTileMinusOne, $adjacentTileMinusOne2]);
        $expectedList3 = new ArrayCollection([$pivotPlusOne, $adjacentTilePlusOne, $adjacentTilePlusOne2]);
        return new ArrayCollection([$expectedList1, $expectedList2, $expectedList3]);
    }

    private function giveExpectedResultForGiveAllAvailablePositionsForTypeTwoAndOrientationZero
    (GameMYR $game, PlayerMYR $player, int $coordX, int $coordY, bool $hasAnt) : ArrayCollection
    {
        $chosenTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);
        $adjacentTile2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY - 1]);
        $pivotMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX -1, "coordY" => $coordY - 1]);
        $adjacentTileMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTileMinusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX,
            "coordY" => $coordY - 2]);
        $pivotPlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY + 1]);
        $adjacentTilePlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTilePlusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX,
            "coordY" => $coordY + 2]);
        if ($hasAnt) {
            $gardenWorker = new GardenWorkerMYR();
            $gardenWorker->setTile($chosenTile);
            $gardenWorker->setPlayer($player);
            $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
            $gardenWorker->setShiftsCount(0);
            $this->entityManager->persist($gardenWorker);
        }
        $expectedList1 = new ArrayCollection([$chosenTile, $adjacentTile, $adjacentTile2]);
        $expectedList2 = new ArrayCollection([$pivotMinusOne, $adjacentTileMinusOne, $adjacentTileMinusOne2]);
        $expectedList3 = new ArrayCollection([$pivotPlusOne, $adjacentTilePlusOne, $adjacentTilePlusOne2]);
        return new ArrayCollection([$expectedList1, $expectedList2, $expectedList3]);
    }
    private function giveExpectedResultForGiveAllAvailablePositionsForTypeThreeAndOrientationZero
    (GameMYR $game, PlayerMYR $player, int $coordX, int $coordY, bool $hasAnt) : ArrayCollection
    {
        $chosenTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);
        $adjacentTile2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY - 1]);
        $adjacentTile3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 2, "coordY" => $coordY]);
        $pivotMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX -1, "coordY" => $coordY - 1]);
        $adjacentTileMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTileMinusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX,
            "coordY" => $coordY - 2]);
        $adjacentTileMinusOne3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1,
            "coordY" => $coordY - 1]);
        $pivotPlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY + 1]);
        $adjacentTilePlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTilePlusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX,
            "coordY" => $coordY + 2]);
        $adjacentTilePlusOne3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1,
            "coordY" => $coordY + 1]);
        $pivotPlusTwo = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 2, "coordY" => $coordY]);
        $adjacentTilePlusTwo1 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $adjacentTilePlusTwo2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1,
            "coordY" => $coordY + 1]);
        $adjacentTilePlusTwo3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX,
            "coordY" => $coordY]);
        if ($hasAnt) {
            $gardenWorker = new GardenWorkerMYR();
            $gardenWorker->setTile($chosenTile);
            $gardenWorker->setPlayer($player);
            $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
            $gardenWorker->setShiftsCount(0);
            $this->entityManager->persist($gardenWorker);
        }
        $expectedList1 = new ArrayCollection([$chosenTile, $adjacentTile, $adjacentTile2, $adjacentTile3]);
        $expectedList2 = new ArrayCollection([$pivotMinusOne, $adjacentTileMinusOne, $adjacentTileMinusOne2, $adjacentTileMinusOne3]);
        $expectedList3 = new ArrayCollection([$pivotPlusOne, $adjacentTilePlusOne, $adjacentTilePlusOne2, $adjacentTilePlusOne3]);
        $expectedList4 = new ArrayCollection([$pivotPlusTwo, $adjacentTilePlusTwo1, $adjacentTilePlusTwo2, $adjacentTilePlusTwo3]);
        return new ArrayCollection([$expectedList1, $expectedList2, $expectedList3, $expectedList4]);
    }

    private function giveExpectedResultForGiveAllAvailablePositionsForTypeFourAndOrientationZero
    (GameMYR $game, PlayerMYR $player, int $coordX, int $coordY, bool $hasAnt) : ArrayCollection
    {
        $chosenTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);
        $adjacentTile2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 2, "coordY" => $coordY + 2]);
        $adjacentTile3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 2]);

        $pivotMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX -1, "coordY" => $coordY - 1]);
        $adjacentTileMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY + 1]);
        $adjacentTileMinusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTileMinusOne3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);

        $pivotPlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 2, "coordY" => $coordY - 2]);
        $adjacentTilePlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $adjacentTilePlusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTilePlusOne3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 2, "coordY" => $coordY]);

        $pivotPlusTwo = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY - 2]);
        $adjacentTilePlusTwo1 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY - 1]);
        $adjacentTilePlusTwo2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 2]);
        $adjacentTilePlusTwo3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX,
            "coordY" => $coordY]);

        if ($hasAnt) {
            $gardenWorker = new GardenWorkerMYR();
            $gardenWorker->setTile($chosenTile);
            $gardenWorker->setPlayer($player);
            $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
            $gardenWorker->setShiftsCount(0);
            $this->entityManager->persist($gardenWorker);
        }
        $expectedList1 = new ArrayCollection([$chosenTile, $adjacentTile, $adjacentTile2, $adjacentTile3]);
        $expectedList2 = new ArrayCollection([$pivotMinusOne, $adjacentTileMinusOne, $adjacentTileMinusOne2, $adjacentTileMinusOne3]);
        $expectedList3 = new ArrayCollection([$pivotPlusOne, $adjacentTilePlusOne, $adjacentTilePlusOne2, $adjacentTilePlusOne3]);
        $expectedList4 = new ArrayCollection([$pivotPlusTwo, $adjacentTilePlusTwo1, $adjacentTilePlusTwo2, $adjacentTilePlusTwo3]);
        return new ArrayCollection([$expectedList1, $expectedList2, $expectedList3, $expectedList4]);
    }

    private function giveExpectedResultForGiveAllAvailablePositionsForTypeFiveAndOrientationZero
    (GameMYR $game, PlayerMYR $player, int $coordX, int $coordY, bool $hasAnt) : ArrayCollection
    {
        $chosenTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 2]);
        $adjacentTile2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY - 1]);
        $adjacentTile3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);
        $adjacentTile4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 3]);


        $pivotMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY - 2]);
        $adjacentTileMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTileMinusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);
        $adjacentTileMinusOne3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY - 1]);
        $adjacentTileMinusOne4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY - 3]);

        $pivotPlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY + 1]);
        $adjacentTilePlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY + 3]);
        $adjacentTilePlusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 4]);
        $adjacentTilePlusOne3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 2]);
        $adjacentTilePlusOne4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);

        $pivotPlusTwo = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $adjacentTilePlusTwo1 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY + 1]);
        $adjacentTilePlusTwo2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 2]);
        $adjacentTilePlusTwo3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTilePlusTwo4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY - 2]);

        $pivotMinusTwo = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 3]);
        $adjacentTileMinusTwo1 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $adjacentTileMinusTwo2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTileMinusTwo3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY - 2]);
        $adjacentTileMinusTwo4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY - 4]);

        if ($hasAnt) {
            $gardenWorker = new GardenWorkerMYR();
            $gardenWorker->setTile($chosenTile);
            $gardenWorker->setPlayer($player);
            $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
            $gardenWorker->setShiftsCount(0);
            $this->entityManager->persist($gardenWorker);
        }
        $expectedList1 = new ArrayCollection([$chosenTile, $adjacentTile, $adjacentTile2, $adjacentTile3, $adjacentTile4]);
        $expectedList2 = new ArrayCollection([$pivotMinusOne, $adjacentTileMinusOne, $adjacentTileMinusOne2, $adjacentTileMinusOne3, $adjacentTileMinusOne4]);
        $expectedList3 = new ArrayCollection([$pivotPlusOne, $adjacentTilePlusOne, $adjacentTilePlusOne2, $adjacentTilePlusOne3, $adjacentTilePlusOne4]);
        $expectedList4 = new ArrayCollection([$pivotPlusTwo, $adjacentTilePlusTwo1, $adjacentTilePlusTwo2, $adjacentTilePlusTwo3, $adjacentTilePlusTwo4]);
        $expectedList5 = new ArrayCollection([$pivotMinusTwo, $adjacentTileMinusTwo1, $adjacentTileMinusTwo2, $adjacentTileMinusTwo3, $adjacentTileMinusTwo4]);
        return new ArrayCollection([$expectedList1, $expectedList2, $expectedList3, $expectedList4, $expectedList5]);
    }

    private function giveExpectedResultForGiveAllAvailablePositionsForTypeSixAndOrientationZero
    (GameMYR $game, PlayerMYR $player, int $coordX, int $coordY, bool $hasAnt) : ArrayCollection
    {
        $chosenTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTile = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);
        $adjacentTile2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY - 1]);
        $adjacentTile3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 2, "coordY" => $coordY + 2]);
        $adjacentTile4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 2, "coordY" => $coordY]);
        $adjacentTile5 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 2, "coordY" => $coordY + 2]);


        $pivotMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY + 1]);
        $adjacentTileMinusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTileMinusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 2]);
        $adjacentTileMinusOne3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);
        $adjacentTileMinusOne4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY - 1]);
        $adjacentTileMinusOne5 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 3]);


        $pivotPlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 2, "coordY" => $coordY + 2]);
        $adjacentTilePlusOne = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $adjacentTilePlusOne2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTilePlusOne3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 2]);
        $adjacentTilePlusOne4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 4]);
        $adjacentTilePlusOne5 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY + 3]);

        $pivotPlusTwo = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $adjacentTilePlusTwo1 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY + 1]);
        $adjacentTilePlusTwo2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 2]);
        $adjacentTilePlusTwo3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTilePlusTwo4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY - 2]);
        $adjacentTilePlusTwo5 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 2, "coordY" => $coordY]);

        $pivotMinusTwo = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 2, "coordY" => $coordY]);
        $adjacentTileMinusTwo1 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);
        $adjacentTileMinusTwo2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY - 2]);
        $adjacentTileMinusTwo3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTileMinusTwo4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY + 2]);
        $adjacentTileMinusTwo5 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX + 1, "coordY" => $coordY + 1]);

        $pivotMinusThree = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 2, "coordY" => $coordY - 2]);
        $adjacentTileMinusThree1 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 3]);
        $adjacentTileMinusThree2 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY - 4]);
        $adjacentTileMinusThree3 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY - 2]);
        $adjacentTileMinusThree4 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX, "coordY" => $coordY]);
        $adjacentTileMinusThree5 = $this->tileMYRRepository->findOneBy(["coordX" => $coordX - 1, "coordY" => $coordY - 1]);

        if ($hasAnt) {
            $gardenWorker = new GardenWorkerMYR();
            $gardenWorker->setTile($chosenTile);
            $gardenWorker->setPlayer($player);
            $gardenWorker->setMainBoardMYR($game->getMainBoardMYR());
            $gardenWorker->setShiftsCount(0);
            $this->entityManager->persist($gardenWorker);
        }
        $expectedList1 = new ArrayCollection([$chosenTile, $adjacentTile, $adjacentTile2, $adjacentTile3,
            $adjacentTile4, $adjacentTile5]);
        $expectedList2 = new ArrayCollection([$pivotMinusOne, $adjacentTileMinusOne, $adjacentTileMinusOne2,
            $adjacentTileMinusOne3, $adjacentTileMinusOne4, $adjacentTileMinusOne5]);
        $expectedList3 = new ArrayCollection([$pivotPlusOne, $adjacentTilePlusOne, $adjacentTilePlusOne2,
            $adjacentTilePlusOne3, $adjacentTilePlusOne4, $adjacentTilePlusOne5]);
        $expectedList4 = new ArrayCollection([$pivotPlusTwo, $adjacentTilePlusTwo1, $adjacentTilePlusTwo2,
            $adjacentTilePlusTwo3, $adjacentTilePlusTwo4, $adjacentTilePlusTwo5]);
        $expectedList5 = new ArrayCollection([$pivotMinusTwo, $adjacentTileMinusTwo1, $adjacentTileMinusTwo2,
            $adjacentTileMinusTwo3, $adjacentTileMinusTwo4, $adjacentTileMinusTwo5]);
        $expectedList6 = new ArrayCollection([$pivotMinusThree, $adjacentTileMinusThree1, $adjacentTileMinusThree2,
            $adjacentTileMinusThree3, $adjacentTileMinusThree4, $adjacentTileMinusThree5]);
        return new ArrayCollection([$expectedList1, $expectedList2, $expectedList3, $expectedList4,
            $expectedList5, $expectedList6]);
    }

}