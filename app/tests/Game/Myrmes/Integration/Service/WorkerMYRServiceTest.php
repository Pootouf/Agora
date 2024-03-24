<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PheromonTileMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
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

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
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
            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setLarvaCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setBonus(0);
            $player->setPersonalBoardMYR($personalBoard);
            $player->setScore(0);
            $player->setGoalLevel(0);
            $entityManager->persist($player);
            $entityManager->persist($personalBoard);
        }
        $mainBoard = new MainBoardMYR();
        $mainBoard->setYearNum(0);
        $mainBoard->setGame($game);
        $season = new SeasonMYR();
        $season->setName("Spring");
        $season->setMainBoardMYR($mainBoard);
        $season->setDiceResult(1);
        $entityManager->persist($season);
        $mainBoard->setActualSeason($season);
        $game->setMainBoardMYR($mainBoard);
        $game->setGameName("test");
        $game->setLaunched(true);
        $entityManager->persist($mainBoard);
        $entityManager->persist($game);
        $entityManager->flush();

        return $game;
    }

}