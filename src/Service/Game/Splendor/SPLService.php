<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\DTO\Player;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PlayerCardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use App\Repository\Game\Splendor\NobleTileSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Repository\Game\Splendor\TokenSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use Doctrine\ORM\EntityManagerInterface;

class SPLService
{
    public static int $MAX_PRESTIGE_POINTS = 15;
    public static int $MIN_AVAILABLE_TOKENS = 4;
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
        $this->playerSPLRepository = $playerSPLRepository;
        $this->tokenSPLRepository = $tokenSPLRepository;
        $this->nobleTileSPLRepository = $nobleTileSPLRepository;
        $this->developmentCardsSPLRepository = $developmentCardsSPLRepository;
    }

    /**
     * initializeNewGame: initialize a new Splendor game
     *
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
            ['level' => DevelopmentCardsSPL::$LEVEL_ONE]
        );
        $levelTwoCards = $this->developmentCardsSPLRepository->findBy(
            ['level' => DevelopmentCardsSPL::$LEVEL_TWO]
        );
        $levelThreeCards = $this->developmentCardsSPLRepository->findBy(
            ['level' => DevelopmentCardsSPL::$LEVEL_THREE]
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
     * isGameEnded : checks if a game must end or not
     *
     * @param GameSPL $game
     * @return bool
     */
    public function isGameEnded(GameSPL $game): bool
    {

        return $this->hasOnePlayerReachedLimit($game)
            && $this->hasLastPlayerPlayed($game);
    }

    /**
     * hasOnePlayerReachedLimit : checks if one player reached prestige points limit
     *
     * @param GameSPL $game
     * @return bool
     */
    public function hasOnePlayerReachedLimit(GameSPL $game): bool
    {
        foreach ($game->getPlayers() as $player) {
            if ($this->getPrestigePoints($player) >= SPLService::$MAX_PRESTIGE_POINTS) {
                return true;
            }
        }
        return false;
    }

    /**
     * getPrestigePoints : returns total prestige points of a player
     *
     * @param PlayerSPL $player
     * @return int
     */
    private function getPrestigePoints(PlayerSPL $player): int
    {
        return $player->getTotalPoints();
    }

    /**
     * calculatePrestigePoints : calculate total points accumulated by a player
     *
     * @param PlayerSPL $player
     */
    public function calculatePrestigePoints(PlayerSPL $player): void
    {
        $total = 0;
        $nobleTiles = $player->getPersonalBoard()->getNobleTiles();
        $developCards = $player->getPersonalBoard()->getPlayerCards();
        foreach ($nobleTiles as $tile) {
            $total += $tile->getPrestigePoints();
        }
        foreach ($developCards as $card) {
            $total += $card->getDevelopmentCard()->getPrestigePoints();
        }
        $player->setTotalPoints($total);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * hasLastPlayerPlayed : checks if the player who last played was the last player
     *
     * @param GameSPL $game
     * @return bool
     */
    private function hasLastPlayerPlayed(GameSPL $game): bool
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
     * @param GameSPL $game
     * @param string  $name
     * @return ?PlayerSPL
     */
    public function getPlayerFromNameAndGame(GameSPL $game, string $name): ?PlayerSPL
    {
        return $this->playerSPLRepository->findOneBy(['gameSPL' => $game->getId(), 'username' => $name]);
    }

    /**
     * getRanking : returns a sorted array of player
     *
     * @param GameSPL $gameSPL
     * @return array
     */
    public function getRanking(GameSPL $gameSPL): array
    {
        $array = $gameSPL->getPlayers()->toArray();
        usort($array,
            function(PlayerSPL $player1, PlayerSPL $player2) {
                return $this->getPrestigePoints($player2) - $this->getPrestigePoints($player1);
            });
        return $array;
    }

    /**
     * endRoundOfPlayer : ends player's round and gives it to next player
     *
     * @param PlayerSPL $playerSPL
     * @return void
     */
    public function endRoundOfPlayer(GameSPL $gameSPL, PlayerSPL $playerSPL): void
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
     *
     * @param GameSPL $gameSPL
     * @return PlayerSPL
     */
    public function getActivePlayer(GameSPL $gameSPL): PlayerSPL
    {
        return $this->playerSPLRepository->findOneBy(["gameSPL" => $gameSPL->getId(),
            "turnOfPlayer" => true]);
    }

    /**
     * buyCard : check if player can buy a card and remove the card from the main board
     *
     * @param GameSPL $gameSPL
     * @param PlayerSPL $playerSPL
     * @param DevelopmentCardsSPL $developmentCardsSPL
     * @return void
     */
    public function buyCard(GameSPL $gameSPL, PlayerSPL $playerSPL,
                            DevelopmentCardsSPL $developmentCardsSPL): void
    {
        if($this->hasEnoughMoney($playerSPL, $developmentCardsSPL)){
            $playerCard = new PlayerCardSPL();
            $playerCard->setDevelopmentCard($developmentCardsSPL);
            $playerSPL->getPersonalBoard()->addPlayerCard($playerCard);
            $this->retrievePlayerMoney($playerSPL, $developmentCardsSPL);

        }
    }

    /**
     * @param Game $game
     * @return ?GameSPL
     */
    private function getGameSplFromGame(Game $game): ?GameSpl
    {
        /** @var GameSpl $game */
        return $game->getGameName() == AbstractGameManagerService::$SPL_LABEL ? $game : null;
    }

    /**
     * hasEnoughMoney : check if player has enough money to buy the card
     * @param PlayerSPL $playerSPL
     * @param DevelopmentCardsSPL $developmentCardSPL
     * @return bool
     */
    private function hasEnoughMoney(PlayerSPL $playerSPL, DevelopmentCardsSPL $developmentCardSPL): bool
    {
        $playerMoney = $this->computePlayerMoney($playerSPL);
        $cardPrice = $this->computeCardPrice($developmentCardSPL);

        $difference = 0;
        foreach ($cardPrice as $color => $amount){
            if($playerMoney[$color] < $amount){
                $difference += 1;
            }
        }
        if($playerMoney[TokenSPL::$COLOR_YELLOW] >= $difference){
            return true;
        }
        return false;
    }

    /**
     * computePlayerMoney : calculate player money from his cards and his tokens
     * @param PlayerSPL $playerSPL
     * @return array
     */
    private function computePlayerMoney(PlayerSPL $playerSPL): array
    {
        $money = array();
        $playerCards = $playerSPL->getPersonalBoard()->getPlayerCards();
        foreach ($playerCards as $playerCard){
            $cardColor = $playerCard->getDevelopmentCard()->getColor();
            $money[$cardColor] += 1;
        }
        $playerTokens = $playerSPL->getPersonalBoard()->getTokens();
        foreach ($playerTokens as $playerToken){
            $tokenColor = $playerToken->getColor();
            $money[$tokenColor] += 1;
        }
        return $money;
    }

    /**
     * computeCardPrice : calculate the price of a card and put it in an array
     * @param DevelopmentCardsSPL $developmentCardsSPL
     * @return array
     */
    private function computeCardPrice(DevelopmentCardsSPL $developmentCardsSPL): array
    {
        $price = array();
        $cardCosts = $developmentCardsSPL->getCardCost();
        foreach ($cardCosts as $cardCost){
            $costColor = $cardCost->getColor();
            $price[$costColor] += 1;
        }
        return $price;
    }

    /**
     * retrievePlayerMoney : remove tokens from the player to buy a card
     * @param PlayerSPL $playerSPL
     * @param DevelopmentCardsSPL $developmentCardsSPL
     * @return void
     */
    private function retrievePlayerMoney(PlayerSPL $playerSPL, DevelopmentCardsSPL $developmentCardsSPL): void
    {
        $playerMoney = $this->computePlayerMoney($playerSPL);
        $cardPrice = $this->computeCardPrice($developmentCardsSPL);

        // compute token number to retrieve
        $playerCards = $playerSPL->getPersonalBoard()->getPlayerCards();
        foreach ($playerCards as $playerCard){
            $cardColor = $playerCard->getDevelopmentCard()->getColor();
            $playerMoney[$cardColor] -= 1;
        }

        // remove non gold token
        foreach ($cardPrice as $color => $amount){
            $tokens = $playerSPL->getPersonalBoard()->getTokens();
            foreach ($tokens as $token){
                if($token->getColor() == $color && $amount > 0){
                    $playerSPL->getPersonalBoard()->removeToken($token);
                    $amount -= 1;
                }
            }
        }

        // remove gold token if it is needed
        foreach ($cardPrice as $amount){
            if($amount > 0){
                $tokens = $playerSPL->getPersonalBoard()->getTokens();
                foreach ($tokens as $token){
                    if($token->getColor() == TokenSPL::$COLOR_YELLOW && $amount > 0){
                        $playerSPL->getPersonalBoard()->removeToken($token);
                        $amount -= 1;
                    }
                }
            }
        }
    }
}