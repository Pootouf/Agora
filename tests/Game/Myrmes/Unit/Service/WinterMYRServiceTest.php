<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\ResourceMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Repository\Game\Myrmes\GoalMYRRepository;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Repository\Game\Myrmes\SeasonMYRRepository;
use App\Repository\Game\Myrmes\TileMYRRepository;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
use App\Service\Game\Myrmes\MYRService;
use App\Service\Game\Myrmes\WinterMYRService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PHPUnit\Framework\TestCase;

class WinterMYRServiceTest extends TestCase
{
    private WinterMYRService $winterMYRService;

    private SeasonMYRRepository $seasonMYRRepository;

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
        $this->seasonMYRRepository = $this->getMockBuilder(SeasonMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $goalMYRRepository = $this->getMockBuilder(GoalMYRRepository::class)
            ->setConstructorArgs([$managerRegistry])
            ->getMock();
        $myrService = new MYRService($playerMYRRepository, $entityManager, $nurseMYRRepository,
            $tileMYRRepository, $tileTypeMYRRepository, $this->seasonMYRRepository, $goalMYRRepository,
            $resourceMYRRepository, $playerResourceMYRRepository );
        $playerResourceMYRRepository->method("findOneBy")->willReturn($playerFood);
        $this->winterMYRService = new WinterMYRService($entityManager, $resourceMYRRepository,
            $playerResourceMYRRepository, $myrService);
    }

