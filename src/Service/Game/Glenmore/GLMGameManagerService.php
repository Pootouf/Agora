<?php

namespace App\Service\Game\Glenmore;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class GLMGameManagerService extends AbstractGameManagerService
{

    public function __construct(private EntityManagerInterface $entityManager,
        private GLMService $GLMService,
        private PlayerGLMRepository $playerGLMRepository,
        private LogService $logService)
    {}


    /**
     * createGame : create a Glenmore game
     */
    public function createGame(): int
    {
        $game = new GameGLM();
        $game->setGameName(AbstractGameManagerService::$GLM_LABEL);
        $mainBoard = new MainBoardGLM();
        $game->setMainBoard($mainBoard);
        $warehouse = new WarehouseGLM();
        $mainBoard->setWarehouse($warehouse);

        $drawTileZero = new DrawTilesGLM();
        $drawTileZero->setLevel(GlenmoreParameters::$TILE_LEVEL_ZERO);
        $mainBoard->addDrawTile($drawTileZero);
        $this->entityManager->persist($drawTileZero);

        $drawTileOne = new DrawTilesGLM();
        $drawTileOne->setLevel(GlenmoreParameters::$TILE_LEVEL_ONE);
        $mainBoard->addDrawTile($drawTileOne);
        $this->entityManager->persist($drawTileOne);

        $drawTileTwo = new DrawTilesGLM();
        $drawTileTwo->setLevel(GlenmoreParameters::$TILE_LEVEL_TWO);
        $mainBoard->addDrawTile($drawTileTwo);
        $this->entityManager->persist($drawTileTwo);

        $drawTileThree = new DrawTilesGLM();
        $drawTileThree->setLevel(GlenmoreParameters::$TILE_LEVEL_THREE);
        $mainBoard->addDrawTile($drawTileThree);
        $this->entityManager->persist($drawTileThree);

        $this->entityManager->persist($warehouse);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $this->logService->sendSystemLog($game, "game " . $game->getId() . " was created");
        return $game->getId();
    }

    /**
     * createPlayer : create a Glenmore player and save him in the database
     */
    public function createPlayer(string $playerName, Game $game): int
    {
        $game = $this->getGameGlenmoreFromGame($game);
        if ($game == null) {
            return GLMGameManagerService::$ERROR_INVALID_GAME;
        }
        if($game->isLaunched()) {
            return GLMGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        if (count($game->getPlayers()) >= GlenmoreParameters::$MAX_NUMBER_OF_PLAYER) {
            return GLMGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER;
        }
        if ($this->playerGLMRepository->findOneBy(
            ['username' => $playerName, 'gameGLM' => $game->getId()]) != null) {
            return GLMGameManagerService::$ERROR_ALREADY_IN_PARTY;
        }
        $player = new PlayerGLM($playerName, $game);
        $player->setPoints(0);
        $pawn = new PawnGLM();
        $pawn->setPosition(0);
        $pawn->setColor(GlenmoreParameters::$COLOR_FROM_POSITION[$game->getPlayers()->count()]);
        $pawn->setMainBoardGLM($game->getMainBoard());
        $player->setPawn($pawn);
        $personalBoard = new PersonalBoardGLM();
        $personalBoard->setLeaderCount(0);
        $personalBoard->setMoney(0);
        $player->setPersonalBoard($personalBoard);
        $game->addPlayer($player);
        $this->entityManager->persist($player);
        $this->entityManager->persist($pawn);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
        $this->logService->sendPlayerLog($game, $player,
            $playerName . " joined game " . $game->getId());
        return GLMGameManagerService::$SUCCESS;
    }

    /**
     * deletePlayer : delete a Glenmore player
     */
    public function deletePlayer(string $playerName, Game $game): int
    {
        $game = $this->getGameGlenmoreFromGame($game);
        if ($game == null) {
            return GLMGameManagerService::$ERROR_INVALID_GAME;
        }
        if ($game->isLaunched()) {
            return GLMGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        $player = $this->GLMService->getPlayerFromNameAndGame($game, $playerName);
        if ($player == null) {
            return GLMGameManagerService::$ERROR_PLAYER_NOT_FOUND;
        }
        $this->entityManager->remove($player);
        $this->entityManager->flush();
        $this->logService->sendSystemLog($game,
            $playerName . " was removed from the game " . $game->getId());
        return GLMGameManagerService::$SUCCESS;
    }

    /**
     * deleteGame : delete a Glenmore game
     */
    public function deleteGame(Game $game): int
    {
        $game = $this->getGameGlenmoreFromGame($game);
        if ($game == null) {
            return GLMGameManagerService::$ERROR_INVALID_GAME;
        }
        foreach ($game->getPlayers() as $player) {
            $this->entityManager->remove($player);
        }
        $this->logService->sendSystemLog($game, "game " . $game->getId() . "ended");
        $this->entityManager->remove($game);
        $this->entityManager->flush();
        return GLMGameManagerService::$SUCCESS;
    }

    /**
     * launchGame : launch a Glenmore game
     * @throws Exception if game invalid
     */
    public function launchGame(Game $game): int
    {
        $game = $this->getGameGlenmoreFromGame($game);
        if ($game == null) {
            return GLMGameManagerService::$ERROR_INVALID_GAME;
        }
        $numberOfPlayers = count($game->getPlayers());
        if ($numberOfPlayers > GlenmoreParameters::$MAX_NUMBER_OF_PLAYER
            || $numberOfPlayers < GlenmoreParameters::$MIN_NUMBER_OF_PLAYER) {
            return GLMGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER;
        }
        $game->setLaunched(true);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $this->GLMService->initializeNewGame($game);
        $this->logService->sendSystemLog($game, "game " . $game->getId() . " began");
        return GLMGameManagerService::$SUCCESS;
    }


    private function getGameGlenmoreFromGame(Game $game): ?GameGLM
    {
        /** @var GameGLM $game */
        return $game->getGameName() == AbstractGameManagerService::$GLM_LABEL ? $game : null;
    }
}