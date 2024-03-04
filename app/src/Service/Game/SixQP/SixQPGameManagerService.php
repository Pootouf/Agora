<?php

namespace App\Service\Game\SixQP;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Entity\Game\SixQP\SixQPParameters;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class SixQPGameManagerService extends AbstractGameManagerService
{
    private EntityManagerInterface $entityManager;
    private PlayerSixQPRepository $playerSixQPRepository;
    private SixQPService $sixQPService;
    private LogService $logService;
    public function __construct(EntityManagerInterface $entityManager,
        PlayerSixQPRepository $playerSixQPRepository,
        SixQPService $sixQPService,
        LogService $logService)
    {
        $this->entityManager = $entityManager;
        $this->playerSixQPRepository = $playerSixQPRepository;
        $this->sixQPService = $sixQPService;
        $this->logService = $logService;
    }


    /**
     * createGame : create a six q p game
     */
    public function createGame(): int
    {
        $game = new GameSixQP();
        $game->setGameName(AbstractGameManagerService::$SIXQP_LABEL);
        for($i = 0; $i < SixQPParameters::$NUMBER_OF_ROWS_BY_GAME; $i++) {
            $row = new RowSixQP();
            $row->setPosition($i);
            $game->addRowSixQP($row);
            $this->entityManager->persist($row);
        }

        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $this->logService->sendSystemLog($game, "game " . $game->getId() . " was created");
        return $game->getId();
    }

    /**
     * createPlayer : create a sixqp player and save him in the database
     */
    public function createPlayer(string $playerName, Game $game): int
    {
        $game = $this->getGameSixQPFromGame($game);
        if ($game == null) {
            return SixQPGameManagerService::$ERROR_INVALID_GAME;
        }
        if($game->isLaunched()) {
            return SixQPGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        if (count($game->getPlayerSixQPs()) >= 10) {
            return SixQPGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER;
        }
        if ($this->playerSixQPRepository->findOneBy(
            ['username' => $playerName, 'game' => $game->getId()]) != null) {
            return SixQPGameManagerService::$ERROR_ALREADY_IN_PARTY;
        }
        $player = new PlayerSixQP($playerName, $game);
        $discard = new DiscardSixQP($player, $game);
        $player->setDiscardSixQP($discard);
        $game->addPlayerSixQP($player);
        $this->entityManager->persist($player);
        $this->entityManager->persist($discard);
        $this->entityManager->flush();
        $this->logService->sendPlayerLog($game, $player,
            $playerName . " joined game " . $game->getId());
        return SixQPGameManagerService::$SUCCESS;
    }

    /**
     * deletePlayer : delete a sixqp player
     */
    public function deletePlayer(string $playerName, Game $game): int
    {
        $game = $this->getGameSixQPFromGame($game);
        if ($game == null) {
            return SixQPGameManagerService::$ERROR_INVALID_GAME;
        }
        if ($game->isLaunched()) {
            return SixQPGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        $player = $this->sixQPService->getPlayerFromNameAndGame($game, $playerName);
        if ($player == null) {
            return SixQPGameManagerService::$ERROR_PLAYER_NOT_FOUND;
        }
        $this->entityManager->remove($player);
        $this->entityManager->flush();
        $this->logService->sendSystemLog($game,
            $playerName . " was removed from the game " . $game->getId());
        return SixQPGameManagerService::$SUCCESS;
    }

    /**
     * deleteGame : delete a sixqpgame
     */
    public function deleteGame(Game $game): int
    {
        $game = $this->getGameSixQPFromGame($game);
        if ($game == null) {
            return SixQPGameManagerService::$ERROR_INVALID_GAME;
        }
        foreach ($game->getPlayerSixQPs() as $playerSixQP) {
            $this->entityManager->remove($playerSixQP);
        }
        foreach ($game->getRowSixQPs() as $rowSixQP) {
            $this->entityManager->remove($rowSixQP);
        }
        $this->logService->sendSystemLog($game, "game " . $game->getId() . "ended");
        $this->entityManager->remove($game);
        $this->entityManager->flush();
        return SixQPGameManagerService::$SUCCESS;
    }

    /**
     * launchGame : launch a sixqpgame
     * @throws Exception if game invalid
     */
    public function launchGame(Game $game): int
    {
        $game = $this->getGameSixQPFromGame($game);
        if ($game == null) {
            return SixQPGameManagerService::$ERROR_INVALID_GAME;
        }
        $numberOfPlayers = count($game->getPlayerSixQPs());
        if ($numberOfPlayers > 10 || $numberOfPlayers < 2) {
            return SixQPGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER;
        }
        $game->setLaunched(true);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $this->sixQPService->initializeNewRound($game);
        $this->logService->sendSystemLog($game, "game " . $game->getId() . " began");
        return SixQPGameManagerService::$SUCCESS;
    }


    private function getGameSixQPFromGame(Game $game): ?GameSixQP {
        /** @var GameSixQP $game */
        return $game->getGameName() == AbstractGameManagerService::$SIXQP_LABEL ? $game : null;
    }
}