    public function testRetrievePointsDuringYearOneAndNoWarriors() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $initialScore = 25;
        $player->setScore($initialScore);
        $expectedScore = $initialScore;
        //WHEN
        $this->winterMYRService->retrievePoints($player);
        //THEN
        $this->assertSame($expectedScore, $player->getScore());
    }

    public function testRetrievePointsDuringYearTwoAndNoWarriors() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $initialScore = 25;
        $player->setScore($initialScore);
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::SECOND_YEAR_NUM);
        $expectedScore = 22;
        //WHEN
        $this->winterMYRService->retrievePoints($player);
        //THEN
        $this->assertSame($expectedScore, $player->getScore());
    }

    public function testRetrievePointsDuringYearThreeAndNoWarriors() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $initialScore = 25;
        $player->setScore($initialScore);
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::THIRD_YEAR_NUM);
        $expectedScore = 19;
        //WHEN
        $this->winterMYRService->retrievePoints($player);
        //THEN
        $this->assertSame($expectedScore, $player->getScore());
    }

    public function testRetrievePointsDuringYearThreeAndOneWarrior() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $initialScore = 25;
        $player->setScore($initialScore);
        $player->getPersonalBoardMYR()->setWarriorsCount(1);
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::THIRD_YEAR_NUM);
        $expectedScore = 22;
        //WHEN
        $this->winterMYRService->retrievePoints($player);
        //THEN
        $this->assertSame($expectedScore, $player->getScore());
    }

    public function testRetrievePointsDuringNonExistingYear() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $game->getMainBoardMYR()->setYearNum(4);
        //THEN
        $this->expectException(\Exception::class);
        //WHEN
        $this->winterMYRService->retrievePoints($player);
    }

    public function testMustDropResourcesForWinterReturnTrueWhenHaving5ResourcesWithAnthillHole0(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WINTER);
        $playerResource = new PlayerResourceMYR();
        $resource = new ResourceMYR();
        $resource->setDescription(MyrmesParameters::RESOURCE_TYPE_DIRT);
        $playerResource->setResource($resource);
        $playerResource->setQuantity(1);
        $playerResource->setPersonalBoard($player->getPersonalBoardMYR());
        $player->getPersonalBoardMYR()->addPlayerResourceMYR($playerResource);

        //WHEN
        $result = $this->winterMYRService->mustDropResourcesForWinter($player);

        //THEN
        $this->assertTrue($result);
    }

    public function testMustDropResourcesForWinterReturnTrueWhenHaving7ResourcesWithAnthillHole2(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WINTER);
        $player->getPersonalBoardMYR()->setAnthillLevel(2);
        $playerResource = new PlayerResourceMYR();
        $resource = new ResourceMYR();
        $resource->setDescription(MyrmesParameters::RESOURCE_TYPE_DIRT);
        $playerResource->setResource($resource);
        $playerResource->setQuantity(3);
        $playerResource->setPersonalBoard($player->getPersonalBoardMYR());
        $player->getPersonalBoardMYR()->addPlayerResourceMYR($playerResource);

        //WHEN
        $result = $this->winterMYRService->mustDropResourcesForWinter($player);

        //THEN
        $this->assertTrue($result);
    }

    public function testMustDropResourcesForWinterReturnFalseWhenHaving4ResourcesWhenAnthillHoleLevel0(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WINTER);

        //WHEN
        $result = $this->winterMYRService->mustDropResourcesForWinter($player);

        //THEN
        $this->assertFalse($result);
    }

    public function testMustDropResourcesForWinterReturnFalseWhenHaving6ResourcesWithAnthillHoleLevel2(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $player->setPhase(MyrmesParameters::PHASE_WINTER);
        $player->getPersonalBoardMYR()->setAnthillLevel(2);
        //WHEN
        $result = $this->winterMYRService->mustDropResourcesForWinter($player);

        //THEN
        $this->assertFalse($result);
    }

    public function testCanManageEndOfWinterReturnFalseIfPlayerMustDropResources(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $lastPlayer = $game->getPlayers()->last();
        $firstPlayer->setPhase(MyrmesParameters::PHASE_WINTER);
        $lastPlayer->setPhase(MyrmesParameters::PHASE_WINTER);
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(2);
        $lastPlayer->getPersonalBoardMYR()->setAnthillLevel(2);
        $playerResource = new PlayerResourceMYR();
        $resource = new ResourceMYR();
        $resource->setDescription(MyrmesParameters::RESOURCE_TYPE_DIRT);
        $playerResource->setResource($resource);
        $playerResource->setQuantity(3);
        $firstPlayer->getPersonalBoardMYR()->addPlayerResourceMYR($playerResource);
        $lastPlayer->getPersonalBoardMYR()->addPlayerResourceMYR($playerResource);

        //WHEN
        $result = $this->winterMYRService->canManageEndOfWinter($game);

        //THEN
        $this->assertFalse($result);
    }

    public function testManageEndOfWinterWhenCanNotManageThis(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $lastPlayer = $game->getPlayers()->last();
        $firstPlayer->setPhase(MyrmesParameters::PHASE_WINTER);
        $lastPlayer->setPhase(MyrmesParameters::PHASE_WINTER);
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(2);
        $lastPlayer->getPersonalBoardMYR()->setAnthillLevel(2);
        $playerResource = new PlayerResourceMYR();
        $resource = new ResourceMYR();
        $resource->setDescription(MyrmesParameters::RESOURCE_TYPE_DIRT);
        $playerResource->setResource($resource);
        $playerResource->setQuantity(3);
        $firstPlayer->getPersonalBoardMYR()->addPlayerResourceMYR($playerResource);
        $lastPlayer->getPersonalBoardMYR()->addPlayerResourceMYR($playerResource);

        // THEN

        $this->expectException(\Exception::class);

        // WHEN

        $this->winterMYRService->manageEndOfWinter($game);

    }

    public function testCanManageEndOfWinterReturnTrueIfNoPlayerMustDropResources(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $lastPlayer = $game->getPlayers()->last();
        $firstPlayer->setPhase(MyrmesParameters::PHASE_WINTER);
        $lastPlayer->setPhase(MyrmesParameters::PHASE_WINTER);
        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(2);
        $lastPlayer->getPersonalBoardMYR()->setAnthillLevel(2);

        //WHEN
        $result = $this->winterMYRService->canManageEndOfWinter($game);

        //THEN
        $this->assertTrue($result);
    }

    public function testManageEndOfWinterCanManageThis(): void
    {
        //GIVEN
        $game = $this->createGame(2);

        $oldYearNum = $game->getMainBoardMYR()->getYearNum();

        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->setScore(6);
        $lastPlayer = $game->getPlayers()->last();
        $firstPlayer->setScore(9);

        $spring = new SeasonMYR();
        $spring->setDiceResult(3);
        $spring->setName(MyrmesParameters::SPRING_SEASON_NAME);
        $spring->setMainBoard($game->getMainBoardMYR());
        $spring->setActualSeason(false);

        $fall = new SeasonMYR();
        $fall->setDiceResult(5);
        $fall->setName(MyrmesParameters::FALL_SEASON_NAME);
        $fall->setMainBoard($game->getMainBoardMYR());
        $fall->setActualSeason(true);

        $game->getMainBoardMYR()->addSeason($spring);
        $game->getMainBoardMYR()->addSeason($fall);

        $this->seasonMYRRepository->method("findOneBy")->willReturn($spring);

        $firstPlayer->setPhase(MyrmesParameters::PHASE_WINTER);
        $lastPlayer->setPhase(MyrmesParameters::PHASE_WINTER);
        $game->setGamePhase(MyrmesParameters::PHASE_WINTER);

        $firstPlayer->getPersonalBoardMYR()->setAnthillLevel(2);
        $lastPlayer->getPersonalBoardMYR()->setAnthillLevel(2);

        // WHEN

        $this->winterMYRService->manageEndOfWinter($game);

        // THEN

        $this->assertGreaterThan($oldYearNum,
            $game->getMainBoardMYR()->getYearNum());
    }

    public function testCanSetPhaseToWinterReturnTrueIfActualSeasonIsFall(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $season = new SeasonMYR();
        $season->setName(MyrmesParameters::FALL_SEASON_NAME);
        $season->setActualSeason(true);
        $season->setMainBoard($game->getMainBoardMYR());
        $season->setDiceResult(1);
        $game->getMainBoardMYR()->addSeason($season);

        //WHEN
        $result = $this->winterMYRService->canSetPhaseToWinter($game);

        //THEN
        $this->assertTrue($result);
    }

    public function testCanSetPhaseToWinterReturnFalseIfActualSeasonIsSpring(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $season = new SeasonMYR();
        $season->setName(MyrmesParameters::SPRING_SEASON_NAME);
        $season->setActualSeason(true);
        $season->setMainBoard($game->getMainBoardMYR());
        $season->setDiceResult(1);
        $game->getMainBoardMYR()->addSeason($season);

        //WHEN
        $result = $this->winterMYRService->canSetPhaseToWinter($game);

        //THEN
        $this->assertFalse($result);
    }

    public function testRemoveCubeFromWarehouseSuccessfullyWhenPlayerHasTheResource(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $playerResource = $player->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $expectedQuantity = $playerResource->getQuantity() - 1;
        //WHEN
        try {
            $this->winterMYRService->removeCubeOfWarehouse($player, $playerResource);
        } catch (\Exception $e) {
            // must not arrive here
        }
        //THEN
        $this->assertEquals($expectedQuantity, $playerResource->getQuantity());
    }

    public function testRemoveCubeFromWarehouseFailWhenPlayerWantToRemoveAResourceWhenNoResource(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $playerResource = $firstPlayer->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();
        $playerResource->setQuantity(0);
        //THEN
        $this->expectException(Exception::class);

        //WHEN
        $this->winterMYRService->removeCubeOfWarehouse($firstPlayer, $playerResource);

    }

    public function testRemoveCubeFromWarehouseFailWhenPlayerWantToRemoveAResourceFromAnotherPlayerResource(): void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $lastPlayer = $game->getPlayers()->last();
        $playerResource = $lastPlayer->getPersonalBoardMYR()->getPlayerResourceMYRs()->first();

        //THEN
        $this->expectException(Exception::class);

        //WHEN
        $this->winterMYRService->removeCubeOfWarehouse($firstPlayer, $playerResource);

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
            $player->setScore(0);
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