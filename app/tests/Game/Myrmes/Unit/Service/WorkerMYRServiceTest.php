<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\AnthillWorkerMYR;
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
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\GardenWorkerMYRRepository;
use App\Repository\Game\Myrmes\AnthillWorkerMYRRepository;
use App\Repository\Game\Myrmes\PheromonMYRRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
use App\Service\Game\Myrmes\MYRService;
use App\Service\Game\Myrmes\WorkerMYRService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class WorkerMYRServiceTest extends TestCase
{

    private EntityManagerInterface $entityManager;
    private MYRService $MYRService;
    private AnthillHoleMYRRepository $anthillHoleMYRRepository;
    private PheromonMYRRepository $pheromonMYRRepository;
    private PreyMYRRepository $preyMYRRepository;
    private TileMYRRepository $tileMYRRepository;
    private PlayerResourceMYRRepository $playerResourceMYRRepository;
    private ResourceMYRRepository $resourceMYRRepository;
    private TileTypeMYRRepository $tileTypeMYRRepository;
    private AnthillWorkerMYRRepository $anthillWorkerMYRRepository;

    private WorkerMYRService $workerMYRService;

    private GardenWorkerMYRRepository $gardenWorkerMYRRepository;

    protected function setUp() : void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->MYRService = $this->createMock(MYRService::class);
        $this->anthillHoleMYRRepository = $this->createMock(AnthillHoleMYRRepository::class);
        $this->pheromonMYRRepository = $this->createMock(PheromonMYRRepository::class);
        $this->preyMYRRepository = $this->createMock(PreyMYRRepository::class);
        $this->tileMYRRepository = $this->createMock(TileMYRRepository::class);
        $this->playerResourceMYRRepository = $this->createMock(PlayerResourceMYRRepository::class);
        $this->resourceMYRRepository = $this->createMock(ResourceMYRRepository::class);
        $this->tileTypeMYRRepository = $this->createMock(TileTypeMYRRepository::class);
        $this->gardenWorkerMYRRepository = $this->createMock(GardenWorkerMYRRepository::class);
        $this->anthillWorkerMYRRepository = $this->createMock(AnthillWorkerMYRRepository::class);
        $this->workerMYRService = new WorkerMYRService(
            $this->entityManager,
            $this->MYRService,
            $this->anthillWorkerMYRRepository,
            $this->anthillHoleMYRRepository,
            $this->pheromonMYRRepository,
            $this->preyMYRRepository,
            $this->tileMYRRepository,
            $this->playerResourceMYRRepository,
            $this->resourceMYRRepository,
            $this->tileTypeMYRRepository,
            $this->gardenWorkerMYRRepository
        );
    }

    public function testTakeOutAntSuccessWithValidAntAndAnthillHole()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $hole = new AnthillHoleMYR();
        $hole->setMainBoardMYR($game->getMainBoardMYR());
        $hole->setPlayer($player);
        $this->anthillWorkerMYRRepository->method("findOneBy")->willReturn($ant);

        // WHEN
        $this->workerMYRService->takeOutAnt($player->getPersonalBoardMYR(), $hole);

        // THEN
        $this->expectNotToPerformAssertions();
    }

    public function testTakeOutAntFailWithNoMoreFreeAnts()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setWorkFloor(2);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $hole = new AnthillHoleMYR();
        $hole->setMainBoardMYR($game->getMainBoardMYR());
        $hole->setPlayer($player);
        $this->anthillWorkerMYRRepository->method("findOneBy")->willReturn(null);

        // THEN
        $this->expectException(\Exception::class);

        // THEN
        $this->workerMYRService->takeOutAnt($player->getPersonalBoardMYR(), $hole);
    }

    public function testTakeOutAntFailIfNotAnthillHoleOfThePlayer()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $hole = new AnthillHoleMYR();
        $hole->setMainBoardMYR($game->getMainBoardMYR());
        $hole->setPlayer($game->getPlayers()->last());
        $this->anthillWorkerMYRRepository->method("findOneBy")->willReturn($ant);

        // THEN
        $this->expectException(\Exception::class);

        // THEN
        $this->workerMYRService->takeOutAnt($player->getPersonalBoardMYR(), $hole);
    }

    public function testTakeOutAntFailIfAlreadyAnAntAtThisLocation()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $hole = new AnthillHoleMYR();
        $hole->setMainBoardMYR($game->getMainBoardMYR());
        $hole->setPlayer($player);
        $gardenWorker = new GardenWorkerMYR();
        $this->anthillWorkerMYRRepository->method("findOneBy")->willReturn($ant);
        $this->gardenWorkerMYRRepository->method("findOneBy")->willReturn($gardenWorker);

        // THEN
        $this->expectException(\Exception::class);

        // THEN
        $this->workerMYRService->takeOutAnt($player->getPersonalBoardMYR(), $hole);
    }

    public function testPlaceAntInAnthillSuccessWithValidFloorAndAnt()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $selectedFloor = 2;
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $this->anthillWorkerMYRRepository->method("findOneBy")->willReturn($ant);

        // WHEN
        $this->workerMYRService->placeAntInAnthill($player->getPersonalBoardMYR(), $selectedFloor);

        // THEN
        $this->assertEquals($selectedFloor, $ant->getWorkFloor());
    }

    public function testPlaceAntInAnthillFailIfSelectedFloorIsGreaterThanAnthillLevel()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setBonus(-1);
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $selectedFloor = 3;
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);
        $this->anthillWorkerMYRRepository->method("findOneBy")->willReturn($ant);

        // THEN
        $this->expectException(\Exception::class);

        // WHEN
        $this->workerMYRService->placeAntInAnthill($player->getPersonalBoardMYR(), $selectedFloor);
    }


    public function testPlaceAntInAnthillFailIfNoMoreFreeAnts()
    {
        // GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $selectedFloor = 2;
        $ant = new AnthillWorkerMYR();
        $ant->setPlayer($player);
        $ant->setWorkFloor(1);
        $ant->setPersonalBoardMYR($player->getPersonalBoardMYR());
        $player->getPersonalBoardMYR()->addAnthillWorker($ant);

        // THEN
        $this->expectException(\Exception::class);

        // THEN
        $this->workerMYRService->placeAntInAnthill($player->getPersonalBoardMYR(), $selectedFloor);
    }

    public function testPlaceAnthillHoleWhenPlaceIsAvailable()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $player = $game->getPlayers()->first();
        // WHEN
        $this->workerMYRService->placeAnthillHole($player, $tile);
        // THEN
        $this->assertNotEmpty($player->getAnthillHoleMYRs());
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseThereIsAnthillHole()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(new AnthillHoleMYR());
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseTileIsWater()
    {
        // GIVEN
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::WATER_TILE_TYPE);
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseThereIsAPheromone()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $tile = new TileMYR();
        $pheromonTile = new PheromonTileMYR();
        $pheromonTile->setTile($tile);
        $pheromon = new PheromonMYR();
        $pheromon->addPheromonTile($pheromonTile);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array($pheromon));
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation0()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(0);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation1()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(1);
        $tile->setCoordY(1);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(2);
        $newTile->setCoordY(0);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(1);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation2()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(2);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(0);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(2);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation3()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(1);
        $tile->setCoordY(1);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(0);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(3);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation4()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(1);
        $tile->setCoordY(1);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(4);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation5()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeZeroWithOrientationImpossible()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(6);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeZeroWhenTileContainPrey()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $this->preyMYRRepository->method("findOneBy")->willReturn($prey);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeOneWithOrientation0()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(0);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeOneWithOrientation1()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(1);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeOneWithOrientation2()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(2);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeOneWithOrientationImpossible()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(3);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeOneWhenTileContainPrey()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ONE);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $this->preyMYRRepository->method("findOneBy")->willReturn($prey);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation0()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(0);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation1()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(1);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation2()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(2);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation3()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(3);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation4()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(4);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientation5()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeTwoWithOrientationImpossible()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(6);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeTwoWhenTileContainPrey()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_ONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_TWO);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $this->preyMYRRepository->method("findOneBy")->willReturn($prey);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation0()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(0);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation1()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(1);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation2()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(2);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation3()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(3);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation4()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(4);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientation5()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeThreeWithOrientationImpossible()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(6);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeThreeWhenTileContainPrey()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $this->preyMYRRepository->method("findOneBy")->willReturn($prey);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeFourWithOrientation0()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(0);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation1()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(1);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation2()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(2);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation3()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(3);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation4()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(4);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation5()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation6()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(6);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation7()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(7);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation8()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(8);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation9()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(9);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation10()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(10);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientation11()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(11);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFourWithOrientationImpossible()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_THREE);
        $tileType->setOrientation(12);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeFourWhenTileContainPrey()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_TWO);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FOUR);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $this->preyMYRRepository->method("findOneBy")->willReturn($prey);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation0()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(0);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation1()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(1);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation2()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(2);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation3()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(3);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation4()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(4);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientation5()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeFiveWithOrientationImpossible()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(6);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeFiveWhenTileContainPrey()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_FIVE);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $this->preyMYRRepository->method("findOneBy")->willReturn($prey);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeSixWithOrientation0()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(0);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientation1()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(1);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientation2()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(2);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientation3()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(3);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientation4()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(4);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientation5()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
        // THEN
        $this->assertNotEmpty($firstPlayer->getPheromonMYRs());
    }

    public function testPlacePheromoneOfTypeSixWithOrientationImpossible()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(6);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfTypeSixWhenTileContainPrey()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $this->preyMYRRepository->method("findOneBy")->willReturn($prey);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneOfUnknownType()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_LEVEL_THREE);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(0);
        $newTile->setCoordY(2);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(-1);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $this->preyMYRRepository->method("findOneBy")->willReturn($prey);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
    }

    public function testPlacePheromoneButNotEnoughLevel()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(MyrmesParameters::ANTHILL_START_LEVEL);
        $firstPlayer->getPersonalBoardMYR()->setBonus(MyrmesParameters::BONUS_PHEROMONE);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $newTile = new TileMYR();
        $newTile->setType(MyrmesParameters::GRASS_TILE_TYPE);
        $newTile->setCoordX(1);
        $newTile->setCoordY(1);
        $this->tileMYRRepository->method("findOneBy")->willReturn($newTile);
        $tileType = new TileTypeMYR();
        $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_SIX);
        $tileType->setOrientation(5);
        $gardenWorker = new GardenWorkerMYR();
        $gardenWorker->setTile($tile);
        $gardenWorker->setPlayer($firstPlayer);
        $game->getMainBoardMYR()->addGardenWorker($gardenWorker);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->workerMYRService->placePheromone($firstPlayer, $tile, $tileType);
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
            $personalBoard = new PersonalBoardMYR();
            $player->setPersonalBoardMYR($personalBoard);
        }
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);
        $game->setGamePhase(MyrmesParameters::PHASE_INVALID);
        return $game;
    }
}