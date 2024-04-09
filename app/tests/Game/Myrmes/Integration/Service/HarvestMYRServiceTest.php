<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Entity\Game\Myrmes\TileTypeMYR;
use App\Service\Game\Myrmes\HarvestMYRService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HarvestMYRServiceTest extends KernelTestCase
{

    private EntityManagerInterface $entityManager;
    private HarvestMYRService $harvestMYRService;

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->harvestMYRService = static::getContainer()->get(HarvestMYRService::class);
    }

    public function testHarvestSpecialTilesFarm() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_FARM);
        $specialTileType->setOrientation(0);
        $this->entityManager->persist($specialTileType);
        $specialTile = new PheromonMYR();
        $specialTile->setType($specialTileType);
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setHarvested(false);
        $this->entityManager->persist($specialTile);
        $firstPlayer->addPheromonMYR($specialTile);

        $resources = new ResourceMYR();
        $resources->setDescription(MyrmesParameters::RESOURCE_TYPE_GRASS);
        $this->entityManager->persist($resources);
        $playerResource = new PlayerResourceMYR();
        $playerResource->setResource($resources);
        $grassCount = 0;
        $playerResource->setQuantity($grassCount);
        $playerResource->setPersonalBoard($firstPlayer->getPersonalBoardMYR());
        $this->entityManager->persist($playerResource);
        $firstPlayer->getPersonalBoardMYR()->addPlayerResourceMYR($playerResource);
        $this->entityManager->persist($firstPlayer);
        $this->entityManager->flush();
        // WHEN
        $this->harvestMYRService->harvestPlayerFarms($firstPlayer);
        // THEN
        $this->assertEquals($grassCount + 1, $playerResource->getQuantity());
    }

    public function testHarvestSpecialTileQuarry() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY);
        $specialTileType->setOrientation(0);
        $this->entityManager->persist($specialTileType);
        $specialTile = new PheromonMYR();
        $specialTile->setType($specialTileType);
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setHarvested(false);
        $this->entityManager->persist($specialTile);
        $firstPlayer->addPheromonMYR($specialTile);

        $resources = new ResourceMYR();
        $resources->setDescription(MyrmesParameters::RESOURCE_TYPE_DIRT);
        $this->entityManager->persist($resources);
        $playerResource = new PlayerResourceMYR();
        $playerResource->setResource($resources);
        $dirtCount = 0;
        $playerResource->setQuantity($dirtCount);
        $playerResource->setPersonalBoard($firstPlayer->getPersonalBoardMYR());
        $this->entityManager->persist($playerResource);
        $firstPlayer->getPersonalBoardMYR()->addPlayerResourceMYR($playerResource);
        $this->entityManager->persist($firstPlayer);
        $this->entityManager->flush();
        // WHEN
        $this->harvestMYRService->harvestPlayerQuarry($firstPlayer, $specialTile,
            MyrmesParameters::RESOURCE_TYPE_DIRT);
        // THEN
        $this->assertEquals($dirtCount + 1, $playerResource->getQuantity());
    }

    public function testHarvestSpecialTileQuarryWhenInvalidAskedResources() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY);
        $specialTileType->setOrientation(0);
        $this->entityManager->persist($specialTileType);
        $specialTile = new PheromonMYR();
        $specialTile->setType($specialTileType);
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setHarvested(false);
        $this->entityManager->persist($specialTile);
        $firstPlayer->addPheromonMYR($specialTile);
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // THEN
        $this->harvestMYRService->harvestPlayerQuarry($firstPlayer, $specialTile,
            MyrmesParameters::RESOURCE_TYPE_GRASS);
    }

    public function testHarvestSpecialTileQuarryWhenAlreadyHarvested() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_QUARRY);
        $specialTileType->setOrientation(0);
        $this->entityManager->persist($specialTileType);
        $specialTile = new PheromonMYR();
        $specialTile->setType($specialTileType);
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setHarvested(true);
        $this->entityManager->persist($specialTile);
        $firstPlayer->addPheromonMYR($specialTile);
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // THEN
        $this->harvestMYRService->harvestPlayerQuarry($firstPlayer, $specialTile,
            MyrmesParameters::RESOURCE_TYPE_STONE);
    }

    public function testHarvestSpecialTileQuarryWhenPheromoneOfInvalidType() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL);
        $specialTileType->setOrientation(0);
        $this->entityManager->persist($specialTileType);
        $specialTile = new PheromonMYR();
        $specialTile->setType($specialTileType);
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setHarvested(false);
        $this->entityManager->persist($specialTile);
        $firstPlayer->addPheromonMYR($specialTile);
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // THEN
        $this->harvestMYRService->harvestPlayerQuarry($firstPlayer, $specialTile,
            MyrmesParameters::RESOURCE_TYPE_STONE);
    }

    public function testHarvestSpecialTilesSubAnthill() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $specialTileType = new TileTypeMYR();
        $specialTileType->setType(MyrmesParameters::SPECIAL_TILE_TYPE_SUBANTHILL);
        $specialTileType->setOrientation(0);
        $this->entityManager->persist($specialTileType);
        $specialTile = new PheromonMYR();
        $specialTile->setType($specialTileType);
        $specialTile->setPlayer($firstPlayer);
        $specialTile->setHarvested(false);
        $this->entityManager->persist($specialTile);
        $firstPlayer->addPheromonMYR($specialTile);
        $playerPoints = $firstPlayer->getScore();
        $this->entityManager->flush();
        // WHEN
        $this->harvestMYRService->harvestPlayerSubAnthill($firstPlayer);
        // THEN
        $this->assertEquals($playerPoints + 2, $firstPlayer->getScore());
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

            $resourceGrass = new ResourceMYR();
            $resourceGrass->setDescription(MyrmesParameters::RESOURCE_TYPE_GRASS);
            $this->entityManager->persist($resourceGrass);
            $playerGrass = new PlayerResourceMYR();
            $playerGrass->setQuantity(4);
            $playerGrass->setResource($resourceGrass);
            $playerGrass->setPersonalBoard($personalBoard);
            $this->entityManager->persist($playerGrass);
            $personalBoard->addPlayerResourceMYR($playerGrass);

            $resourceStone = new ResourceMYR();
            $resourceStone->setDescription(MyrmesParameters::RESOURCE_TYPE_STONE);
            $this->entityManager->persist($resourceStone);
            $playerStone = new PlayerResourceMYR();
            $playerStone->setQuantity(4);
            $playerStone->setResource($resourceStone);
            $playerStone->setPersonalBoard($personalBoard);
            $this->entityManager->persist($playerStone);
            $personalBoard->addPlayerResourceMYR($playerStone);

            $resourceDirt = new ResourceMYR();
            $resourceDirt->setDescription(MyrmesParameters::RESOURCE_TYPE_DIRT);
            $this->entityManager->persist($resourceDirt);
            $playerDirt = new PlayerResourceMYR();
            $playerDirt->setQuantity(4);
            $playerDirt->setResource($resourceDirt);
            $playerDirt->setPersonalBoard($personalBoard);
            $this->entityManager->persist($playerDirt);
            $personalBoard->addPlayerResourceMYR($playerDirt);

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