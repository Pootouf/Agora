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
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class MYRServiceTest extends TestCase
{
    private MYRService $MYRService;
    protected function setUp() : void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $playerMYRRepository = $this->createMock(PlayerMYRRepository::class);
        $nurseMYRRepository = $this->createMock(NurseMYRRepository::class);
        $tileMYRRepository = $this->createMock(TileMYRRepository::class);
        $tileTypeMYRRepository = $this->createMock(TileTypeMYRRepository::class);
        $seasonMYRRepository = $this->createMock(SeasonMYRRepository::class);
        $resourceMYRRepository = $this->createMock(ResourceMYRRepository::class);
        $playerResourceMYRRepository = $this->createMock(PlayerResourceMYRRepository::class);
        $goalMYRRepository = $this->createMock(GoalMYRRepository::class);
        $this->MYRService = new MYRService($playerMYRRepository, $entityManager, $nurseMYRRepository,
            $tileMYRRepository, $tileTypeMYRRepository,
            $seasonMYRRepository, $goalMYRRepository,
            $resourceMYRRepository, $playerResourceMYRRepository);
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
        $this->assertEquals(MyrmesParameters::GOAL_REWARD_LEVEL_THREE, $firstPlayer->getScore());
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
        $this->assertEquals(MyrmesParameters::GOAL_REWARD_WHEN_GOAL_ALREADY_DONE, $secondPlayer->getScore());
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
            for($j = 0; $j < MyrmesParameters::START_NURSES_COUNT_PER_PLAYER; $j += 1) {
                $nurse = new NurseMYR();
                $personalBoard->addNurse($nurse);
            }
        }
        $mainBoard = new MainBoardMYR();
        $game->setMainBoardMYR($mainBoard);

        return $game;
    }

}