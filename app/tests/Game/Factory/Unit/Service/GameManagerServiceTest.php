<?php


namespace App\Tests\Game\Factory\Unit\Service;

use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Platform\User;
use App\Repository\Game\Glenmore\GameGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Myrmes\GameMYRRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\GameManagerService;
use App\Service\Game\Glenmore\GLMGameManagerService;
use App\Service\Game\Myrmes\MYRGameManagerService;
use App\Service\Game\SixQP\SixQPGameManagerService;
use App\Service\Game\Splendor\SPLGameManagerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GameManagerServiceTest extends TestCase
{

    private GameManagerService $gameService;
    private SixQPGameManagerService $sixQPGameManagerService;
    private SPLGameManagerService $SPLGameManagerService;
    private GLMGameManagerService $GLMGameManagerService;
    private MYRGameManagerService $MYRGameManagerService;
    private GameSixQPRepository $gameSixQPRepository;
    private GameSPLRepository $gameSPLRepository;
    private GameGLMRepository $gameGLMRepository;
    private GameMYRRepository $gameMYRRepository;

    protected function setUp(): void
    {
        $this->gameSixQPRepository = $this->createMock(GameSixQPRepository::class);
        $this->gameSPLRepository = $this->createMock(GameSPLRepository::class);
        $this->gameGLMRepository = $this->createMock(GameGLMRepository::class);
        $this->gameMYRRepository = $this->createMock(GameMYRRepository::class);

        $playerSixQPRepository = $this->createMock(PlayerSixQPRepository::class);
        $playerSplendorRepository = $this->createMock(PlayerSPLRepository::class);
        $playerGlenmoreRepository = $this->createMock(PlayerGLMRepository::class);
        $playerMyrmesRepository = $this->createMock(PlayerMYRRepository::class);

        $this->sixQPGameManagerService = $this->createMock(SixQPGameManagerService::class);
        $this->SPLGameManagerService = $this->createMock(SPLGameManagerService::class);
        $this->GLMGameManagerService = $this->createMock(GLMGameManagerService::class);
        $this->MYRGameManagerService = $this->createMock(MYRGameManagerService::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $this->gameService = new GameManagerService(
            $this->gameSixQPRepository, $this->gameSPLRepository,
            $this->gameGLMRepository, $this->gameMYRRepository,
            $playerSixQPRepository, $playerSplendorRepository, $playerGlenmoreRepository, $playerMyrmesRepository,
            $this->sixQPGameManagerService, $this->SPLGameManagerService,
            $this->GLMGameManagerService, $this->MYRGameManagerService,
            $entityManager);
    }


    public function testCreate6QPGameSuccessful()
    {
        // GIVEN
        $this->createGameManagerServiceWithMockFunctionRepository('createGame');
        // WHEN
        $result = $this->gameService->createGame(AbstractGameManagerService::SIXQP_LABEL);
        // THEN
        $this->assertEquals(1, $result);
    }

    public function testJoinGameWhenInvalidGame()
    {
        // GIVEN
        $user = new User();
        // WHEN
        $result = $this->gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testJoinGameWhenGameAlreadyLaunched()
    {
        // GIVEN
        $this->createGameManagerServiceWithMockFunctionWillReturn('createPlayer',
                                        AbstractGameManagerService::ERROR_GAME_ALREADY_LAUNCHED);
        $user = new User();
        $user->setUsername("testUser");
        // WHEN
        $result = $this->gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testJoinGameWhenPlayerAlreadyInParty()
    {
        // GIVEN
       $this->createGameManagerServiceWithMockFunctionWillReturn('createPlayer',
            AbstractGameManagerService::ERROR_ALREADY_IN_PARTY);
        $user = new User();
        $user->setUsername("testUser");
        // WHEN
        $result = $this->gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_ALREADY_IN_PARTY, $result);
    }

    public function testJoinGameWhenInvalidNumberOfPlayer()
    {
        // GIVEN
        $this->createGameManagerServiceWithMockFunctionWillReturn('createPlayer',
            AbstractGameManagerService::ERROR_INVALID_NUMBER_OF_PLAYER);
        $user = new User();
        $user->setUsername("testUser");
        // WHEN
        $result = $this->gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_NUMBER_OF_PLAYER, $result);
    }

    public function testJoinGameSuccessful()
    {
        // GIVEN

        $this->createGameManagerServiceWithMockFunctionWillReturn('createPlayer',
            AbstractGameManagerService::SUCCESS);
        $user = new User();
        $user->setUsername("testUser");
        // WHEN
        $result = $this->gameService->joinGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $result);
    }

    public function testDeletePlayerWhenGameIsInvalid()
    {
        // GIVEN
        $user = new User();
        // WHEN
        $result = $this->gameService->quitGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testDeletePlayerWhenPlayerNotFound()
    {
        // GIVEN
        $this->createGameManagerServiceWithMockFunctionWillReturn('deletePlayer',
            AbstractGameManagerService::ERROR_PLAYER_NOT_FOUND);
        $user = new User();
        $user->setUsername("testUser");
        // WHEN
        $result = $this->gameService->quitGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_PLAYER_NOT_FOUND, $result);
    }

    public function testDeletePlayerWhenGameAlreadyLaunched()
    {
        // GIVEN
        $this->createGameManagerServiceWithMockFunctionWillReturn('deletePlayer',
            AbstractGameManagerService::ERROR_GAME_ALREADY_LAUNCHED);
        $user = new User();
        $user->setUsername("testUser");
        // WHEN
        $result = $this->gameService->quitGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_GAME_ALREADY_LAUNCHED, $result);
    }

    public function testDeletePlayerSuccessful()
    {
        // GIVEN
        $this->createGameManagerServiceWithMockFunctionWillReturn('deletePlayer',
            AbstractGameManagerService::SUCCESS);
        $user = new User();
        $user->setUsername("testUser");
        // WHEN
        $result = $this->gameService->quitGame(-1, $user);
        // THEN
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $result);
    }

    public function testDeleteGameWhenGameIsInvalid()
    {
        // GIVEN
        // WHEN
        $result = $this->gameService->deleteGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testDeleteGameSuccessful()
    {
        // GIVEN
        $this->createGameManagerServiceWithMockFunctionWillReturn('deleteGame',
            AbstractGameManagerService::SUCCESS);
        $user = new User();
        $user->setUsername("testUser");
        // WHEN
        $result = $this->gameService->deleteGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $result);
    }

    public function testLaunchGameWhenGameIsInvalid()
    {
        // GIVEN
        // WHEN
        $result = $this->gameService->launchGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::ERROR_INVALID_GAME, $result);
    }

    public function testLaunchGameSuccessful()
    {
        // GIVEN
        $this->createGameManagerServiceWithMockFunctionWillReturn('launchGame',
            AbstractGameManagerService::SUCCESS);
        $user = new User();
        $user->setUsername("testUser");
        // WHEN
        $result = $this->gameService->launchGame(-1);
        // THEN
        $this->assertEquals(AbstractGameManagerService::SUCCESS, $result);
    }

    /**
     * createGameManagerServiceWithMockFunction : initialize a GameManagerService and mock function name in parameter
     *          to return success
     * @param string $functionName
     * @return void
     */
    private function createGameManagerServiceWithMockFunctionRepository(string $functionName) : void
    {
        $this->sixQPGameManagerService->method($functionName)->willReturn(AbstractGameManagerService::SUCCESS);
        $this->SPLGameManagerService->method($functionName)->willReturn(AbstractGameManagerService::SUCCESS);
        $this->GLMGameManagerService->method($functionName)->willReturn(AbstractGameManagerService::SUCCESS);
        $this->MYRGameManagerService->method($functionName)->willReturn(AbstractGameManagerService::SUCCESS);
    }

    /**
     * createGameManagerServiceWithMockFunctionRepositoryAndService : initialize a GameManagerService and mock
     *          functions name in parameter
     *           to return success
     * @param string $functionNameService
     * @param int $returnCode
     * @return void
     */
    private function createGameManagerServiceWithMockFunctionWillReturn(string $functionNameService,
                                                                        int $returnCode)
    : void
    {
        $this->gameSixQPRepository->method('findOneBy')->willReturn(new GameSixQP());
        $this->gameSPLRepository->method('findOneBy')->willReturn(new GameSPL());
        $this->gameGLMRepository->method('findOneBy')->willReturn(new GameGLM());
        $this->gameMYRRepository->method('findOneBy')->willReturn(new GameMYR());
        $this->sixQPGameManagerService->method($functionNameService)->willReturn($returnCode);
        $this->SPLGameManagerService->method($functionNameService)->willReturn($returnCode);
        $this->GLMGameManagerService->method($functionNameService)->willReturn($returnCode);
        $this->MYRGameManagerService->method($functionNameService)->willReturn($returnCode);
    }
}
