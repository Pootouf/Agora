<?php

namespace App\Tests\Game\Myrmes\Integration\Service;

use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Repository\Game\Myrmes\GameMYRRepository;
use App\Service\Game\Myrmes\MYRGameManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MyrGameManagerServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private MYRGameManagerService $myrGameManagerService;
    private GameMYRRepository $gameMYRRepository;

    protected function setUp(): void
    {
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->myrGameManagerService = static::getContainer()->get(MyrGameManagerService::class);
        $this->gameMYRRepository = static::getContainer()->get(GameMYRRepository::class);
    }

    public function testCreateGame() {
        // GIVEN
        // WHEN
        $game = $this->myrGameManagerService->createGame();
        // THEN
        $this->assertNotNull($game);
    }

    public function testCreatePlayerWhenGameIsLaunched() {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        $game->setLaunched(true);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $result = $this->myrGameManagerService->createPlayer("test", $game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testCreatePlayerWhenGameHaveToMuchPlayers() {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        for ($i = 0; $i <= MyrmesParameters::MAX_NUMBER_OF_PLAYER; ++ $i) {
            $player = new PlayerMYR("test1".$i, $game);
            $player->setColor("blue");
            $player->setTurnOfPlayer(false);
            $player->setScore(0);
            $player->setPhase(0);
            $player->setRemainingHarvestingBonus(0);
            $player->setGoalLevel(0);

            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setPlayer($player);
            $personalBoard->setBonus(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setLarvaCount(0);
            $personalBoard->setSelectedEventLarvaeAmount(0);
            $this->entityManager->persist($personalBoard);

            $player->setPersonalBoardMYR($personalBoard);
            $this->entityManager->persist($player);
            $game->addPlayer($player);
            $this->entityManager->flush();
        }
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $result = $this->myrGameManagerService->createPlayer("test", $game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER, $result);
    }

    public function testCreatePlayerWhenPlayerAlreadyInGame() {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        for ($i = 0; $i < MyrmesParameters::MAX_NUMBER_OF_PLAYER - 1; ++ $i) {
            $player = new PlayerMYR("jean", $game);
            $player->setColor("blue");
            $player->setTurnOfPlayer(false);
            $player->setScore(0);
            $player->setPhase(0);
            $player->setRemainingHarvestingBonus(0);
            $player->setGoalLevel(0);

            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setPlayer($player);
            $personalBoard->setBonus(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setLarvaCount(0);
            $personalBoard->setSelectedEventLarvaeAmount(0);
            $this->entityManager->persist($personalBoard);

            $player->setPersonalBoardMYR($personalBoard);
            $this->entityManager->persist($player);
            $game->addPlayer($player);
            $this->entityManager->flush();
        }
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $result = $this->myrGameManagerService->createPlayer("jean", $game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$ERROR_ALREADY_IN_PARTY, $result);
    }

    public function testCreatePlayer() {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        $game->setLaunched(false);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $result = $this->myrGameManagerService->createPlayer("test", $game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$SUCCESS, $result);
    }

    public function testDeletePlayerWhenGameIsLaunched() {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        $game->setLaunched(true);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $result = $this->myrGameManagerService->deletePlayer("test", $game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testDeletePlayerWhenPlayerNotInGame() {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        $game->setLaunched(false);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $result = $this->myrGameManagerService->deletePlayer("test", $game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$ERROR_PLAYER_NOT_FOUND, $result);
    }

    public function testDeletePlayer() {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        $game->setLaunched(false);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $this->myrGameManagerService->createPlayer("test", $game);
        // WHEN
        $result = $this->myrGameManagerService->deletePlayer("test", $game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$SUCCESS, $result);
    }

    /*public function testDeleteGame() {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        $game->setLaunched(false);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $this->myrGameManagerService->createPlayer("test", $game);
        // WHEN
        $result = $this->myrGameManagerService->deleteGame($game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$SUCCESS, $result);
    }*/

    public function testLaunchGameWhenNotEnoughPlayers()
    {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        for ($i = 0; $i < MyrmesParameters::MIN_NUMBER_OF_PLAYER - 1; ++ $i) {
            $player = new PlayerMYR("test3".$i, $game);
            $player->setColor("blue");
            $player->setTurnOfPlayer(false);
            $player->setScore(0);
            $player->setPhase(0);
            $player->setRemainingHarvestingBonus(0);
            $player->setGoalLevel(0);

            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setPlayer($player);
            $personalBoard->setBonus(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setLarvaCount(0);
            $personalBoard->setSelectedEventLarvaeAmount(0);
            $this->entityManager->persist($personalBoard);

            $player->setPersonalBoardMYR($personalBoard);
            $this->entityManager->persist($player);
            $game->addPlayer($player);
            $this->entityManager->flush();
        }
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $result = $this->myrGameManagerService->launchGame($game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER, $result);
    }

    public function testLaunchGameWhenTooMuchPlayers()
    {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        for ($i = 0; $i < MyrmesParameters::MAX_NUMBER_OF_PLAYER + 1; ++ $i) {
            $player = new PlayerMYR("test4".$i, $game);
            $player->setColor("blue");
            $player->setTurnOfPlayer(false);
            $player->setScore(0);
            $player->setPhase(0);
            $player->setRemainingHarvestingBonus(0);
            $player->setGoalLevel(0);

            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setPlayer($player);
            $personalBoard->setBonus(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setLarvaCount(0);
            $personalBoard->setSelectedEventLarvaeAmount(0);
            $this->entityManager->persist($personalBoard);

            $player->setPersonalBoardMYR($personalBoard);
            $this->entityManager->persist($player);
            $game->addPlayer($player);
            $this->entityManager->flush();
        }
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $result = $this->myrGameManagerService->launchGame($game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER, $result);
    }

    public function testLaunchGameWhenGameIsLaunched()
    {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        $game->setLaunched(true);
        for ($i = 0; $i < MyrmesParameters::MAX_NUMBER_OF_PLAYER; ++ $i) {
            $player = new PlayerMYR("test5".$i, $game);
            $player->setColor("blue");
            $player->setTurnOfPlayer(false);
            $player->setScore(0);
            $player->setPhase(0);
            $player->setRemainingHarvestingBonus(0);
            $player->setGoalLevel(0);

            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setPlayer($player);
            $personalBoard->setBonus(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setLarvaCount(0);
            $personalBoard->setSelectedEventLarvaeAmount(0);
            $this->entityManager->persist($personalBoard);

            $player->setPersonalBoardMYR($personalBoard);
            $this->entityManager->persist($player);
            $game->addPlayer($player);
            $this->entityManager->flush();
        }
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $result = $this->myrGameManagerService->launchGame($game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testLaunchGameWhenNoProblem()
    {
        // GIVEN
        $game= $this->myrGameManagerService->createGame();
        $game = $this->gameMYRRepository->findOneBy(["id" => $game]);
        for ($i = 0; $i < MyrmesParameters::MAX_NUMBER_OF_PLAYER; ++ $i) {
            $player = new PlayerMYR("test6".$i, $game);
            $player->setColor("blue");
            $player->setTurnOfPlayer(false);
            $player->setScore(0);
            $player->setPhase(0);
            $player->setRemainingHarvestingBonus(0);
            $player->setGoalLevel(0);

            $personalBoard = new PersonalBoardMYR();
            $personalBoard->setPlayer($player);
            $personalBoard->setBonus(0);
            $personalBoard->setWarriorsCount(0);
            $personalBoard->setAnthillLevel(0);
            $personalBoard->setLarvaCount(0);
            $personalBoard->setSelectedEventLarvaeAmount(0);
            $this->entityManager->persist($personalBoard);

            $player->setPersonalBoardMYR($personalBoard);
            $this->entityManager->persist($player);
            $game->addPlayer($player);
            $this->entityManager->flush();
        }
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        // WHEN
        $result = $this->myrGameManagerService->launchGame($game);
        // THEN
        $this->assertEquals(MYRGameManagerService::$SUCCESS, $result);
    }
}