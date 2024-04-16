<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GoalMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\TileMYR;
use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use App\Repository\Game\Myrmes\PheromonMYRRepository;
use App\Repository\Game\Myrmes\PheromonTileMYRRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\SeasonMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
use App\Service\Game\Myrmes\MYRService;
use App\Service\Game\Myrmes\WinterMYRService;
use App\Service\Game\Myrmes\WorkshopMYRService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class WorkshopMYRServiceTest extends TestCase
{

    private WorkshopMYRService $workshopMYRService;

    private MYRService $MYRService;

    private NurseMYRRepository $nurseMYRRepository;

    private AnthillHoleMYRRepository $anthillHoleMYRRepository;

    protected function setUp() : void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $resourceMYRRepository = $this->getMockBuilder(ResourceMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $food = new ResourceMYR();
        $food->setDescription(MyrmesParameters::RESOURCE_TYPE_GRASS);
        $resourceMYRRepository->method("findOneBy")->willReturn($food);
        $playerResourceMYRRepository = $this->getMockBuilder(PlayerResourceMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $playerFood = new PlayerResourceMYR();
        $playerFood->setResource($food);
        $playerFood->setQuantity(4);
        $this->nurseMYRRepository = $this->getMockBuilder(NurseMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $tileMYRRepository = $this->getMockBuilder(TileMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $pheromoneMyrRepository = $this->getMockBuilder(PheromonMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $pheromoneTileMyrRepository = $this->getMockBuilder(PheromonTileMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $this->anthillHoleMYRRepository = $this->getMockBuilder(AnthillHoleMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $preyMyrRepository = $this->getMockBuilder(PreyMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $this->MYRService = $this->createMock(MYRService::class);

        $playerResourceMYRRepository->method("findOneBy")->willReturn($playerFood);
        $this->workshopMYRService = new WorkshopMYRService($entityManager, $this->MYRService, $pheromoneTileMyrRepository,
            $preyMyrRepository, $tileMYRRepository, $pheromoneMyrRepository, $resourceMYRRepository,
            $playerResourceMYRRepository, $this->nurseMYRRepository, $this->anthillHoleMYRRepository);
    }

    public function testCanSetPhaseToWorkshopReturnTrueIfPlayerHasNursesInWorkshop(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        //WHEN
        $result = $this->workshopMYRService->canSetPhaseToWorkshop($player);
        //THEN
        $this->assertTrue($result);
    }

    public function testCanSetPhaseToWorkshopReturnFalseIfPlayerHasNoNursesInWorkshop(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $nurse = $player->getPersonalBoardMYR()->getNurses()->first();
        $nurse->setArea(MyrmesParameters::LARVAE_AREA);
        //WHEN
        $result = $this->workshopMYRService->canSetPhaseToWorkshop($player);
        //THEN
        $this->assertFalse($result);
    }

    public function testManageEndOfWorkshopDoesNothingIfAllPlayersHaveEndedWorkshopPhase(): void
    {
            //GIVEN
            $game = $this->createGame(2);

            //THEN
            $this->MYRService->expects(self::once())
            ->method('canManageEndOfPhase')
            ->willReturn(true);

            //WHEN
            try {
                $this->workshopMYRService->manageEndOfWorkshop($game);
            } catch (\Exception $e) {
                // must not arrive here
            }
    }

    public function testManageEndOfWorkshopThrowExceptionIfAllPlayersHaveNotEndedWorkshopPhase(): void
    {
        //GIVEN
        $game = $this->createGame(2);

        //THEN
        $this->MYRService->expects(self::once())
            ->method('canManageEndOfPhase')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        //WHEN
        $this->workshopMYRService->manageEndOfWorkshop($game);
    }
    public function testGiveBonusWhenAskIncreaseLevelWhenCanIncrease() : void
    {
        // GIVEN

        $game = $this->createGame(2);

        foreach ($game->getPlayers() as $p)
        {
            $p->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        }

        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();

        $array = new ArrayCollection();
        $nurse = $personalBoard->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_LEVEL_AREA);
        $array->add($nurse);
        $this->MYRService->method("getNursesAtPosition")->willReturn($array);

        foreach (array_keys(MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_ONE) as $resourceName)
        {
            $countForResource = MyrmesParameters::BUY_RESOURCE_FOR_LEVEL_ONE[$resourceName];
            foreach ($personalBoard->getPlayerResourceMYRs() as $r)
            {
                if ($r->getResource()->getDescription() === $resourceName)
                {
                    $r->setQuantity($r->getQuantity() + $countForResource);
                    break;
                }
            }
        }

        // WHEN

        $this->workshopMYRService->manageWorkshop($player, MyrmesParameters::WORKSHOP_LEVEL_AREA);

        // THEN

        $this->assertSame(1, $personalBoard->getAnthillLevel());
    }

    public function testGiveBonusWhenAskIncreaseLevelWhenCanNotIncrease() : void
    {
        // GIVEN

        $game = $this->createGame(2);

        foreach ($game->getPlayers() as $p)
        {
            $p->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        }

        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setAnthillLevel(1);

        $array = new ArrayCollection();
        $nurse = $personalBoard->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_LEVEL_AREA);
        $array->add($nurse);
        $this->MYRService->method("getNursesAtPosition")->willReturn($array);

        // THEN

        $this->expectException(\Exception::class);

        // WHEN


        $this->workshopMYRService->manageWorkshop($player,
            MyrmesParameters::WORKSHOP_LEVEL_AREA);
    }

    public function testGiveBonusWhenAskIncreaseLevelAndActionAlreadyDone() : void
    {
        // GIVEN

        $game = $this->createGame(2);
        $action = MyrmesParameters::WORKSHOP_LEVEL_AREA;

        foreach ($game->getPlayers() as $player)
        {
            $player->setPhase(MyrmesParameters::PHASE_WORKSHOP);
            $player->getWorkshopActions()[$action] = 1;
        }

        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setAnthillLevel(1);

        $array = new ArrayCollection();
        $nurse = $personalBoard->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_LEVEL_AREA);
        $array->add($nurse);
        $this->MYRService->method("getNursesAtPosition")->willReturn($array);

        // THEN

        $this->expectException(\Exception::class);

        // WHEN


        $this->workshopMYRService->manageWorkshop($player,
            $action);
    }

    public function testGiveBonusWhenAskNewNurseWhenCanAdd() : void
    {
        // GIVEN

        $game = $this->createGame(2);

        foreach ($game->getPlayers() as $p)
        {
            $p->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        }

        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount(2);

        $array = new ArrayCollection();
        $nurse = $personalBoard->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_NURSE_AREA);
        $array->add($nurse);

        $this->MYRService->method("getNursesAtPosition")
            ->willReturn($array);
        $this->nurseMYRRepository->method("findOneBy")
            ->willReturn($personalBoard->getNurses()->last());

        // WHEN

        $this->workshopMYRService->manageWorkshop($player,
            MyrmesParameters::WORKSHOP_NURSE_AREA);

        // THEN

        $this->assertSame(0, $personalBoard->getLarvaCount());
    }

    public function testGiveBonusWhenAskNewNurseWhenCanNotAdd() : void
    {
        // GIVEN

        $game = $this->createGame(2);

        foreach ($game->getPlayers() as $p)
        {
            $p->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        }

        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount(1);

        $array = new ArrayCollection();
        $nurse = $personalBoard->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_NURSE_AREA);
        $array->add($nurse);

        $this->MYRService->method("getNursesAtPosition")
            ->willReturn($array);
        $this->nurseMYRRepository->method("findOneBy")
            ->willReturn($personalBoard->getNurses()->last());

        // WHEN

        $this->workshopMYRService->manageWorkshop($player,
            MyrmesParameters::WORKSHOP_NURSE_AREA);

        // THEN

        $this->assertSame(1, $personalBoard->getLarvaCount());
    }

    public function testGiveBonusWhenAreaIsInvalid() : void
    {
        // GIVEN

        $game = $this->createGame(2);

        foreach ($game->getPlayers() as $p)
        {
            $p->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        }

        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount(1);

        $array = new ArrayCollection();
        $nurse = $personalBoard->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_NURSE_AREA + 1);
        $array->add($nurse);

        $this->MYRService->method("getNursesAtPosition")
            ->willReturn($array);
        $this->nurseMYRRepository->method("findOneBy")
            ->willReturn($personalBoard->getNurses()->last());

        // THEN

        $this->expectException(\Exception::class);

        // WHEN

        $this->workshopMYRService->manageWorkshop($player,
            MyrmesParameters::WORKSHOP_NURSE_AREA + 1);

    }

    public function testGiveBonusWithALotNurses() : void
    {
        // GIVEN

        $game = $this->createGame(2);

        foreach ($game->getPlayers() as $p)
        {
            $p->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        }

        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();

        $array = new ArrayCollection();
        $array->add($personalBoard->getNurses()->first());
        $array->add($personalBoard->getNurses()->last());
        $this->MYRService->method("getNursesAtPosition")
            ->willReturn($array);

        $this->nurseMYRRepository->method("findOneBy")
            ->willReturn($personalBoard->getNurses()->last());

        // THEN

        $this->expectException(\Exception::class);

        // WHEN

        $this->workshopMYRService->manageWorkshop($player,
            MyrmesParameters::WORKSHOP_NURSE_AREA);

    }

    public function testGiveBonusWithBadPhase() : void
    {
        // GIVEN

        $game = $this->createGame(2);

        foreach ($game->getPlayers() as $p)
        {
            $p->setPhase(MyrmesParameters::PHASE_INVALID);
        }

        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();

        $array = new ArrayCollection();
        $array->add($personalBoard->getNurses()->first());
        $this->MYRService->method("getNursesAtPosition")
            ->willReturn($array);

        $this->nurseMYRRepository->method("findOneBy")
            ->willReturn($personalBoard->getNurses()->last());

        // THEN

        $this->expectException(\Exception::class);

        // WHEN

        $this->workshopMYRService->manageWorkshop($player,
            MyrmesParameters::WORKSHOP_NURSE_AREA);

    }

    public function testGiveBonusWhenAskGoal() : void
    {
        // GIVEN

        $game = $this->createGame(2);

        foreach ($game->getPlayers() as $p)
        {
            $p->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        }

        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount(4);

        $array = new ArrayCollection();
        $nurse = $personalBoard->getNurses()->first();
        $nurse->setArea(MyrmesParameters::WORKSHOP_GOAL_AREA);
        $array->add($nurse);

        $this->MYRService->method("getNursesAtPosition")
            ->willReturn($array);

        // WHEN

        $this->workshopMYRService->manageWorkshop($player,
            MyrmesParameters::WORKSHOP_GOAL_AREA);

        // THEN

        $this->assertSame(4, $personalBoard->getLarvaCount());
    }

    public function testGetAnthillHoleFromTileSucceedAndReturnAnthillHole() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $anthillHole = new AnthillHoleMYR();
        $anthillHole->setTile($tile);
        $anthillHole->setMainBoardMYR($game->getMainBoardMYR());
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn($anthillHole);
        //WHEN
        $result = $this->workshopMYRService->getAnthillHoleFromTile($tile, $game);
        //THEN
        $this->assertSame($result, $anthillHole);
    }

    public function testGetAnthillHoleFromTileSucceedAndReturnNullIFDoesntExist() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $tile = new TileMYR();
        $tile->setCoordX(0);
        $tile->setCoordY(0);
        $tile->setType(MyrmesParameters::DIRT_TILE_TYPE);
        $this->anthillHoleMYRRepository->method("findOneBy")->willReturn(null);
        //WHEN
        $result = $this->workshopMYRService->getAnthillHoleFromTile($tile, $game);
        //THEN
        $this->assertNull($result);
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
            $personalBoard->setAnthillLevel(0);
            $nurse = new NurseMYR();
            $nurse->setArea(MyrmesParameters::WORKSHOP_AREA);
            $nurse->setAvailable(true);
            $player->setPersonalBoardMYR($personalBoard);
            $player->getPersonalBoardMYR()->addNurse($nurse);
            for($j = 0; $j < MyrmesParameters::START_NURSES_COUNT_PER_PLAYER; $j += 1) {
                $nurse = new NurseMYR();
                $personalBoard->addNurse($nurse);
            }
            $playerActions = array();
            for($j = MyrmesParameters::WORKSHOP_GOAL_AREA; $j <= MyrmesParameters::WORKSHOP_NURSE_AREA; $j += 1) {
                $playerActions[$j] = 0;
            }
            $player->setWorkshopActions($playerActions);
        }
        $mainBoard = new MainBoardMYR();
        $mainBoard->setYearNum(MyrmesParameters::FIRST_YEAR_NUM);

        $gameGoal = new GameGoalMYR();
        $goal = new GoalMYR();
        $goal->setDifficulty(1);
        $goal->setName("goal");
        $gameGoal->setGoal($goal);

        $mainBoard->addGameGoalsLevelOne($gameGoal);

        $game->setMainBoardMYR($mainBoard);
        $game->setGamePhase(MyrmesParameters::PHASE_INVALID);
        return $game;
    }
}