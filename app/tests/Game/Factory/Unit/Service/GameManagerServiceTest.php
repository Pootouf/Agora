<?php


namespace App\Tests\Game\Factory\Unit\Service;

use App\Entity\Game\GameUser;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\Splendor\GameSPL;
use App\Repository\Game\Glenmore\GameGLMRepository;
use App\Repository\Game\Myrmes\GameMYRRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\GameManagerService;
use App\Service\Game\Glenmore\GLMGameManagerService;
use App\Service\Game\Myrmes\MYRGameManagerService;
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
        $gameService = $this->createGameManagerServiceWithMockFunctionRepository('createGame');
        // WHEN
        $result = $gameService->createGame(AbstractGameManagerService::$SIXQP_LABEL);
        // THEN
        $this->assertEquals(1, $result);
    }

    public function testJoinGameWhenInvalidGame()
    {
        // GIVEN
        $gameService = $this->createGameManagerService();
        $user = new GameUser();
        // WHEN
        $result = $gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_GAME, $result);
    }

    public function testJoinGameWhenGameAlreadyLaunched()
    {
        // GIVEN
        $gameService = $this->createGameManagerServiceWithMockFunctionWillReturn('createPlayer',
                                        AbstractGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED);
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
        $gameService = $this->createGameManagerServiceWithMockFunctionWillReturn('createPlayer',
            AbstractGameManagerService::$ERROR_ALREADY_IN_PARTY);
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
        $gameService = $this->createGameManagerServiceWithMockFunctionWillReturn('createPlayer',
            AbstractGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER);
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

        $gameService = $this->createGameManagerServiceWithMockFunctionWillReturn('createPlayer',
            AbstractGameManagerService::$SUCCESS);
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
        $gameService = $this->createGameManagerService();
        $user = new GameUser();
        // WHEN
        $result = $gameService->quitGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_GAME, $result);
    }

    public function testDeletePlayerWhenPlayerNotFound()
    {
        // GIVEN
        $gameService = $this->createGameManagerServiceWithMockFunctionWillReturn('deletePlayer',
            AbstractGameManagerService::$ERROR_PLAYER_NOT_FOUND);
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
        $gameService = $this->createGameManagerServiceWithMockFunctionWillReturn('deletePlayer',
            AbstractGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED);
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
        $gameService = $this->createGameManagerServiceWithMockFunctionWillReturn('deletePlayer',
            AbstractGameManagerService::$SUCCESS);
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
        $gameService = $this->createGameManagerService();
        // WHEN
        $result = $gameService->deleteGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_GAME, $result);
    }

    public function testDeleteGameSuccessful()
    {
        // GIVEN
        $gameService = $this->createGameManagerServiceWithMockFunctionWillReturn('deleteGame',
            AbstractGameManagerService::$SUCCESS);
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
        $gameService = $this->createGameManagerService();
        // WHEN
        $result = $gameService->launchGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$ERROR_INVALID_GAME, $result);
    }

    public function testLaunchGameSuccessful()
    {
        // GIVEN
        $gameService = $this->createGameManagerServiceWithMockFunctionWillReturn('launchGame',
            AbstractGameManagerService::$SUCCESS);
        $user = new GameUser();
        $user->setUsername("testUser");
        // WHEN
        $result = $gameService->launchGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::$SUCCESS, $result);
    }

    /**
     * createGameManagerServiceWithMockFunction : initialize a GameManagerService and mock function name in parameter
     *          to return success
     * @param string $functionName
     * @return GameManagerService
     */
    private function createGameManagerServiceWithMockFunctionRepository(string $functionName) : GameManagerService
    {
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameGlenmoreRepository = $this->createMock(GameGLMRepository::class);
        $gameMyrmesRepository = $this->createMock(GameMYRRepository::class);
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method($functionName)->willReturn(AbstractGameManagerService::$SUCCESS);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $splendorService->method($functionName)->willReturn(AbstractGameManagerService::$SUCCESS);
        $glenmoreService = $this->createMock(GLMGameManagerService::class);
        $glenmoreService->method($functionName)->willReturn(AbstractGameManagerService::$SUCCESS);
        $myrmesService = $this->createMock(MYRGameManagerService::class);
        $myrmesService->method($functionName)->willReturn(AbstractGameManagerService::$SUCCESS);
        return new GameManagerService($gameSixQPRepository, $gameSplendorRepository, $gameGlenmoreRepository,
            $gameMyrmesRepository, $sixQPService, $splendorService, $glenmoreService, $myrmesService);
    }

    /**
     * createGameManagerServiceWithMockFunctionRepositoryAndService : initialize a GameManagerService and mock
     *          functions name in parameter
     *           to return success
     * @param string $functionNameRepo
     * @param string $functionNameService
     * @return GameManagerService
     */
    private function createGameManagerServiceWithMockFunctionWillReturn(string $functionNameService,
                                                                        int $returnCode)
    : GameManagerService
    {
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameSplendorRepository->method('findOneBy')->willReturn(new GameSPL());
        $gameGlenmoreRepository = $this->createMock(GameGLMRepository::class);
        $gameGlenmoreRepository->method('findOneBy')->willReturn(new GameGLM());
        $gameMyrmesRepository = $this->createMock(GameMYRRepository::class);
        $gameMyrmesRepository->method('findOneBy')->willReturn(new GameMYR());
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $sixQPService->method($functionNameService)->
        willReturn($returnCode);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $splendorService->method($functionNameService)->
        willReturn($returnCode);
        $glenmoreService = $this->createMock(GLMGameManagerService::class);
        $glenmoreService->method($functionNameService)->
        willReturn($returnCode);
        $myrmesService = $this->createMock(MYRGameManagerService::class);
        $myrmesService->method($functionNameService)->
        willReturn($returnCode);
        return new GameManagerService($gameSixQPRepository, $gameSplendorRepository, $gameGlenmoreRepository,
            $gameMyrmesRepository, $sixQPService, $splendorService, $glenmoreService, $myrmesService);
    }

    /**
     * createGameManagerService : initialize a GameManagerService
     * @return GameManagerService
     */
    private function createGameManagerService() : GameManagerService
    {
        $gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $gameSplendorRepository = $this->createMock(GameSPLRepository::class);
        $gameGlenmoreRepository = $this->createMock(GameGLMRepository::class);
        $gameMyrmesRepository = $this->createMock(GameMYRRepository::class);
        $sixQPService = $this->createMock(SixQPGameManagerService::class);
        $splendorService = $this->createMock(SPLGameManagerService::class);
        $glenmoreService = $this->createMock(GLMGameManagerService::class);
        $myrmesService = $this->createMock(MYRGameManagerService::class);
        return new GameManagerService($gameSixQPRepository, $gameSplendorRepository, $gameGlenmoreRepository,
            $gameMyrmesRepository, $sixQPService, $splendorService, $glenmoreService, $myrmesService);
    }
}
