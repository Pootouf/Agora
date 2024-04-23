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
use App\Entity\Game\Myrmes\PlayerResourceMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use App\Repository\Game\Myrmes\ResourceMYRRepository;
use App\Service\Game\Myrmes\MYRService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MYRServiceTest extends KernelTestCase
{

    private EntityManagerInterface $entityManager;
    private MYRService $MYRService;
    private ResourceMYRRepository $resourceMYRRepository;

    private PlayerResourceMYRRepository $playerResourceMYRRepository;

    protected function setUp() : void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->MYRService = static::getContainer()->get(MYRService::class);
        $this->resourceMYRRepository = static::getContainer()->get(ResourceMYRRepository::class);
        $this->playerResourceMYRRepository = static::getContainer()->get(PlayerResourceMYRRepository::class);
    }

    public function testActivateGoalWhenGoalIsLevelOne() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE);
        $this->entityManager->persist($goal);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $this->entityManager->persist($gameGoal);
        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal);
        // THEN
        $this->assertEquals(MyrmesParameters::GOAL_REWARD_LEVEL_ONE, $firstPlayer->getScore());
    }

    public function testActivateGoalAndGivePointsToOtherPlayers() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $secondPlayer = $game->getPlayers()->last();
        $goal = new GoalMYR();
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE);
        $this->entityManager->persist($goal);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $gameGoal->addPrecedentsPlayer($secondPlayer);
        $this->entityManager->persist($gameGoal);
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
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_ONE);
        $this->entityManager->persist($goal);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $this->entityManager->persist($gameGoal);
        $firstPlayer->addGameGoalMYR($gameGoal);
        $this->entityManager->persist($firstPlayer);

        $goal2 = new GoalMYR();
        $goal2->setName("Test2");
        $goal2->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO);
        $this->entityManager->persist($goal2);
        $gameGoal2 = new GameGoalMYR();
        $gameGoal2->setGoal($goal2);
        $this->entityManager->persist($gameGoal2);

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
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_TWO);
        $this->entityManager->persist($goal);
        $gameGoal = new GameGoalMYR();
        $gameGoal->setGoal($goal);
        $this->entityManager->persist($gameGoal);
        $firstPlayer->addGameGoalMYR($gameGoal);
        $this->entityManager->persist($firstPlayer);

        $goal2 = new GoalMYR();
        $goal2->setName("Test2");
        $goal2->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE);
        $this->entityManager->persist($goal2);
        $gameGoal2 = new GameGoalMYR();
        $gameGoal2->setGoal($goal2);
        $this->entityManager->persist($gameGoal2);

        // WHEN
        $this->MYRService->doGameGoal($firstPlayer, $gameGoal2);
        // THEN
        $this->assertEquals(MyrmesParameters::GOAL_REWARD_LEVEL_THREE, $firstPlayer->getScore());
    }

    public function testActivateBonusWhenGoalIsAtTooHighLevel() : void
    {
        // GIVEN
        $game = $this->createGame(2);
        $firstPlayer = $game->getPlayers()->first();
        $goal = new GoalMYR();
        $goal->setName("Test");
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE);
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
        $goal->setDifficulty(MyrmesParameters::GOAL_DIFFICULTY_LEVEL_THREE);
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

    public function testIsGameEndedShouldBeTrue() : void
    {
        //GIVEN
        $game = $this->createGame(2);
        $game->getMainBoardMYR()->setYearNum(MyrmesParameters::THIRD_YEAR_NUM + 1);
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->flush();
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
        $this->entityManager->persist($game->getMainBoardMYR());
        $this->entityManager->flush();
        //WHEN
        $isEnded = $this->MYRService->isGameEnded($game);
        //THEN
        $this->assertFalse($isEnded);
    }

    public function testGetPlayerFromGameAndHisNameWhenReturnGoodPlayer() : void
    {
        // GIVEN

        $myrService = static::getContainer()->get(MYRService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();
        $entityManager->persist($player);
        $entityManager->persist($game);
        $entityManager->flush();
        $expectedResult = $player;

        // WHEN

        $result = $myrService->getPlayerFromNameAndGame(
            $game,
            $player->getUsername()
        );

        // THEN

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetPlayerFromGameAndHisNameWhenIsNull() : void
    {
        // GIVEN

        $myrService = static::getContainer()->get(MYRService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2);
        $player = $game->getPlayers()->first();

        $entityManager->persist($player);
        $entityManager->persist($game);
        $entityManager->flush();
        $wrongName = "gtaeuaioea";

        // WHEN

        $result = $myrService->getPlayerFromNameAndGame($game, $wrongName);

        // THEN

        $this->assertNull($result);
    }

    public function testGetDiceResults() : void
    {
        // GIVEN

        $myrService = static::getContainer()->get(MYRService::class);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $game = $this->createGame(2);
        $game->getMainBoardMYR()->getSeasons()->clear();
        $entityManager->persist($game);

        $dice = 4;

        $seasonSpring = new SeasonMYR();
        $seasonSpring->setDiceResult($dice);
        $seasonSpring->setActualSeason(false);
        $seasonSpring->setName(MyrmesParameters::SPRING_SEASON_NAME);
        $entityManager->persist($seasonSpring);

        $seasonFall = new SeasonMYR();
        $seasonFall->setDiceResult($dice);
        $seasonFall->setActualSeason(false);
        $seasonFall->setName(MyrmesParameters::FALL_SEASON_NAME);
        $entityManager->persist($seasonFall);

        $seasonSummer = new SeasonMYR();
        $seasonSummer->setDiceResult($dice);
        $seasonSummer->setActualSeason(false);
        $seasonSummer->setName(MyrmesParameters::SUMMER_SEASON_NAME);
        $entityManager->persist($seasonSummer);

        $game->getMainBoardMYR()->addSeason($seasonSpring);
        $game->getMainBoardMYR()->addSeason($seasonFall);
        $game->getMainBoardMYR()->addSeason($seasonSummer);

        $entityManager->persist($seasonSpring);
        $entityManager->persist($seasonFall);
        $entityManager->persist($seasonSummer);
        $entityManager->persist($game->getMainBoardMYR());
        $entityManager->flush();

        // WHEN

        $result = $myrService->getDiceResults($game);

        // THEN

        foreach ($result as $r)
        {
            $this->assertSame($dice, $r);
        }
    }

    public function testExchangeLarvaeForFoodWhenNotEnoughLarvae() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount(2);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
        $expectedLarvaeNb = 2;
        $expectedFood = 0;
        $food = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        //WHEN
        $this->MYRService->exchangeLarvaeForFood($player);
        //THEN
        $larvaCount = $personalBoard->getLarvaCount();
        $playerFood = $this->playerResourceMYRRepository->findOneBy(
            ["personalBoard" => $personalBoard, "resource" => $food]
        );
        $this->assertSame($expectedFood, $playerFood->getQuantity());
        $this->assertSame($expectedLarvaeNb, $larvaCount);
    }

    public function testExchangeLarvaeForFoodWhenEnoughLarvae() : void
    {
        //GIVEN
        $game = $this->createGame(4);
        $player = $game->getPlayers()->first();
        $personalBoard = $player->getPersonalBoardMYR();
        $personalBoard->setLarvaCount(7);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
        $expectedLarvaeNb = 4;
        $expectedFood = 1;
        $food = $this->resourceMYRRepository->findOneBy(["description" => MyrmesParameters::RESOURCE_TYPE_GRASS]);
        //WHEN
        $this->MYRService->exchangeLarvaeForFood($player);
        //THEN
        $larvaCount = $personalBoard->getLarvaCount();
        $playerFood = $this->playerResourceMYRRepository->findOneBy(
            ["personalBoard" => $personalBoard, "resource" => $food]
        );
        $this->assertSame($expectedFood, $playerFood->getQuantity());
        $this->assertSame($expectedLarvaeNb, $larvaCount);
    }

    private function createGame(int $numberOfPlayers) : GameMYR
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        if($numberOfPlayers < MyrmesParameters::MIN_NUMBER_OF_PLAYER ||
            $numberOfPlayers > MyrmesParameters::MAX_NUMBER_OF_PLAYER) {
            throw new \Exception("TOO MUCH PLAYERS ON CREATE GAME");
        }
        $game = new GameMYR();
        $mainBoard = new MainBoardMYR();
        $mainBoard->setYearNum(0);
        $mainBoard->setGame($game);
        $entityManager->persist($mainBoard);
        $season = new SeasonMYR();
        $season->setName(MyrmesParameters::SPRING_SEASON_NAME);
        $season->setDiceResult(1);
        $season->setActualSeason(true);
        $season->setMainBoard($mainBoard);
        $mainBoard->addSeason($season);
        $entityManager->persist($season);
        $game->setMainBoardMYR($mainBoard);
        $game->setGameName("test");
        $game->setLaunched(true);
        $game->setGamePhase(MyrmesParameters::PHASE_INVALID);
        $entityManager->persist($mainBoard);
        $entityManager->persist($game);
        for ($i = 0; $i < $numberOfPlayers; $i += 1) {
            $player = new PlayerMYR('test' . $i, $game);
            $game->addPlayer($player);
            $player->setGameMyr($game);
            $player->setColor("");
            $player->setPhase(MyrmesParameters::PHASE_WORKER);
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
            for($j = 0; $j < MyrmesParameters::START_NURSES_COUNT_PER_PLAYER; $j += 1) {
                $nurse = new NurseMYR();
                $nurse->setArea(MyrmesParameters::LARVAE_AREA);
                $nurse->setAvailable(true);
                $personalBoard->addNurse($nurse);
                $entityManager->persist($nurse);
            }
            $playerActions = array();
            for($j = MyrmesParameters::WORKSHOP_GOAL_AREA; $j <= MyrmesParameters::WORKSHOP_NURSE_AREA; $j += 1) {
                $playerActions[$j] = 0;
            }
            $player->setWorkshopActions($playerActions);
            $entityManager->persist($player);
            $entityManager->persist($personalBoard);
            foreach ($this->resourceMYRRepository->findAll() as $resource) {
                $playerResource = new PlayerResourceMYR();
                $playerResource->setResource($resource);
                $playerResource->setQuantity(0);
                $player->getPersonalBoardMYR()
                       ->addPlayerResourceMYR($playerResource);
                $this->entityManager->persist($playerResource);
                $this->entityManager->persist($player->getPersonalBoardMYR());
            }
            $entityManager->flush();
        }
        $entityManager->flush();
        return $game;
    }
}