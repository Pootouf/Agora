<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

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
use App\Entity\Game\Myrmes\TileMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
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
    private WorkerMYRService $workerMYRService;

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
        $this->workerMYRService = new WorkerMYRService(
            $this->entityManager,
            $this->MYRService,
            $this->anthillHoleMYRRepository,
            $this->pheromonMYRRepository,
            $this->preyMYRRepository,
            $this->tileMYRRepository,
            $this->playerResourceMYRRepository,
            $this->resourceMYRRepository,
            $this->tileTypeMYRRepository
        );
    }

    public function testPlaceAnthillHoleWhenPlaceIsAvailable()
    {
        // GIVEN
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $MYRService = $this->createMock(MYRService::class);
        $anthillHoleRepository = $this->createMock(AnthillHoleMYRRepository::class);
        $anthillHoleRepository->method("findOneBy")->willReturn(null);
        $pheromonRepository = $this->createMock(PheromonMYRRepository::class);
        $pheromonRepository->method("findBy")->willReturn(array());
        $preyRepository = $this->createMock(PreyMYRRepository::class);
        $tileRepository = $this->createMock(TileMYRRepository::class);
        $playerResourceRepository = $this->createMock(PlayerResourceMYRRepository::class);
        $resourceRepository = $this->createMock(ResourceMYRRepository::class);
        $tileTypeMYRRepository = $this->createMock(TileTypeMYRRepository::class);
        $workerMYRService = new WorkerMYRService($entityManager, $MYRService,
            $anthillHoleRepository, $pheromonRepository, $preyRepository,
            $tileRepository, $playerResourceRepository, $resourceRepository, $tileTypeMYRRepository);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $player = $game->getPlayers()->first();
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
        // THEN
        $this->assertNotEmpty($player->getAnthillHoleMYRs());
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseThereIsAnthillHole()
    {
        // GIVEN
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $MYRService = $this->createMock(MYRService::class);
        $anthillHoleRepository = $this->createMock(AnthillHoleMYRRepository::class);
        $anthillHoleRepository->method("findOneBy")->willReturn(new AnthillHoleMYR());
        $pheromonRepository = $this->createMock(PheromonMYRRepository::class);
        $pheromonRepository->method("findBy")->willReturn(array());
        $preyRepository = $this->createMock(PreyMYRRepository::class);
        $tileRepository = $this->createMock(TileMYRRepository::class);
        $playerResourceRepository = $this->createMock(PlayerResourceMYRRepository::class);
        $resourceRepository = $this->createMock(ResourceMYRRepository::class);
        $tileTypeMYRRepository = $this->createMock(TileTypeMYRRepository::class);
        $workerMYRService = new WorkerMYRService($entityManager, $MYRService,
            $anthillHoleRepository, $pheromonRepository, $preyRepository,
            $tileRepository, $playerResourceRepository, $resourceRepository, $tileTypeMYRRepository);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseTileIsWater()
    {
        // GIVEN
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $MYRService = $this->createMock(MYRService::class);
        $anthillHoleRepository = $this->createMock(AnthillHoleMYRRepository::class);
        $pheromonRepository = $this->createMock(PheromonMYRRepository::class);
        $preyRepository = $this->createMock(PreyMYRRepository::class);
        $tileRepository = $this->createMock(TileMYRRepository::class);
        $playerResourceRepository = $this->createMock(PlayerResourceMYRRepository::class);
        $resourceRepository = $this->createMock(ResourceMYRRepository::class);
        $tileTypeMYRRepository = $this->createMock(TileTypeMYRRepository::class);
        $workerMYRService = new WorkerMYRService($entityManager, $MYRService,
            $anthillHoleRepository, $pheromonRepository, $preyRepository,
            $tileRepository, $playerResourceRepository, $resourceRepository, $tileTypeMYRRepository);
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setType(MyrmesParameters::WATER_TILE_TYPE);
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlaceAnthillHoleWhenPlaceIsNotAvailableBecauseThereIsAPheromone()
    {
        // GIVEN
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $MYRService = $this->createMock(MYRService::class);
        $anthillHoleRepository = $this->createMock(AnthillHoleMYRRepository::class);
        $anthillHoleRepository->method("findOneBy")->willReturn(null);
        $pheromonRepository = $this->createMock(PheromonMYRRepository::class);
        $tile = new TileMYR();
        $pheromonTile = new PheromonTileMYR();
        $pheromonTile->setTile($tile);
        $pheromon = new PheromonMYR();
        $pheromon->addPheromonTile($pheromonTile);
        $pheromonRepository->method("findBy")->willReturn(array($pheromon));
        $preyRepository = $this->createMock(PreyMYRRepository::class);
        $tileRepository = $this->createMock(TileMYRRepository::class);
        $playerResourceRepository = $this->createMock(PlayerResourceMYRRepository::class);
        $resourceRepository = $this->createMock(ResourceMYRRepository::class);
        $tileTypeMYRRepository = $this->createMock(TileTypeMYRRepository::class);
        $workerMYRService = new WorkerMYRService($entityManager, $MYRService,
            $anthillHoleRepository, $pheromonRepository, $preyRepository,
            $tileRepository, $playerResourceRepository, $resourceRepository, $tileTypeMYRRepository);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $workerMYRService->placeAnthillHole($player, $tile);
    }

    public function testPlacePheromoneOfTypeZeroWithOrientation0()
    {
        // GIVEN
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        $this->pheromonMYRRepository->method("findBy")->willReturn(array());
        $this->preyMYRRepository->method("findOneBy")->willReturn(null);
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
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

        return $game;
    }
}