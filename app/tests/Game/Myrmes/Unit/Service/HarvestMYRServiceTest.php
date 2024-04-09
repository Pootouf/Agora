<?php

namespace App\Tests\Game\Myrmes\Unit\Service;


use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Repository\Game\Myrmes\GoalMYRRepository;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\SeasonMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
use App\Service\Game\Myrmes\HarvestMYRService;
use App\Service\Game\Myrmes\MYRService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class HarvestMYRServiceTest extends TestCase
{

    private HarvestMYRService $harvestMYRService;

    protected function setUp() : void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $playerMYRRepository = $this->getMockBuilder(PlayerMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $nurseMYRRepository = $this->getMockBuilder(NurseMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $tileMYRRepository = $this->getMockBuilder(TileMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $tileTypeMYRRepository = $this->getMockBuilder(TileTypeMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $seasonMYRRepository = $this->getMockBuilder(SeasonMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $resourceMYRRepository = $this->getMockBuilder(ResourceMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $food = new ResourceMYR();
        $food->setDescription(MyrmesParameters::RESOURCE_TYPE_GRASS);
        $resourceMYRRepository->method("findOneBy")->willReturn($food);
        $playerResourceMYRRepository = $this->getMockBuilder(PlayerResourceMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $goalMYRRepository = $this->getMockBuilder(GoalMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $playerFood = new PlayerResourceMYR();
        $playerFood->setResource($food);
        $playerFood->setQuantity(4);
        $myrService = new MYRService($playerMYRRepository, $entityManager, $nurseMYRRepository,
            $tileMYRRepository, $tileTypeMYRRepository, $seasonMYRRepository,
            $goalMYRRepository, $resourceMYRRepository, $playerResourceMYRRepository );
        $playerResourceMYRRepository->method("findOneBy")->willReturn($playerFood);
        $this->harvestMYRService = new HarvestMYRService($entityManager, $myrService);
    }

    public function testAreAllPheromonesHarvestedReturnTrueWhenPlayerHasHarvestedPheromones(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $pheromoneFirstPlayer = $firstPlayer->getPheromonMYRs()->first();
        $pheromoneFirstPlayer->setHarvested(true);
        //WHEN
        $result = $this->harvestMYRService->areAllPheromonesHarvested($firstPlayer);
        //THEN
        $this->assertTrue($result);
    }

    public function testAreAllPheromonesHarvestedReturnFalseWhenPlayerHasNotHarvestedPheromone(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $pheromoneFirstPlayer = $firstPlayer->getPheromonMYRs()->first();
        $pheromoneFirstPlayer->setHarvested(false);
        //WHEN
        $result = $this->harvestMYRService->areAllPheromonesHarvested($firstPlayer);
        //THEN
        $this->assertFalse($result);
    }

    public function testCanStillHarvestReturnTrueWhenPlayerHasNotHarvestedPheromone(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $pheromoneFirstPlayer = $firstPlayer->getPheromonMYRs()->first();
        $pheromoneFirstPlayer->setHarvested(false);
        //WHEN
        $result = $this->harvestMYRService->canStillHarvest($firstPlayer);
        //THEN
        $this->assertTrue($result);
    }

    public function testHarvestSpecialTilesFarm() : void
    {
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_FARM);
        $specialTile = new PheromonMYR();
        $specialTile->addPheromonTile(new PheromonTileMYR());
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setType($specialTileType);
        $firstPlayer->addPheromonMYR($specialTile);
        $resourceMYR = new ResourceMYR();
        $resourceMYR->setDescription(MyrmesParameters::RESOURCE_TYPE_GRASS);
        $playerResources = new PlayerResourceMYR();
        $playerResources->setResource($resourceMYR);
        $grassCount = 0;
        $playerResources->setQuantity($grassCount);
        $firstPlayer->getPersonalBoardMYR()->addPlayerResourceMYR($playerResources);
        // WHEN
        $this->harvestMYRService->harvestPlayerFarms($firstPlayer);
        // THEN
        $this->assertEquals($grassCount + 1, $playerResources->getQuantity());
    }

    public function testHarvestSpecialTileQuarry() : void
    {
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY);
        $specialTile = new PheromonMYR();
        $specialTile->addPheromonTile(new PheromonTileMYR());
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setType($specialTileType);
        $firstPlayer->addPheromonMYR($specialTile);
        $resourceMYR = new ResourceMYR();
        $resourceMYR->setDescription(MyrmesParameters::RESOURCE_TYPE_STONE);
        $playerResources = new PlayerResourceMYR();
        $playerResources->setResource($resourceMYR);
        $stoneCount = 0;
        $playerResources->setQuantity($stoneCount);
        $firstPlayer->getPersonalBoardMYR()->addPlayerResourceMYR($playerResources);
        // WHEN
        $this->harvestMYRService->harvestPlayerQuarry($firstPlayer, $specialTile,
            MyrmesParameters::RESOURCE_TYPE_STONE);
        // THEN
        $this->assertEquals($stoneCount + 1, $playerResources->getQuantity());
    }

    public function testHarvestSpecialTileQuarryWhenInvalidAskedResources() : void
    {
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY);
        $specialTile = new PheromonMYR();
        $specialTile->addPheromonTile(new PheromonTileMYR());
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setType($specialTileType);
        $firstPlayer->addPheromonMYR($specialTile);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->harvestMYRService->harvestPlayerQuarry($firstPlayer, $specialTile,
            MyrmesParameters::RESOURCE_TYPE_GRASS);
    }

    public function testHarvestSpecialTileQuarryWhenPheromoneAlreadyHarvested() : void
    {
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY);
        $specialTile = new PheromonMYR();
        $specialTile->addPheromonTile(new PheromonTileMYR());
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setType($specialTileType);
        $specialTile->setHarvested(true);
        $firstPlayer->addPheromonMYR($specialTile);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->harvestMYRService->harvestPlayerQuarry($firstPlayer, $specialTile,
            MyrmesParameters::RESOURCE_TYPE_STONE);
    }

    public function testHarvestSpecialTileQuarryWhenPheromoneOfInvalidType() : void
    {
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL);
        $specialTile = new PheromonMYR();
        $specialTile->addPheromonTile(new PheromonTileMYR());
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setType($specialTileType);
        $specialTile->setHarvested(false);
        $firstPlayer->addPheromonMYR($specialTile);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->harvestMYRService->harvestPlayerQuarry($firstPlayer, $specialTile,
            MyrmesParameters::RESOURCE_TYPE_STONE);
    }

    public function testHarvestSpecialTilesSubAnthill() : void
    {
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL);
        $specialTile = new PheromonMYR();
        $specialTile->addPheromonTile(new PheromonTileMYR());
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setType($specialTileType);
        $firstPlayer->addPheromonMYR($specialTile);
        $score = $firstPlayer->getScore();
        // WHEN
        $this->harvestMYRService->harvestPlayerSubAnthill($firstPlayer);
        // THEN
        $this->assertEquals($score + 2, $firstPlayer->getScore());
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

            $resource = new ResourceMYR();
            $resource->setDescription(MyrmesParameters::RESOURCE_TYPE_GRASS);
            $playerFood = new PlayerResourceMYR();
            $playerFood->setQuantity(4);
            $playerFood->setResource($resource);
            $playerFood->setPersonalBoard($personalBoard);
            $personalBoard->addPlayerResourceMYR($playerFood);

            $resourceStone = new ResourceMYR();
            $resource->setDescription(MyrmesParameters::RESOURCE_TYPE_STONE);
            $playerStone = new PlayerResourceMYR();
            $playerStone->setQuantity(4);
            $playerStone->setResource($resourceStone);
            $playerStone->setPersonalBoard($personalBoard);
            $personalBoard->addPlayerResourceMYR($playerStone);

            $resourceDirt = new ResourceMYR();
            $resource->setDescription(MyrmesParameters::RESOURCE_TYPE_DIRT);
            $playerDirt = new PlayerResourceMYR();
            $playerDirt->setQuantity(4);
            $playerDirt->setResource($resourceDirt);
            $playerDirt->setPersonalBoard($personalBoard);
            $personalBoard->addPlayerResourceMYR($playerDirt);

            $personalBoard->setAnthillLevel(0);

            $pheromone = new PheromonMYR();
            $tileType = new TileTypeMYR();
            $tileType->setType(MyrmesParameters::PHEROMONE_TYPE_ZERO);
            $pheromone->setType($tileType);
            $pheromone->addPheromonTile(new PheromonTileMYR());
            $pheromone->setPlayer($player);
            $player->addPheromonMYR($pheromone);

            $player->setPersonalBoardMYR($personalBoard);
            for($j = 0; $j < MyrmesParameters::START_NURSES_COUNT_PER_PLAYER; $j += 1) {
                $nurse = new NurseMYR();
                $personalBoard->addNurse($nurse);
            }
        }
        $mainBoard = new MainBoardMYR();
        $mainBoard->setYearNum(MyrmesParameters::FIRST_YEAR_NUM);
        $game->setMainBoardMYR($mainBoard);
        $game->setGamePhase(MyrmesParameters::PHASE_INVALID);
        return $game;
    }
}