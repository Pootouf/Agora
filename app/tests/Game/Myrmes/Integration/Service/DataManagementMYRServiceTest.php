<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\DTO\Myrmes\BoardBoxMYR;
use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GardenWorkerMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\PreyMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Repository\Game\Myrmes\GoalMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
use App\Service\Game\Myrmes\DataManagementMYRService;
use App\Service\Game\Myrmes\WorkshopMYRService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DataManagementMYRServiceTest extends KernelTestCase
{

    private EntityManagerInterface $entityManager;
    private DataManagementMYRService $dataManagementMYRService;
    private TileMYRRepository $tileMYRRepository;

    private TileTypeMYRRepository $tileTypeMYRRepository;

    private PlayerResourceMYRRepository $playerResourceMYRRepository;

    private ResourceMYRRepository $resourceMYRRepository;

    private GoalMYRRepository $goalMYRRepository;

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->dataManagementMYRService = static::getContainer()->get(DataManagementMYRService::class);
        $this->tileMYRRepository = static::getContainer()->get(TileMYRRepository::class);
        $this->tileTypeMYRRepository = static::getContainer()->get(TileTypeMYRRepository::class);
        $this->playerResourceMYRRepository = static::getContainer()->get(PlayerResourceMYRRepository::class);
        $this->resourceMYRRepository = static::getContainer()->get(ResourceMYRRepository::class);
        $this->goalMYRRepository = static::getContainer()->get(GoalMYRRepository::class);
    }

    public function testGetListOfCoordinatesFromString() : void
    {
        //GIVEN
        $string = "[1_2 2_4 3_1] ";
        $result = array();
        $coord1 = [1, 2];
        $coord2 = [2, 4];
        $coord3 = [3, 1];
        array_push($result, $coord1, $coord2, $coord3);
        //WHEN
        $coords = $this->dataManagementMYRService->getListOfCoordinatesFromString($string);
        //THEN
        $i = 0;
        foreach ($coords as $coord) {
            $this->assertSame($coord, $result[$i]);
            ++$i;
        }
    }

    public function testCreateBoardBoxWorkerPhaseWhenNotHasAntAndNotCleaned() : void
    {
        //GIVEN
        $game = $this->createGame(3);
        $player = $game->getPlayers()->first();
        $x = 12;
        $y = 5;
        $tile = $this->tileMYRRepository->findOneBy(
            ["coordX" => $x, "coordY" => $y]
        );
        $ant = null;
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $prey->setPlayer($player);
        $prey->setMainBoardMYR($game->getMainBoardMYR());
        $prey->setType(MyrmesParameters::LADYBUG_TYPE);
        $this->entityManager->persist($prey);
        $this->entityManager->flush();
        $pheromoneTile = null;
        $anthillHole = null;
        $expectedBoardBox = new BoardBoxMYR($tile, $ant, $pheromoneTile, $anthillHole, $prey, $x, $y, 0);
        //WHEN
        $box = $this->dataManagementMYRService->createBoardBoxWorkerPhase($game, $tile, $x, $y, false, $player, 0, []);
        //THEN
        $this->assertEquals($expectedBoardBox, $box);
    }

    public function testCreateBoardBoxWorkerPhaseWhenNotHasAntAndCleaned() : void
    {
        //GIVEN
        $game = $this->createGame(3);
        $player = $game->getPlayers()->first();
        $x = 12;
        $y = 5;
        $tile = $this->tileMYRRepository->findOneBy(
            ["coordX" => $x, "coordY" => $y]
        );
        $ant = null;
        $prey = null;
        $pheromoneTile = null;
        $anthillHole = null;
        $expectedBoardBox = new BoardBoxMYR($tile, $ant, $pheromoneTile, $anthillHole, $prey, $x, $y, 0);
        //WHEN
        $box = $this->dataManagementMYRService->createBoardBoxWorkerPhase($game, $tile, $x, $y, false, $player, 0, [[$x - 1, $y]]);
        //THEN
        $this->assertEquals($expectedBoardBox, $box);
    }

    public function testCreateBoardBoxWorkerPhaseWhenHasAntAndNotCleaned() : void
    {
        //GIVEN
        $game = $this->createGame(3);
        $player = $game->getPlayers()->first();
        $x = 12;
        $y = 5;
        $tile = $this->tileMYRRepository->findOneBy(
            ["coordX" => $x, "coordY" => $y]
        );
        $ant = new GardenWorkerMYR();
        $ant->setMainBoardMYR($game->getMainBoardMYR());
        $ant->setTile($tile);
        $ant->setPlayer($player);
        $ant->setShiftsCount(0);
        $this->entityManager->persist($ant);
        $prey = new PreyMYR();
        $prey->setTile($tile);
        $prey->setPlayer($player);
        $prey->setMainBoardMYR($game->getMainBoardMYR());
        $prey->setType(MyrmesParameters::LADYBUG_TYPE);
        $this->entityManager->persist($prey);
        $this->entityManager->flush();
        $pheromoneTile = null;
        $anthillHole = null;
        //WHEN
        $box = $this->dataManagementMYRService->createBoardBoxWorkerPhase($game, $tile, $x, $y, true, $player, 0, []);
        //THEN
        $this->assertNotNull($box->getAnt());
    }


    public function testCreateBoardBoxWhenTileIsNull() : void
    {
        //GIVEN
        $game = $this->createGame(3);
        $tile = null;
        $ant = null;
        $prey = null;
        $pheromoneTile = null;
        $anthillHole = null;
        $x = 1;
        $y = 5;
        $expectedBoardBox = new BoardBoxMYR($tile, $ant, $pheromoneTile, $anthillHole, $prey, $x, $y);
        //WHEN
        $box = $this->dataManagementMYRService->createBoardBox($game, $tile, $x, $y);
        //THEN
        $this->assertEquals($expectedBoardBox, $box);
    }

    public function testCreateBoardBoxWhenTileNotNullWithAnt() : void
    {
        //GIVEN
        $game = $this->createGame(3);
        $x = 12;
        $y = 5;
        $tile = $this->tileMYRRepository->findOneBy(
            ["coordX" => $x, "coordY" => $y]
        );
        $ant = new GardenWorkerMYR();
        $ant->setTile($tile);
        $ant->setMainBoardMYR($game->getMainBoardMYR());
        $ant->setPlayer($game->getPlayers()->first());
        $ant->setShiftsCount(0);
        $this->entityManager->persist($ant);
        $this->entityManager->flush();
        $prey = null;
        $pheromoneTile = null;
        $anthillHole = null;
        $expectedBoardBox = new BoardBoxMYR($tile, $ant, $pheromoneTile, $anthillHole, $prey, $x, $y);
        //WHEN
        $box = $this->dataManagementMYRService->createBoardBox($game, $tile, $x, $y);
        //THEN
        $this->assertEquals($expectedBoardBox, $box);
    }

    public function testWorkerOnAnthillLevels() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $anthillWorker = new AnthillWorkerMYR();
        $anthillWorker->setPersonalBoardMYR($personalBoard);
        $anthillWorker->setWorkFloor(MyrmesParameters::WORKER_AREA);
        $this->entityManager->persist($anthillWorker);
        $personalBoard->addAnthillWorker($anthillWorker);
        $this->entityManager->persist($personalBoard);
        $anthillWorker = new AnthillWorkerMYR();
        $anthillWorker->setPersonalBoardMYR($personalBoard);
        $anthillWorker->setWorkFloor(MyrmesParameters::SOLDIERS_AREA);
        $this->entityManager->persist($anthillWorker);
        $personalBoard->addAnthillWorker($anthillWorker);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
        $expectedResult = [false, false, true, true];
        //WHEN
        $result = $this->dataManagementMYRService->workerOnAnthillLevels($personalBoard);
        //THEN
        $this->assertSame($expectedResult, $result);
    }

    private function createGame(int $numberOfPlayers) : GameMYR
    {
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
            $nurse = new NurseMYR();
            $nurse->setArea(MyrmesParameters::BASE_AREA);
            $nurse->setAvailable(true);
            $nurse->setPersonalBoardMYR($personalBoard);
            $this->entityManager->persist($nurse);
            $personalBoard->addNurse($nurse);
            $player->setPersonalBoardMYR($personalBoard);
            $player->setScore(0);
            $player->setGoalLevel(0);
            $player->setRemainingHarvestingBonus(0);
            $playerActions = array();
            for($j = MyrmesParameters::WORKSHOP_GOAL_AREA; $j <= MyrmesParameters::WORKSHOP_NURSE_AREA; $j += 1) {
                $playerActions[$j] = 0;
            }
            $player->setWorkshopActions($playerActions);
            $this->entityManager->persist($player);
            $this->entityManager->persist($personalBoard);
            foreach ($this->resourceMYRRepository->findAll() as $resource) {
                $playerResource = new PlayerResourceMYR();
                $playerResource->setResource($resource);
                $playerResource->setQuantity(0);
                $player->getPersonalBoardMYR()
                       ->addPlayerResourceMYR($playerResource);
                $this->entityManager->persist($playerResource);
                $this->entityManager->persist($player->getPersonalBoardMYR());
            }
            $this->entityManager->flush();
        }
        $this->entityManager->flush();
        return $game;
    }
}