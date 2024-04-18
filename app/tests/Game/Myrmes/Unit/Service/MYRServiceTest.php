<?php

namespace App\Tests\Game\Myrmes\Unit\Service;

use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GoalMYR;
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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class MYRServiceTest extends TestCase
{
    private MYRService $MYRService;
    private PlayerResourceMYRRepository $playerResourceMYRRepository;
    private ResourceMYRRepository $resourceMYRRepository;
    private SeasonMYRRepository $seasonMYRRepository;

    private GoalMYRRepository $goalMYRRepository;

    protected function setUp() : void
    {
        $entityManager = $this->createMock(
            EntityManagerInterface::class);
        $playerMYRRepository = $this->createMock(
            PlayerMYRRepository::class);
        $nurseMYRRepository = $this->createMock(
            NurseMYRRepository::class);
        $tileMYRRepository = $this->createMock(
            TileMYRRepository::class);
        $tileTypeMYRRepository = $this->createMock(
            TileTypeMYRRepository::class);
        $this->seasonMYRRepository = $this->createMock(
            SeasonMYRRepository::class);
        $this->resourceMYRRepository = $this->createMock(
            ResourceMYRRepository::class);
        $this->playerResourceMYRRepository = $this->createMock(
            PlayerResourceMYRRepository::class);
        $goalMYRRepository = $this->createMock(
            GoalMYRRepository::class);
        $this->goalMYRRepository = $goalMYRRepository;
        $this->MYRService = new MYRService(
            $playerMYRRepository,
            $entityManager,
            $nurseMYRRepository,
            $tileMYRRepository,
            $tileTypeMYRRepository,
            $this->seasonMYRRepository,
            $goalMYRRepository,
            $this->resourceMYRRepository,
            $this->playerResourceMYRRepository
        );
    }

    public function testActivateGoalWhenGoalIsLevelOne() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal);
        // THEN
        $this->assertEquals(MyrmesParameters::GOAL_REWARD_LEVEL_ONE, $firstPlayer->getScore());
    }

    public function testActivateGoalWhenGoalIsLevelTwo() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $firstPlayer->addGameGoalMYR($gameGoal);

        $goal2 = new GoalMYR();
        $goal2->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO);
        $gameGoal2 = new GameGoalMYR();
        $gameGoal2->setGoal($goal2);
        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal2);
        // THEN
        $this->assertEquals(MyrmesParameters::GOAL_REWARD_LEVEL_TWO, $firstPlayer->getScore());
    }

    public function testActivateGoalWhenGoalIsLevelThree() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $firstPlayer->addGameGoalMYR($gameGoal);

        $goal2 = new GoalMYR();
        $goal2->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE);
        $gameGoal2 = new GameGoalMYR();
        $gameGoal2->setGoal($goal2);
        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal2);
        // THEN
        $this->assertEquals(
            MyrmesParameters::GOAL_REWARD_LEVEL_THREE,
            $firstPlayer->getScore()
        );
    }

    public function testActivateGoalAndGivePointsToOtherPlayers() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $secondPlayer = $game->getPlayers()->last();
        $goal = new GoalMYR();
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $gameGoal->addPrecedentsPlayer($secondPlayer);
        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal);
        // THEN
        $this->assertEquals(
            MyrmesParameters::GOAL_REWARD_WHEN_GOAL_ALREADY_DONE,
            $secondPlayer->getScore()
        );
    }

    public function testActivateBonusWhenGoalIsAtTooHighLevel() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal);
    }

    public function testActivateBonusWhenPlayerHasAlreadyDoneTheBonus() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $firstPlayer->addGameGoalMYR($gameGoal);
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal);
    }

    public function testSetPhaseWhenOnlyOnePlayerChangePhase() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $lastPlayer = $game->getPlayers()->last();
        $newPhase = MyrmesParameters::PHASE_EVENT;
        $oldPhase = $lastPlayer->getPhase();

        //WHEN
        $this->MYRService->setPhase($firstPlayer, $newPhase);

        //THEN
        $this->assertSame($firstPlayer->getPhase(), $newPhase);
        $this->assertNotEquals($oldPhase, $firstPlayer->getPhase());
        $this->assertNotEquals($game->getGamePhase(), $newPhase);

    }

    /* TODO fix call repository in endSeason
    public function testSetPhaseWhenTwoPlayerChangePhase() : void
    {
        //GIVEN
        $game = $this->createGame(2);

        $season = new SeasonMYR();
        $season->setActualSeason(false);
        $season->setDiceResult(5);
        $season->setName(MyrmesParameters::SPRING_SEASON_NAME);
        $game->getMainBoardMYR()->addSeason($season);

        $season = new SeasonMYR();
        $season->setActualSeason(true);
        $season->setDiceResult(5);
        $season->setName(MyrmesParameters::WINTER_SEASON_NAME);
        $game->getMainBoardMYR()->addSeason($season);

        $season = new SeasonMYR();
        $season->setActualSeason(false);
        $season->setDiceResult(5);
        $season->setName(MyrmesParameters::FALL_SEASON_NAME);

        $game->getMainBoardMYR()->addSeason($season);

        $firstPlayer = $game->getPlayers()->first();
        $lastPlayer = $game->getPlayers()->last();
        $newPhase = MyrmesParameters::PHASE_EVENT;
        $oldPhase = $lastPlayer->getPhase();
        $firstPlayer->setPhase($newPhase);
        //WHEN
        $this->MYRService->setPhase($lastPlayer, $newPhase);

        //THEN
        $this->assertSame($lastPlayer->getPhase(), $newPhase);
        $this->assertSame($firstPlayer->getPhase(), $lastPlayer->getPhase());
        $this->assertNotEquals($oldPhase, $firstPlayer->getPhase());
        $this->assertSame($game->getGamePhase(), $newPhase);

    }*/

    public function testIsGameEndedShouldBeTrue() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::THIRD_YEAR_NUM + 1);
        //WHEN
        $isEnded = $this->MYRService->isGameEnded($game);
        //THEN
        $this->assertTrue($isEnded);
    }

    public function testIsGameEndedShouldBeFalse() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::THIRD_YEAR_NUM);
        //WHEN
        $isEnded = $this->MYRService->isGameEnded($game);
        //THEN
        $this->assertFalse($isEnded);
    }

    public function testGetNumberOfPlayerGrassResource() : void
    {
        // GIVEN

        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();

        $personalBoard = $firstPlayer->getPersonalBoardMYR();

        $playerResource = $personalBoard->getPlayerResourceMYRs()->first();

        $this->resourceMYRRepository->method("findOneBy")
            ->willReturn($playerResource->getResource());
        $this->playerResourceMYRRepository->method("findOneBy")
            ->willReturn($playerResource);

        // WHEN

        $numberOfGrass = $this->MYRService->getPlayerResourceAmount(
            $firstPlayer,
            MyrmesParameters::RESOURCE_TYPE_GRASS
        );

        // THEN

        $this->assertSame(1, $numberOfGrass);
    }

    public function testGetAvailableLarvae() : void
    {
        // GIVEN

        $game = $this->createGame(3);
        $firstPlayer = $game->getPlayers()->first();
        $personalBoard = $firstPlayer->getPersonalBoardMYR();
        $personalBoard->setLarvaCount(4);
        $availableLarvaeExpect = $personalBoard->getLarvaCount() - 1;

        // WHEN

        $available = $this->MYRService->getAvailableLarvae($firstPlayer);

        // THEN

        $this->assertSame($availableLarvaeExpect, $available);
    }

    public function testReturnFalseWhenCheckGoodPhase() : void
    {
        // GIVEN

        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();

        // WHEN

        $resultCheck = $this->MYRService->isInPhase(
            $firstPlayer,
            MyrmesParameters::PHASE_EVENT
        );

        // THEN

        $this->assertFalse($resultCheck);
    }

    public function testGetActualSeasonWhenActualSeasonIsNull() : void
    {
        // GIVEN

        $game = $this->createGame(2);

        // WHEN

        $value = $this->MYRService->getActualSeason($game);

        // THEN

        $this->assertNull($value);
    }

    public function testCanManageEndOfPhaseWhenReturnTrue() : void
    {
        // GIVEN

        $game = $this->createGame(2);
        $phase = MyrmesParameters::PHASE_EVENT;

        foreach ($game->getPlayers() as $player)
        {
            $player->setPhase($phase + 1);
        }

        // WHEN

        $result = $this->MYRService->canManageEndOfPhase($game, $phase);

        // THEN

        $this->assertTrue($result);
    }

    public function testCanManageEndOfPhaseWhenReturnFalse() : void
    {
        // GIVEN

        $game = $this->createGame(2);
        $phase = MyrmesParameters::PHASE_EVENT;

        foreach ($game->getPlayers() as $player)
        {
            $player->setPhase($phase + 1);
        }

        $game->getPlayers()->first()->setPhase($phase);

        // WHEN

        $result = $this->MYRService->canManageEndOfPhase($game, $phase);

        // THEN

        $this->assertFalse($result);
    }

    /*
    public function testInitializeNewGame() : void
    {
        // GIVEN

        $game = $this->createGame(2);
        $mainBoard = $game->getMainBoardMYR();

        foreach ($game->getPlayers() as $player)
        {
            $player->getPersonalBoardMYR()->getNurses()->clear();
        }

        $dirt = new ResourceMYR();
        $dirt->setDescription(MyrmesParameters::RESOURCE_TYPE_DIRT);

        $this->resourceMYRRepository
            ->method("findAll")->willReturn(array($dirt));
        $goal = new GoalMYR();
        $goal2 = new GoalMYR();
        $goal3 = new GoalMYR();
        $this->goalMYRRepository->method("findBy")->willReturn(array($goal, $goal2, $goal3));
        // WHEN

        $this->MYRService->initializeNewGame($game);

        // THEN

        $this->assertSame(1, $mainBoard->getYearNum());

        // TODO check preys

        foreach ($game->getPlayers() as $player)
        {
            $this->assertSame(
                $this->MYRService->getActualSeason($game)->getDiceResult(),
                $player->getPersonalBoardMYR()->getBonus()
            );

            $this->assertSame(MyrmesParameters::START_NURSES_COUNT_PER_PLAYER,
                $player->getPersonalBoardMYR()->getNurses()->count());

            $this->assertSame(MyrmesParameters::NUMBER_OF_WORKER_AT_START,
                $player->getPersonalBoardMYR()->getAnthillWorkers()->count());

            $this->assertSame(MyrmesParameters::NUMBER_OF_LARVAE_AT_START,
                $player->getPersonalBoardMYR()->getLarvaCount());

            $this->assertSame(MyrmesParameters::ANTHILL_START_LEVEL,
                $player->getPersonalBoardMYR()->getAnthillLevel());

            $this->assertSame(0, $player->getRemainingHarvestingBonus());

            $this->assertSame($dirt, $player->getPersonalBoardMYR()
                ->getPlayerResourceMYRs()->last()->getResource());

            $this->assertSame(0, $player->getPersonalBoardMYR()
                ->getPlayerResourceMYRs()->last()->getQuantity());

            // TODO check anthill hole

            $this->assertSame(MyrmesParameters::PLAYER_START_SCORE,
                $player->getScore());
        }

    }*/

    public function testGetPlayerResourceOfTypeWhenIsUnknow() : void
    {
        // GIVEN

        $game = $this->createGame(3);
        $player = $game->getPlayers()->first();

        // WHEN

        $result = $this->MYRService->getPlayerResourceOfType($player, "Unknow");

        // THEN

        $this->assertNull($result);
    }

    private function createGame(int $numberOfPlayers) : GameMYR
    {
        if ($numberOfPlayers < MyrmesParameters::MIN_NUMBER_OF_PLAYER ||
            $numberOfPlayers > MyrmesParameters::MAX_NUMBER_OF_PLAYER) {
            throw new \Exception("TOO MUCH PLAYERS ON CREATE GAME");
        }
        $game = new GameMYR();
        for ($i = 0; $i < $numberOfPlayers; $i += 1) {
            $player = new PlayerMYR('test' . $i, $game);
            $game->addPlayer($player);
            $player->setGameMyr($game);
            $player->setPhase(MyrmesParameters::PHASE_INVALID);

            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setSelectedEventLarvaeAmount(
                1 + $i);

            $resource = new ResourceMYR();
            $resource->setDescription(
                MyrmesParameters::RESOURCE_TYPE_GRASS);
            $playerResource = new PlayerResourceMYR();
            $playerResource->setResource($resource);
            $playerResource->setQuantity(1 + $i);
            $personalBoard->addPlayerResourceMYR($playerResource);
            $player->setPersonalBoardMYR($personalBoard);
            for (
                $j = 0;
                $j < MyrmesParameters::START_NURSES_COUNT_PER_PLAYER;
                $j += 1
            )
            {
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
        $game->setMainBoardMYR($mainBoard);
        $game->setGamePhase(MyrmesParameters::PHASE_INVALID);

        return $game;
    }

}