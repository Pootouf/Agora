<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\GameTranslation;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\RowSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\LogService;
use App\Service\Game\SixQP\SixQPService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class SPLGameManagerService extends AbstractGameManagerService
{

    public function __construct(private EntityManagerInterface $entityManager,
        private SPLService $splService,
        private PlayerSPLRepository $playerSPLRepository,
        private LogService $logService,
        private TokenSPLService $tokenSPLService)
    {}


    /**
     * createGame : create a Splendor game
     */
    public function createGame(): int
    {
        $game = new GameSPL();
        $game->setGameName(AbstractGameManagerService::$SPL_LABEL);
        $mainBoard = new MainBoardSPL();
        $game->setMainBoard($mainBoard);
        for ($i = 1; $i <= SplendorParameters::$NUMBER_OF_ROWS_BY_GAME; $i++) {
            $row = new RowSPL();
            $row->setLevel($i);
            $mainBoard->addRowsSPL($row);
            $this->entityManager->persist($row);
            $drawCard = new DrawCardsSPL();
            $drawCard->setLevel($i);
            $mainBoard->addDrawCard($drawCard);
            $this->entityManager->persist($drawCard);
        }
        $this->entityManager->persist($mainBoard);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $this->logService->sendSystemLog($game, GameTranslation::GAME_STRING
            . $game->getId() . " a été créée");
        return $game->getId();
    }

    /**
     * createPlayer : create a Splendor player and save him in the database
     */
    public function createPlayer(string $playerName, Game $game): int
    {
        $game = $this->getGameSplendorFromGame($game);
        if ($game == null) {
            return SPLGameManagerService::$ERROR_INVALID_GAME;
        }
        if($game->isLaunched()) {
            return SPLGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        if (count($game->getPlayers()) >= SplendorParameters::$MAX_NUMBER_OF_PLAYER) {
            return SPLGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER;
        }
        if ($this->playerSPLRepository->findOneBy(
            ['username' => $playerName, 'gameSPL' => $game->getId()]) != null) {
            return SPLGameManagerService::$ERROR_ALREADY_IN_PARTY;
        }
        $player = new PlayerSPL($playerName, $game);
        $personalBoard = new PersonalBoardSPL();
        $player->setGameSPL($game);
        $player->setPersonalBoard($personalBoard);
        $personalBoard->setPlayerSPL($player);
        $player->setPersonalBoard($personalBoard);
        $game->addPlayer($player);
        $this->entityManager->persist($player);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
        $this->logService->sendPlayerLog($game, $player,
            $playerName . " a rejoint la partie " . $game->getId());
        return SPLGameManagerService::$SUCCESS;
    }

    /**
     * deletePlayer : delete a Splendor player
     */
    public function deletePlayer(string $playerName, Game $game): int
    {
        $game = $this->getGameSplendorFromGame($game);
        if ($game == null) {
            return SPLGameManagerService::$ERROR_INVALID_GAME;
        }
        if ($game->isLaunched()) {
            return SPLGameManagerService::$ERROR_GAME_ALREADY_LAUNCHED;
        }
        $player = $this->splService->getPlayerFromNameAndGame($game, $playerName);
        if ($player == null) {
            return SPLGameManagerService::$ERROR_PLAYER_NOT_FOUND;
        }
        $this->entityManager->remove($player);
        $this->entityManager->flush();
        $this->logService->sendSystemLog($game,
            $playerName . " a été retiré de la partie " . $game->getId());
        return SPLGameManagerService::$SUCCESS;
    }

    /**
     * deleteGame : delete a Splendor game
     */
    public function deleteGame(Game $game): int
    {
        $game = $this->getGameSplendorFromGame($game);
        if ($game == null) {
            return SPLGameManagerService::$ERROR_INVALID_GAME;
        }
        foreach ($game->getPlayers() as $player) {
            $this->entityManager->remove($player);
        }
        $this->logService->sendSystemLog($game, GameTranslation::GAME_STRING . $game->getId() . " s'est terminée");
        $this->entityManager->remove($game);
        $this->entityManager->flush();
        return SPLGameManagerService::$SUCCESS;
    }

    /**
     * launchGame : launch a Splendor game
     * @throws Exception if game invalid
     */
    public function launchGame(Game $game): int
    {
        $game = $this->getGameSplendorFromGame($game);
        if ($game == null) {
            return SPLGameManagerService::$ERROR_INVALID_GAME;
        }
        $numberOfPlayers = count($game->getPlayers());
        if ($numberOfPlayers > SplendorParameters::$MAX_NUMBER_OF_PLAYER
            || $numberOfPlayers < SplendorParameters::$MIN_NUMBER_OF_PLAYER) {
            return SPLGameManagerService::$ERROR_INVALID_NUMBER_OF_PLAYER;
        }
        $game->setLaunched(true);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $this->splService->initializeNewGame($game);
        $this->tokenSPLService->initializeGameToken($game);
        $this->logService->sendSystemLog($game, GameTranslation::GAME_STRING . $game->getId() . " a débuté");
        return SPLGameManagerService::$SUCCESS;
    }


    private function getGameSplendorFromGame(Game $game): ?GameSPL {
        /** @var GameSPL $game */
        return $game->getGameName() == AbstractGameManagerService::$SPL_LABEL ? $game : null;
    }
}
