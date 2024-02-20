<?php


namespace App\Tests\Game\Factory\Unit\Service;

use App\Entity\Game\GameUser;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\Splendor\GameSPL;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\GameManagerService;
use App\Service\Game\SixQP\SixQPGameManagerService;
use App\Service\Game\Splendor\SPLGameManagerService;
use PHPUnit\Framework\TestCase;

class GameManagerServiceTest extends TestCase
{
    /*
    private GameManagerService $gameService;

    protected function setUp(): void
    {
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $this->gameService = new GameManagerService($gameSixQPRepository, $sixQPService);
    }
    */

    public function testCreate6QPGameSuccessful()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $sixQPService->method('createGame')->willReturn(1);
        $splendorService->method('createGame')->willReturn(1);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        // WHEN
        $result = $gameService->createGame(AbstractGameManagerService::$SIXQP_LABEL);
        // THEN
        $this->assertEquals(1, $result);
    }

    public function testJoinGameWhenInvalidGame()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        // WHEN
        $result = $gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_GAME, $result);
    }

    public function testJoinGameWhenGameAlreadyLaunched()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameSplendorRepository->method('findOneBy')->willReturn(new GameSPL());
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method('createPlayer')->

            willReturn(AbstractGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $splendorService->method('createPlayer')->
            willReturn(AbstractGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        $user->setUsername("testUser");
        // WHEN
        $result = $gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testJoinGameWhenPlayerAlreadyInParty()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $gameSplendorRepository->method('findOneBy')->willReturn(new GameSPL());
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method('createPlayer')->
            willReturn(AbstractGameManagerService::$ERROR_ALREADY_IN_PARTY);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $splendorService->method('createPlayer')->
            willReturn(AbstractGameManagerService::$ERROR_ALREADY_IN_PARTY);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        $user->setUsername("testUser");
        // WHEN
        $result = $gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_ALREADY_IN_PARTY, $result);
    }

    public function testJoinGameWhenInvalidNumberOfPlayer()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameSplendorRepository->method('findOneBy')->willReturn(new GameSPL());
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method('createPlayer')->
            willReturn(AbstractGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $splendorService->method('createPlayer')->
            willReturn(AbstractGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        $user->setUsername("testUser");
        // WHEN
        $result = $gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER, $result);
    }

    public function testJoinGameSuccessful()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameSplendorRepository->method('findOneBy')->willReturn(new GameSPL());
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method('createPlayer')->
            willReturn(AbstractGameManagerService::$SUCCESS);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $splendorService->method('createPlayer')->
            willReturn(AbstractGameManagerService::$SUCCESS);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        $user->setUsername("testUser");
        // WHEN
        $result = $gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$SUCCESS, $result);
    }

    public function testDeletePlayerWhenGameIsInvalid()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        // WHEN
        $result = $gameService->quitGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_GAME, $result);
    }

    public function testDeletePlayerWhenPlayerNotFound()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameSplendorRepository->method('findOneBy')->willReturn(new GameSPL());
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method('deletePlayer')->
            willReturn(AbstractGameManagerService::$ERROR_PLAYER_NOT_FOUND);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $splendorService->method('deletePlayer')->
            willReturn(AbstractGameManagerService::$ERROR_PLAYER_NOT_FOUND);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        $user->setUsername("testUser");
        // WHEN
        $result = $gameService->quitGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_PLAYER_NOT_FOUND, $result);
    }

    public function testDeletePlayerWhenGameAlreadyLaunched()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameSplendorRepository->method('findOneBy')->willReturn(new GameSPL());
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method('deletePlayer')->
        willReturn(AbstractGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        $user->setUsername("testUser");
        // WHEN
        $result = $gameService->quitGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testDeletePlayerSuccessful()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameSplendorRepository->method('findOneBy')->willReturn(new GameSPL());
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method('deletePlayer')->
            willReturn(AbstractGameManagerService::$SUCCESS);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $splendorService->method('deletePlayer')->
            willReturn(AbstractGameManagerService::$SUCCESS);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        $user->setUsername("testUser");
        // WHEN
        $result = $gameService->quitGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$SUCCESS, $result);
    }

    public function testDeleteGameWhenGameIsInvalid()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        // WHEN
        $result = $gameService->deleteGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_GAME, $result);
    }

    public function testDeleteGameSuccessful()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameSplendorRepository->method('findOneBy')->willReturn(new GameSPL());
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method('deleteGame')->
            willReturn(AbstractGameManagerService::$SUCCESS);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $splendorService->method('deleteGame')->
            willReturn(AbstractGameManagerService::$SUCCESS);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        $user->setUsername("testUser");
        // WHEN
        $result = $gameService->deleteGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$SUCCESS, $result);
    }

    public function testLaunchGameWhenGameIsInvalid()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        // WHEN
        $result = $gameService->launchGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_GAME, $result);
    }

    public function testLaunchGameSuccessful()
    {
        // GIVEN
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameSplendorRepository->method('findOneBy')->willReturn(new GameSPL());
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method('launchGame')->
            willReturn(AbstractGameManagerService::$SUCCESS);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $splendorService->method('launchGame')->
            willReturn(AbstractGameManagerService::$SUCCESS);
        $gameService = new GameManagerService($gameSixQPRepository, $gameSplendorRepository,
            $sixQPService, $splendorService);
        $user = new GameUser();
        $user->setUsername("testUser");
        // WHEN
        $result = $gameService->launchGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$SUCCESS, $result);
    }
}
