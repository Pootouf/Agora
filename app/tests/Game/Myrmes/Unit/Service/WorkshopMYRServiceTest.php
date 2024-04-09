<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PheromonMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class WorkshopMYRServiceTest extends TestCase
{

    private WorkshopMYRService $workshopMYRService;

    private MYRService $MYRService;

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
        $nurseMYRRepository = $this->getMockBuilder(NurseMYRRepository::class)
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
        $anthillHoleMyrRepository = $this->getMockBuilder(AnthillHoleMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $preyMyrRepository = $this->getMockBuilder(PreyMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $this->MYRService = $this->createMock(MYRService::class);

        $playerResourceMYRRepository->method("findOneBy")->willReturn($playerFood);
        $this->workshopMYRService = new WorkshopMYRService($entityManager, $this->MYRService, $pheromoneTileMyrRepository,
            $preyMyrRepository, $tileMYRRepository, $pheromoneMyrRepository, $resourceMYRRepository,
            $playerResourceMYRRepository, $nurseMYRRepository, $anthillHoleMyrRepository);
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

    public function testGiveBonusWhenAskIncreaseLevel() : void
    {
        // GIVEN

        $game = $this->createGame(2);

        foreach ($game->getPlayers() as $p)
        {
            $p->setPhase(MyrmesParameters::PHASE_WORKSHOP);
        }

        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();

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
            $nurse->setPlayer($player);
            $nurse->setAvailable(true);
            $player->setPersonalBoardMYR($personalBoard);
            $player->getPersonalBoardMYR()->addNurse($nurse);
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