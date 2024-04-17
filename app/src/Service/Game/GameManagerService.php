<?php

namespace App\Service\Game;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Player;
use App\Entity\Game\GameUser;
use App\Repository\Game\Glenmore\GameGLMRepository;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Myrmes\GameMYRRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Service\Game\Glenmore\GLMGameManagerService;
use App\Service\Game\Myrmes\MYRGameManagerService;
use App\Service\Game\SixQP\SixQPGameManagerService;
use App\Service\Game\Splendor\SPLGameManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class GameManagerService
{
    private array $gameManagerServices;

    public function __construct(private GameSixQPRepository $gameSixQPRepository,
                                private GameSPLRepository $gameSPLRepository,
                                private GameGLMRepository $gameGLMRepository,
                                private GameMYRRepository $gameMYRRepository,
                                private PlayerSixQPRepository $playerSixQPRepository,
                                private PlayerSPLRepository $playerSPLRepository,
                                private PlayerGLMRepository $playerGLMRepository,
                                private PlayerMYRRepository $playerMYRRepository,
                                SixQPGameManagerService $sixQPGameManagerService,
                                SPLGameManagerService $SPLGameManagerService,
                                GLMGameManagerService $GLMGameManagerService,
                                MYRGameManagerService $MYRGameManagerService,
                                private readonly EntityManagerInterface $entityManager
    )
    {
        $this->gameManagerServices[AbstractGameManagerService::$SIXQP_LABEL] = $sixQPGameManagerService;
        $this->gameManagerServices[AbstractGameManagerService::$SPL_LABEL] = $SPLGameManagerService;
        $this->gameManagerServices[AbstractGameManagerService::$GLM_LABEL] = $GLMGameManagerService;
        $this->gameManagerServices[AbstractGameManagerService::$MYR_LABEL] = $MYRGameManagerService;
    }

    public function createGame(string $gameName): int {
        return $this->gameManagerServices[$gameName]->createGame();
    }

    public function joinGame(int $gameId, GameUser $user): int {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return AbstractGameManagerService::$ERROR_INVALID_GAME;
        }
        return $this->gameManagerServices[$game->getGameName()]->createPlayer($user->getUsername(), $game);
    }

    public function quitGame(int $gameId, GameUser $user): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return AbstractGameManagerService::$ERROR_INVALID_GAME;
        }
        if ($game->isLaunched()) {
            return AbstractGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        return $this->gameManagerServices[$game->getGameName()]->deletePlayer($user->getUsername(), $game);
    }

    public function deleteGame(int $gameId): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return AbstractGameManagerService::$ERROR_INVALID_GAME;
        }
        return $this->gameManagerServices[$game->getGameName()]->deleteGame($game);
    }

    public function launchGame(int $gameId): int
    {
        $game = $this->getGameFromId($gameId);
        if ($game == null) {
            return AbstractGameManagerService::$ERROR_INVALID_GAME;
        }
        return $this->gameManagerServices[$game->getGameName()]->launchGame($game);
    }

    /**
     * getGameFromId : enable to get a game object by an id depending on the game
     * @param int $gameId
     * @return Game|null
     */
    public function getGameFromId(int $gameId): ?Game {
        $game = $this->gameSixQPRepository->findOneBy(['id' => $gameId]);
        if ($game == null) {
            $game = $this->gameSPLRepository->findOneBy(['id' => $gameId]);
        }
        if ($game == null) {
            $game = $this->gameGLMRepository->findOneBy(['id' => $gameId]);
        }
        if ($game == null) {
            $game = $this->gameMYRRepository->findOneBy(['id' => $gameId]);
        }
        return $game;
    }

    /**
     * @param int $playerId
     * @return Player|null
     */
    public function getPlayerFromId(int $playerId): ?Player {
        $player = $this->playerSixQPRepository->findOneBy(['id' => $playerId]);
        if ($player == null) {
            $player = $this->playerSPLRepository->findOneBy(['id' => $playerId]);
        }
        if ($player == null) {
            $player = $this->playerGLMRepository->findOneBy(['id' => $playerId]);
        }
        if ($player == null) {
            $player = $this->playerMYRRepository->findOneBy(['id' => $playerId]);
        }
        return $player;
    }

    /**
     * getExcludedPlayerFromGameId: return the excluded player from the game if it exists
     * @param int $gameId
     * @return Player|null
     */
    public function getExcludedPlayerFromGameId(int $gameId): ?Player {
        $player = $this->playerSixQPRepository->findOneBy(['game' => $gameId, 'excluded' => true]);
        if ($player == null) {
            $player = $this->playerSPLRepository->findOneBy(['game' => $gameId, 'excluded' => true]);
        }
        if ($player == null) {
            $player = $this->playerGLMRepository->findOneBy(['gameGLM' => $gameId, 'excluded' => true]);
        }
        if ($player == null) {
            $player = $this->playerMYRRepository->findOneBy(['gameMYR' => $gameId, 'excluded' => true]);
        }
        return $player;
    }

    /**
     * excludePlayer : exclude the player from the game, by removing its username and set user excluded
     * @param Player $player
     * @param Game $game
     * @return void
     */
    public function excludePlayer(Player $player, Game $game): void
    {
        $player->setExcluded(true);
        $player->setUsername(null);
        $game->setPaused(true);
        $this->entityManager->persist($player);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * replacePlayer : replace a previously excluded player by a new user, by changing the player username
     *                  and re-launch the game
     * @param Player $player
     * @param string $username
     * @param Game $game
     * @return void
     */
    public function replacePlayer(Player $player, string $username, Game $game): void
    {
        $player->setExcluded(false);
        $player->setUsername($username);
        $game->setPaused(false);
        $this->entityManager->persist($player);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }
}