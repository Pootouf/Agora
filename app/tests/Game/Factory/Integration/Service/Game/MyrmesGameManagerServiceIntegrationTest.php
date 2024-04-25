<?php

namespace App\Tests\Game\Factory\Integration\Service\Game;

use App\Entity\Game\GameUser;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\GameManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MyrmesGameManagerServiceIntegrationTest extends KernelTestCase
{
    private readonly EntityManagerInterface $entityManager;
    private readonly GameManagerService $gameManager;

    protected function setUp(): void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->gameManager = static::getContainer()->get(GameManagerService::class);
    }

    public function testJoinGameWhenGameIsNull() : void
    {
        // GIVEN
        $gameId = -1;
        // WHEN
        $result = $this->gameManager->joinGame($gameId, new GameUser());
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testJoinGameWhenGameIsFull() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(10);
        $gameId = $game->getId();
        $user = new GameUser();
        $user->setUsername("test");
        $this->entityManager->persist($user);
        // WHEN
        $result = $this->gameManager->joinGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_NUMBER_OF_PLAYER, $result);
    }

    public function testJoinGameWhenGameIsLaunched() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(3);
        $game->setLaunched(true);
        $gameId = $game->getId();
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $user = new GameUser();
        $user->setUsername("test");
        $this->entityManager->persist($user);
        // WHEN
        $result = $this->gameManager->joinGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testJoinGameWhenPlayerAlreadyInGame(): void
    {
        // GIVEN
        $game = $this->createMyrmesGame(2);
        $user = new GameUser();
        $user->setUsername("test");
        $this->gameManager->joinGame($game->getId(), $user);
        // WHEN
        $result = $this->gameManager->joinGame($game->getId(), $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_ALREADY_IN_PARTY, $result);
    }

    public function testJoinGameSuccessfully(): void
    {
        // GIVEN
        $game = $this->createMyrmesGame(3);
        $user = new GameUser();
        $user->setUsername("test");
        // WHEN
        $result = $this->gameManager->joinGame($game->getId(), $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $result);
    }

    public function testQuitGameWhenGameIsNull(): void
    {
        // GIVEN
        $gameId = -1;
        $user = new GameUser();
        // WHEN
        $result = $this->gameManager->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testQuitGameWhenGameIsLaunched() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(3);
        $game->setLaunched(true);
        $gameId = $game->getId();
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $user = new GameUser();
        $user->setUsername("test");
        $this->entityManager->persist($user);
        // WHEN
        $result = $this->gameManager->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testQuitGameWhenPlayerIsInvalid() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(3);
        $gameId = $game->getId();
        $user = new GameUser();
        $user->setUsername("vhuheguruogvtyugbseyvgbyosbge");
        $this->entityManager->persist($user);
        // WHEN
        $result = $this->gameManager->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_PLAYER_NOT_FOUND, $result);
    }

    public function testQuitGameOnSuccess() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(3);
        $gameId = $game->getId();
        $userName = $game->getPlayers()->first()->getUsername();
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $user = new GameUser();
        $user->setUsername($userName);
        $user->setPassword("uyfvgbuybgvyugbr");
        // WHEN
        $result = $this->gameManager->quitGame($gameId, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $result);
    }

    public function testDeleteGameWhenGameIsInvalid() : void
    {
        // WHEN
        $result = $this->gameManager->deleteGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testDeleteGameWhenGameIsValid() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(2);
        $game->setGameName(AbstractGameManagerService::MYR_LABEL);
        $game->setGamePhase(0);
        // WHEN
        $result = $this->gameManager->deleteGame($game->getId());
        // THEN
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $result);
    }

    public function testLaunchGameMYRFailWhenNotEnoughPlayers() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(1);
        // WHEN
        $result = $this->gameManager->launchGame($game->getId());
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_NUMBER_OF_PLAYER, $result);
    }

    public function testLaunchGameMYRFailWhenTooMuchPlayers() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(5);
        // WHEN
        $result = $this->gameManager->launchGame($game->getId());
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_NUMBER_OF_PLAYER, $result);
    }

    public function testLaunchGameMYRFailWhenGameIsInvalid() : void
    {
        // GIVEN
        // WHEN
        $result = $this->gameManager->launchGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testLaunchGameMYRFailWhenGameIsValid() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(4);
        // WHEN
        $result = $this->gameManager->launchGame($game->getId());
        // THEN
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $result);
    }

    public function testExcludePlayerGameMYR() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(4);
        $player = $game->getPlayers()->first();
        // WHEN
        $this->gameManager->excludePlayer($player, $game);
        // THEN
        $this->assertTrue($player->isExcluded());
        $this->assertNull($player->getUsername());
        $this->assertTrue($game->isPaused());
    }

    public function testReplacePlayerGameMYR() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(4);
        $player = $game->getPlayers()->first();
        $userName = "hieyrgyibri";
        // WHEN
        $this->gameManager->replacePlayer($player, $userName, $game);
        // THEN
        $this->assertFalse($player->isExcluded());
        $this->assertNotNull($player->getUsername());
        $this->assertFalse($game->isPaused());
    }

    public function testGetPlayerFromGameIdMYR() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(4);
        $firstPlayerId = $game->getPlayers()->first()->getId();
        // WHEN
        $result = $this->gameManager->getPlayerFromId($firstPlayerId);
        // THEN
        $this->assertNotNull($result);
    }

    public function testGetExcludedPlayerFromGameIdMYR() : void
    {
        // GIVEN
        $game = $this->createMyrmesGame(4);
        $id = $game->getId();
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->setExcluded(true);
        $this->entityManager->persist($firstPlayer);
        $this->entityManager->flush();
        // WHEN
        $result = $this->gameManager->getExcludedPlayerFromGameId($id);
        // THEN
        $this->assertNotNull($result);
    }

    private function createMyrmesGame(int $numberOfPlayer) : GameMYR
    {
        $game = new GameMYR();
        $game->setGameName(AbstractGameManagerService::MYR_LABEL);
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
        $game->setGameName(AbstractGameManagerService::MYR_LABEL);
        $game->setLaunched(false);
        $game->setGamePhase(MyrmesParameters::PHASE_INVALID);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->persist($game);
        for ($i = 0; $i < $numberOfPlayer; $i += 1) {
            $player = new PlayerMYR('test'.$i, $game);
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
            $this->entityManager->flush();
        }
        $this->entityManager->flush();
        return $game;
    }
}