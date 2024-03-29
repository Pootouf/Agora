<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\Glenmore\DrawTilesGLM;
use App\Entity\Game\Glenmore\GameGLM;
use App\Entity\Game\Glenmore\GlenmoreParameters;
use App\Entity\Game\Glenmore\MainBoardGLM;
use App\Entity\Game\Glenmore\PawnGLM;
use App\Entity\Game\Glenmore\PersonalBoardGLM;
use App\Entity\Game\Glenmore\PlayerGLM;
use App\Entity\Game\Glenmore\WarehouseGLM;
use App\Entity\Game\Myrmes\GameMYR;
use App\Entity\Game\Myrmes\MainBoardMYR;
use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\PersonalBoardMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Entity\Game\Myrmes\SeasonMYR;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use App\Repository\Game\Myrmes\PlayerMYRRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\Glenmore\GLMService;
use App\Service\Game\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;


/**
 * @codeCoverageIgnore
 */
class MYRGameManagerService extends AbstractGameManagerService
{

    public function __construct(private EntityManagerInterface $entityManager,
        private PlayerMYRRepository $playerMYRRepository,
        private MYRService $MYRService,
        private LogService $logService)
    {}


    /**
     * createGame : create a Myrmes game
     */
    public function createGame(): int
    {
        $game = new GameMYR();
        $game->setGameName(AbstractGameManagerService::$MYR_LABEL);
        $mainBoard = new MainBoardMYR();
        $mainBoard->setYearNum(0);
        $season = new SeasonMYR();
        $season->setName(MyrmesParameters::$INVALID_SEASON_NAME);
        $season->setDiceResult(-1);
        $this->entityManager->persist($season);
        $mainBoard->setActualSeason($season);
        $game->setMainBoardMYR($mainBoard);
        $this->entityManager->persist($game);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->flush();
        $this->logService->sendSystemLog($game, "la partie " . $game->getId() . " a été créée");
        return $game->getId();
    }

    /**
     * createPlayer : create a Myrmes player and save him in the database
     */
    public function createPlayer(string $playerName, Game $game): int
    {
        $game = $this->getGameMyrmesFromGame($game);
        if ($game == null) {
            return MYRGameManagerService::$ERROR_INVALID_GAME;
        }
        if($game->isLaunched()) {
            return MYRGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        if (count($game->getPlayers()) >= MyrmesParameters::$MAX_NUMBER_OF_PLAYER) {
            return MYRGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER;
        }
        if ($this->playerMYRRepository->findOneBy(
            ['username' => $playerName, 'gameMYR' => $game->getId()]) != null) {
            return MYRGameManagerService::$ERROR_ALREADY_IN_PARTY;
        }
        $player = new PlayerMYR($playerName, $game);
        $player->setScore(0);
        $player->setGoalLevel(0);
        $player->setColor("");
        $player->setRemainingHarvestingBonus(0);
        $player->setPhase(MyrmesParameters::$PHASE_EVENT);
        $personalBoard = new PersonalBoardMYR();
        $personalBoard->setAnthillLevel(0);
        $personalBoard->setLarvaCount(0);
        $personalBoard->setWarriorsCount(0);
        $personalBoard->setSelectedEventLarvaeAmount(0);
        $personalBoard->setBonus(0);
        $player->setPersonalBoardMYR($personalBoard);
        $game->addPlayer($player);
        $this->entityManager->persist($player);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
        $this->logService->sendPlayerLog($game, $player,
            $playerName . " a rejoint la partie " . $game->getId());
        return MYRGameManagerService::$SUCCESS;
    }

    /**
     * deletePlayer : delete a Myrmes player
     */
    public function deletePlayer(string $playerName, Game $game): int
    {
        $game = $this->getGameMyrmesFromGame($game);
        if ($game == null) {
            return MYRGameManagerService::$ERROR_INVALID_GAME;
        }
        if ($game->isLaunched()) {
            return MYRGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        $player = $this->MYRService->getPlayerFromNameAndGame($game, $playerName);
        if ($player == null) {
            return MYRGameManagerService::$ERROR_PLAYER_NOT_FOUND;
        }
        $this->entityManager->remove($player);
        $this->entityManager->flush();
        $this->logService->sendSystemLog($game,
            $playerName . " a été retiré de la partie " . $game->getId());
        return MYRGameManagerService::$SUCCESS;
    }

    /**
     * deleteGame : delete a Myrmes game
     */
    public function deleteGame(Game $game): int
    {
        $game = $this->getGameMyrmesFromGame($game);
        if ($game == null) {
            return MYRGameManagerService::$ERROR_INVALID_GAME;
        }
        foreach ($game->getPlayers() as $player) {
            $this->entityManager->remove($player->getPersonalBoardMYR());
            $this->entityManager->remove($player);
        }
        $this->entityManager->remove($game->getMainBoardMYR());
        $this->logService->sendSystemLog($game, "la partie " . $game->getId() . " a pris fin");
        $this->entityManager->remove($game);
        $this->entityManager->flush();
        return MYRGameManagerService::$SUCCESS;
    }

    /**
     * launchGame : launch a Myrmes game
     * @throws Exception if game invalid
     */
    public function launchGame(Game $game): int
    {
        $game = $this->getGameMyrmesFromGame($game);
        if ($game == null) {
            return MYRGameManagerService::$ERROR_INVALID_GAME;
        }
        $numberOfPlayers = count($game->getPlayers());
        if ($numberOfPlayers > MyrmesParameters::$MAX_NUMBER_OF_PLAYER
            || $numberOfPlayers < MyrmesParameters::$MIN_NUMBER_OF_PLAYER) {
            return MYRGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER;
        }
        if ($game->isLaunched()) {
            return MYRGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        $game->setLaunched(true);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $this->MYRService->initializeNewGame($game);
        $this->logService->sendSystemLog($game, "la partie " . $game->getId() . " a commencé");
        return MYRGameManagerService::$SUCCESS;
    }


    private function getGameMyrmesFromGame(Game $game): ?GameMYR
    {
        /** @var GameMYR $game */
        return $game->getGameName() == AbstractGameManagerService::$MYR_LABEL ? $game : null;
    }
}