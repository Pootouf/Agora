<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PersonalBoardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use App\Repository\Game\Splendor\GameSPLRepository;
use App\Repository\Game\Splendor\NobleTileSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Repository\Game\Splendor\TokenSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use function PHPUnit\Framework\throwException;

class SPLService
{
    public static int $MAX_PRESTIGE_POINTS = 15;
    private EntityManagerInterface $entityManager;
    private PlayerSPLRepository $playerSPLRepository;
    private TokenSPLRepository $tokenSPLRepository;
    private NobleTileSPLRepository $nobleTileSPLRepository;
    private DevelopmentCardsSPLRepository $developmentCardsSPLRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                PlayerSPLRepository $playerSPLRepository,
                                TokenSPLRepository $tokenSPLRepository,
                                NobleTileSPLRepository $nobleTileSPLRepository,
                                DevelopmentCardsSPLRepository $developmentCardsSPLRepository)
    {
        $this->entityManager = $entityManager;
        $this->playerSPLRepository =  $playerSPLRepository;
        $this->tokenSPLRepository = $tokenSPLRepository;
        $this->nobleTileSPLRepository = $nobleTileSPLRepository;
        $this->developmentCardsSPLRepository = $developmentCardsSPLRepository;
    }

    /**
     * initializeNewGame: initialize a new Splendor game
     * @param GameSPL $game
     * @return void
     */
    public function initializeNewGame(GameSPL $game): void
    {
        $mainBoard = $game->getMainBoard();
        $tokens = $this->tokenSPLRepository->findAll();
        foreach ($tokens as $token) {
            $mainBoard->addToken($token);
        }
        $nobleTiles = $this->nobleTileSPLRepository->findAll();
        shuffle($nobleTiles);
        for ($i = 0; $i < $game->getPlayers()->count() + 1; $i++) {
            $mainBoard->addNobleTile($nobleTiles[$i]);
        }
        $levelOneCards = $this->developmentCardsSPLRepository->findBy(
            ['color' => DevelopmentCardsSPL::$LEVEL_ONE_COLOR]
        );
        $levelTwoCards = $this->developmentCardsSPLRepository->findBy(
            ['color' => DevelopmentCardsSPL::$LEVEL_TWO_COLOR]
        );
        $levelThreeCards = $this->developmentCardsSPLRepository->findBy(
            ['color' => DevelopmentCardsSPL::$LEVEL_THREE_COLOR]
        );
        shuffle($levelOneCards);
        shuffle($levelTwoCards);
        shuffle($levelThreeCards);
        $rows = $mainBoard->getRowsSPL();
        for ($i = 0; $i < 4; $i++) {
            $rows[DrawCardsSPL::$LEVEL_ONE]->addDevelopmentCard($levelOneCards[$i]);
            $rows[DrawCardsSPL::$LEVEL_TWO]->addDevelopmentCard($levelTwoCards[$i]);
            $rows[DrawCardsSPL::$LEVEL_THREE]->addDevelopmentCard($levelThreeCards[$i]);
        }
        array_splice($levelOneCards, 0, 4);
        array_splice($levelTwoCards, 0, 4);
        array_splice($levelThreeCards, 0, 4);
        $this->entityManager->persist($rows[DrawCardsSPL::$LEVEL_ONE]);
        $this->entityManager->persist($rows[DrawCardsSPL::$LEVEL_TWO]);
        $this->entityManager->persist($rows[DrawCardsSPL::$LEVEL_THREE]);
        $drawCardLevelOne = $mainBoard->getDrawCards()->get(DrawCardsSPL::$LEVEL_ONE);
        $drawCardLevelTwo = $mainBoard->getDrawCards()->get(DrawCardsSPL::$LEVEL_TWO);
        $drawCardLevelThree = $mainBoard->getDrawCards()->get(DrawCardsSPL::$LEVEL_THREE);
        foreach ($levelOneCards as $card) {
            $drawCardLevelOne->addDevelopmentCard($card);
        }
        foreach ($levelTwoCards as $card) {
            $drawCardLevelTwo->addDevelopmentCard($card);
        }
        foreach ($levelThreeCards as $card) {
            $drawCardLevelThree->addDevelopmentCard($card);
        }
        $this->entityManager->persist($drawCardLevelOne);
        $this->entityManager->persist($drawCardLevelTwo);
        $this->entityManager->persist($drawCardLevelThree);
        $this->entityManager->persist($mainBoard);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
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
            throw new Exception("Can't pick up more tokens");
        }
        $tokensPickable = $this->canChooseTwoTokens($playerSPL, $tokenSPL);
        if($tokensPickable == -1){
            throw new Exception("An error as occurred");
        }
        $playerSPL->getPersonalBoard()->addSelectedToken($tokenSPL);
        if($tokensPickable == 1){
            $selectedTokens = $playerSPL->getPersonalBoard()->getSelectedTokens();
            foreach ($selectedTokens as $selectedToken){
                $playerSPL->getPersonalBoard()->addToken($selectedToken);
            }
            $game = $playerSPL->getGameSPL();
            $this->endRoundOfPlayer($game, $playerSPL);
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
     * endRoundOfPlayer : ends player's round and gives it to next player
     * @param PlayerSPL $playerSPL
     * @return void
     */
    public function endRoundOfPlayer(GameSPL $gameSPL, PlayerSPL $playerSPL) : void
    {
        $players = $gameSPL->getPlayers();
        $nbOfPlayers = $players->count();
        $index = $players->indexOf($playerSPL);
        $nextPlayer = $players->get(($index + 1) % $nbOfPlayers);
        foreach ($players as $player) {
            $player->setTurnOfPlayer(false);
        }
        $nextPlayer->setTurnOfPlayer(true);
    }

    /**
     * getActivePlayer : returns the player who has to play
     * @param GameSPL $gameSPL
     * @return PlayerSPL
     */
    public function getActivePlayer(GameSPL $gameSPL) : PlayerSPL
    {
        return $this->playerSPLRepository->findOneBy(["gameSPL" => $gameSPL->getId(),
        "turnOfPlayer" => true]);
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
          && $selectedTokens[1]->getColor() != $tokenSPL->getColor()
          && $selectedTokens->first()->getColor() != $selectedTokens[1]->getColor()){
            return 1;
        }
        return -1;
    }
}