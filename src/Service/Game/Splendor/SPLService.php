<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use function PHPUnit\Framework\throwException;

class SPLService
{
    public static int $MAX_PRESTIGE_POINTS = 15;
    private EntityManagerInterface $entityManager;
    private PlayerSPLRepository $playerSPLRepository;

    public function __construct(EntityManagerInterface $entityManager, PlayerSPLRepository $playerSPLRepository)
    {
        $this->entityManager = $entityManager;
        $this->playerSPLRepository =  $playerSPLRepository;
    }

    /**
     * takeToken : player takes a token from the mainBoard
     * @param PlayerSPL $playerSPL
     * @param TokenSPL $tokenSPL
     * @return void
     */
    public function takeToken(PlayerSPL $playerSPL, TokenSPL $tokenSPL) : void
    {
        if($playerSPL->getPersonalBoard()->getTokens()->count() >= 10){
            throwException(new Exception("Can't pick up more tokens"));
        }
        $tokensPickable = $this->canChooseTwoTokens($playerSPL, $tokenSPL);
        if($tokensPickable == -1){
            throwException(new Exception("An error as occurred"));
        }
        $playerSPL->getPersonalBoard()->addSelectedToken($tokenSPL);
        if($tokensPickable == 1){
            $selectedTokens = $playerSPL->getPersonalBoard()->getSelectedTokens();
            foreach ($selectedTokens as $selectedToken){
                $playerSPL->getPersonalBoard()->addToken($selectedToken);
            }
            // TODO : SET PLAYER TURN TO FALSE
        }
    }

    /**
     * isGameEnded : checks if a game must end or not
     * @param GameSPL $game
     * @return bool
     */
    public function isGameEnded(GameSPL $game) : bool
    {

       return $this->hasOnePlayerReachedLimit($game)
           && $this->hasLastPlayerPlayed($game);
    }

    //TODO : METHOD TO SET TURN TO NEXT PLAYER AND ENSURE EVERY OTHER TURN IS FALSE

    /**
     * @param GameSPL $game
     * @param string $name
     * @return ?PlayerSPL
     */
    public function getPlayerFromNameAndGame(GameSPL $game, string $name) : ?PlayerSPL
    {
        return $this->playerSPLRepository->findOneBy(['game' => $game->getId(), 'username' => $name]);
    }

    /**
     * @param Game $game
     * @return ?GameSPL
     */
    private function getGameSplFromGame(Game $game): ?GameSpl {
        /** @var GameSpl $game */
        return $game->getGameName() == AbstractGameManagerService::$SPL_LABEL ? $game : null;
    }

    /**
     * hasLastPlayerPlayed : checks if the player who last played was the last player
     * @param GameSPL $game
     * @return bool
     */
    private function hasLastPlayerPlayed(GameSPL $game) : bool
    {
        $players = $game->getPlayers();
        $lastPlayer = $players->last();
        $result = $lastPlayer->isTurnOfPlayer();
        if ($result == null) {
            return false;
        }
        return $lastPlayer->isTurnOfPlayer();
    }

    /**
     * hasOnePlayerReachedLimit : checks if one player reached prestige points limit
     * @param GameSPL $game
     * @return bool
     */
    public function hasOnePlayerReachedLimit(GameSPL $game) : bool
    {
        foreach($game->getPlayers() as $player) {
            if($this->getPrestigePoints($player) >= SPLService::$MAX_PRESTIGE_POINTS) {
                return true;
            }
        }
        return false;
    }

    /**
     * getRanking : returns a sorted array of player
     * @param GameSPL $gameSPL
     * @return array
     */
    public function getRanking(GameSPL $gameSPL): array
    {
        $array = $gameSPL->getPlayers()->toArray();
        usort($array,
            function (PlayerSPL $player1, PlayerSPL $player2) {
                return $this->getPrestigePoints($player2) - $this->getPrestigePoints($player1);
            });
        return $array;
    }

    /**
     * getPrestigePoints : returns total prestige points of a player
     * @param PlayerSPL $player
     * @return int
     */
    private function getPrestigePoints(PlayerSPL $player) : int
    {
        $total = 0;
        $nobleTiles = $player->getPersonalBoard()->getNobleTiles();
        $developCards = $player->getPersonalBoard()->getPlayerCards();
        foreach($nobleTiles as $tile) {
            $total += $tile->getPrestigePoints();
        }
        foreach($developCards as $card) {
            $total += $card->getDevelopmentCard()->getPrestigePoints();
        }
        return $total;
    }

    /**
     * canChooseTwoTokens : permet de vérifier si un joueur peut prendre le jeton $tokenSPL
     * @param PlayerSPL $playerSPL
     * @param TokenSPL $tokenSPL
     * @return int
     *          0 Si la pile est vide
     *          1 Si on prends un jeton de même couleur que
     *              celui dans notre pile
     *          -1 Si on ne peut pas prendre le jeton
     */
    private function canChooseTwoTokens(PlayerSPL $playerSPL, TokenSPL $tokenSPL): int
    {
        $selectedTokens = $playerSPL->getPersonalBoard()->getSelectedTokens();
        if($selectedTokens->count() == 0){
            return 0;
        }
        if($selectedTokens->count() == 1 && $selectedTokens->first()->getColor() != $tokenSPL->getColor()){
            return 1;
        }
        return $this->canChooseThreeTokens($playerSPL, $tokenSPL);
    }

    /**
     * @param PlayerSPL $playerSPL
     * @param TokenSPL $tokenSPL
     * @return int
     *          0 Si la pile est vide ou que la pile est de taille 1 et que
     *              le jeton dedans est de couleur différente
     *          1 Si on prends un jeton de couleur différente de
     *              ceux déjà présent dans la pile
     *          -1 Si on ne peut pas prendre le jeton
     */
    private function canChooseThreeTokens(PlayerSPL $playerSPL, TokenSPL $tokenSPL): int
    {
        $selectedTokens = $playerSPL->getPersonalBoard()->getSelectedTokens();
        if($selectedTokens->count() == 0){
            return 0;
        }
        if($selectedTokens->count() == 1 && $selectedTokens->first()->getColor() != $tokenSPL->getColor()){
            return 0;
        }
        if($selectedTokens->count() == 2 && $selectedTokens->first()->getColor() != $tokenSPL->getColor()
          && $selectedTokens[1]->getColor() != $tokenSPL->getColor()){
            return 1;
        }
        return -1;
    }
}