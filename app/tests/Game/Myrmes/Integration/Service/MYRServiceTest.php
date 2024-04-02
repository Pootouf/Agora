<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\GameGoalMYR;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\GoalMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Service\Game\Myrmes\MYRService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MYRServiceTest extends KernelTestCase
{

    private EntityManagerInterface $entityManager;
    private MYRService $MYRService;

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->MYRService = static::getContainer()->get(MYRService::class);
    }

    public function testActivateGoalWhenGoalIsLevelOne() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::$GOAL_DIFFICULTY_LEVEL_ONE);
        $this->entityManager->persist($goal);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $this->entityManager->persist($gameGoal);
        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal);
        // THEN
        $this->assertEquals(MyrmesParameters::$GOAL_REWARD_LEVEL_ONE, $firstPlayer->getScore());
    }

    public function testActivateGoalAndGivePointsToOtherPlayers() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $secondPlayer = $game->getPlayers()->last();
        $goal = new GoalMYR();
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::$GOAL_DIFFICULTY_LEVEL_ONE);
        $this->entityManager->persist($goal);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $gameGoal->addPrecedentsPlayer($secondPlayer);
        $this->entityManager->persist($gameGoal);
        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal);
        // THEN
        $this->assertEquals(MyrmesParameters::$GOAL_REWARD_LEVEL_ONE, $firstPlayer->getScore());
    }

    public function testActivateGoalWhenGoalIsLevelTwo() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::$GOAL_DIFFICULTY_LEVEL_ONE);
        $this->entityManager->persist($goal);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $this->entityManager->persist($gameGoal);
        $firstPlayer->addGameGoalMYR($gameGoal);
        $this->entityManager->persist($firstPlayer);

        $goal2 = new GoalMYR();
        $goal2->setName("Test2");
        $goal2->setDifficulty(MyrmesParameters::$GOAL_DIFFICULTY_LEVEL_TWO);
        $this->entityManager->persist($goal2);
        $gameGoal2 = new GameGoalMYR();
        $gameGoal2->setGoal($goal2);
        $this->entityManager->persist($gameGoal2);

        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal2);
        // THEN
        $this->assertEquals(MyrmesParameters::$GOAL_REWARD_LEVEL_TWO, $firstPlayer->getScore());
    }

    public function testActivateGoalWhenGoalIsLevelThree() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::$GOAL_DIFFICULTY_LEVEL_TWO);
        $this->entityManager->persist($goal);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $this->entityManager->persist($gameGoal);
        $firstPlayer->addGameGoalMYR($gameGoal);
        $this->entityManager->persist($firstPlayer);

        $goal2 = new GoalMYR();
        $goal2->setName("Test2");
        $goal2->setDifficulty(MyrmesParameters::$GOAL_DIFFICULTY_LEVEL_THREE);
        $this->entityManager->persist($goal2);
        $gameGoal2 = new GameGoalMYR();
        $gameGoal2->setGoal($goal2);
        $this->entityManager->persist($gameGoal2);

        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal2);
        // THEN
        $this->assertEquals(MyrmesParameters::$GOAL_REWARD_LEVEL_THREE, $firstPlayer->getScore());
    }

    public function testActivateBonusWhenGoalIsAtTooHighLevel() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::$GOAL_DIFFICULTY_LEVEL_THREE);
        $this->entityManager->persist($goal);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $this->entityManager->persist($gameGoal);
        $this->entityManager->flush();
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
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::$GOAL_DIFFICULTY_LEVEL_THREE);
        $this->entityManager->persist($goal);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $this->entityManager->persist($gameGoal);
        $firstPlayer->addGameGoalMYR($gameGoal);
        $this->entityManager->persist($firstPlayer);
        $this->entityManager->flush();
        // THEN
        $this->expectException(\Exception::class);
        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal);
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
            $player->setColor("");
            $player->setPhase(MyrmesParameters::$PHASE_WORRKER);
            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setLarvaCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setSelectedEventLarvaeAmount(0);
            $personalBoard->setBonus(5);
            $player->setPersonalBoardMYR($personalBoard);
            $player->setScore(0);
            $player->setGoalLevel(0);
            $player->setRemainingHarvestingBonus(0);
            for($j = 0; $j < MyrmesParameters::$START_NURSES_COUNT_PER_PLAYER; $j += 1) {
                $nurse = new NurseMYR();
                $nurse->setPosition(-1);
                $nurse->setArea(MyrmesParameters::$LARVAE_AREA);
                $nurse->setAvailable(true);
                $nurse->setPlayer($player);
                $personalBoard->addNurse($nurse);
                $entityManager->persist($nurse);
            }
            $entityManager->persist($player);
            $entityManager->persist($personalBoard);
        }
        $mainBoard = new MainBoardMYR();
        $mainBoard->setYearNum(0);
        $mainBoard->setGame($game);
        $entityManager->persist($mainBoard);
        $season = new SeasonMYR();
        $season->setName("Spring");
        $season->setDiceResult(1);
        $season->setActualSeason(true);
        $season->setMainBoard($mainBoard);
        $entityManager->persist($season);
        $game->setMainBoardMYR($mainBoard);
        $game->setGameName("test");
        $game->setLaunched(true);
        $entityManager->persist($game);
        $entityManager->flush();
        return $game;
    }

}