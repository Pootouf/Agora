<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\PlayerCardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\MainBoardSPLRepository;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use App\Repository\Game\Splendor\NobleTileSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Repository\Game\Splendor\TokenSPLRepository;
use App\Service\Game\AbstractGameManagerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Nullable;
use function PHPUnit\Framework\throwException;
class SPLService
{
    public static string $LABEL_JOKER = "yellow";
    public static int $MAX_POSSIBLE_COUNT_TOKENS = 10;
    public static int $MIN_COUNT_PLAYER = 2;
    public static int $MAX_COUNT_PLAYER = 4;
    public static int $COMES_OF_THE_DISCARDS = 1;
    public static int $COMES_OF_THE_ROWS = 2;
    public static int $MAX_COUNT_RESERVED_CARDS = 3;
    public static int $MAX_PRESTIGE_POINTS = 15;
    public static int $MIN_AVAILABLE_TOKENS = 4;

    public function __construct(private EntityManagerInterface $entityManager,
        private PlayerSPLRepository $playerSPLRepository,
        private MainBoardSPLRepository $mainBoardSPLRepository,
        private TokenSPLRepository $tokenSPLRepository,
        private NobleTileSPLRepository $nobleTileSPLRepository,
        private DevelopmentCardsSPLRepository $developmentCardsSPLRepository)
    { }

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
     * reserveCards : a player reserve a development card
     * @param PlayerSPL $player
     * @param DevelopmentCardsSPL $card
     * @return void
     */
    public function reserveCards(PlayerSPL $player, DevelopmentCardsSPL $card) : void
    {
        // Check can reserve
        if (!$this->canReserveCards($player, $card))
        {
            throwException(new \Exception("You can't reserve cards"));
        }

        $game = $player->getGameSPL();

        // Boards
        $mainBoard = $game->getMainBoard();
        $personalBoard = $player->getPersonalBoard();

        // From ?
        $from = $this->whereIsThisCard($mainBoard, $card);
        if ($from == -1)
        {
            throwException(new \Exception("An error has been received"));
        }

        // Reserve now => Manage cards
        $playerCard = new PlayerCardSPL($player, $card, true);
        $personalBoard->addPlayerCard($playerCard);

        // Manage cards
        // => I know that my card is in main board; so i remove this card from row or draw card
        if ($from === SPLService::$COMES_OF_THE_DISCARDS)
        {
           $this->manageDiscard($mainBoard, $card);
        } else {
            $this->manageRow($mainBoard, $card);
        }

       // Manage token
       $this->manageJokerToken($player);
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
     * getReserveCards : get development cards reserved by player
     * @param PlayerSPL $player
     * @return Collection<int, DevelopmentCardsSPL>
     */
    public function getReserveCards(PlayerSPL $player) : Collection
    {
        $personalBoard = $player->getPersonalBoard();
        $cardsOfPlayer = $personalBoard->getPlayerCards();

        return $this->getCards($cardsOfPlayer, true);
    }

    /**
     * getPurchasedCards : get development cards purchased by player
     * @param PlayerSPL $player
     * @return Collection<int, DevelopmentCardsSPL>
     */
    public function getPurchasedCards(PlayerSPL $player) : Collection
    {
        $personalBoard = $player->getPersonalBoard();
        $cardsOfPlayer = $personalBoard->getPlayerCards();

        return $this->getCards($cardsOfPlayer, false);
    }

    //TODO : METHOD TO SET TURN TO NEXT PLAYER AND ENSURE EVERY OTHER TURN IS FALSE

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

    private function canReserveCards(PlayerSPL $player, DevelopmentCardsSPL $card): Boolean
    {
        if (!$this->checkCanReserveSidesPlayer($player, $card) ||
            !$this->checkCanReserveSideMainBoard($player, $card) )
        {
            return false;
        }

        return true;
    }

    private function checkCanReserveSidesPlayer(PlayerSPL $player, DevelopmentCardsSPL $card): Boolean
    {
        // Method that checks the number of reserved cards

        $reservedCardsOfPlayer = $this->getReserveCards($player);
        if ($reservedCardsOfPlayer->count()
            == SPLService::$MAX_COUNT_RESERVED_CARDS)
        {
            return false;
        }

        // Method that check this card doesn't already at player

        if ($reservedCardsOfPlayer->contains($card))
        {
            return false;
        }

        return true;
    }

    private function checkCanReserveSideMainBoard(PlayerSPL $player, DevelopmentCardsSPL $card): Boolean
    {
        // check if exist in main board

        $mainBoard = $player->getGameSPL()->getMainBoard();

        return $this->checkInRowAtLevel($mainBoard, $card)
            || $this->checkInDiscardAtLevel($mainBoard, $card);
    }

    private function checkInRowAtLevel(MainBoardSPL $mainBoard
        , DevelopmentCardsSPL $card) : Boolean
    {
        $level = $card->getLevel();
        $rowAtLevel = $mainBoard->getRowsSPL()->get($level - 1);
        return $rowAtLevel->getDevelopmentCards()->contains($card);
    }

    private function checkInDiscardAtLevel(MainBoardSPL $mainBoard
        , DevelopmentCardsSPL $card) : Boolean
    {
        $level = $card->getLevel();
        $discardsAtLevel = $mainBoard->getDrawCards()->get( $level - 1);
        return $discardsAtLevel->getDevelopmentCards()->contains($card);
    }

    private function getCards(Collection $cards, bool $reserved) : Collection
    {
        $searchCards = new ArrayCollection();
        for ($i = 0; $i < $cards->count(); $i++)
        {
            $card = $cards->get($i);
            if ($card->isReserved() == $reserved)
            {
                $searchCards->add($card);
            }
        }
        return $searchCards;
    }

    private function manageRow(MainBoardSPL $mainBoard, DevelopmentCardsSPL $card): void
    {
        $level = $card->getLevel();
        $row = $mainBoard->getRowsSPL()->get($level - 1);
        $row->removeDevelopmentCard($card);

        // get first cards of discards associate at level
        $discardsOfLevel = $mainBoard->getDrawCards()->get($level - 1);
        $cardsInDiscard = $discardsOfLevel->getDevelopmentCards();
        $lastCard = $cardsInDiscard->count();
        if ($lastCard > 0)
        {
            $discard = $cardsInDiscard->get($lastCard - 1);
            $row->addDevelopmentCard($discard);
            $discardsOfLevel->removeDevelopmentCard($discard);
        }
    }

    private function manageDiscard(MainBoardSPL $mainBoard, DevelopmentCardsSPL $card): void
    {
        $level = $card->getLevel();
        $discardsAtLevel = $mainBoard->getDrawCards()->get($level - 1);
        $discardsAtLevel->removeDevelopmentCard($card);
    }

    private function manageJokerToken(PlayerSPL $player): void
    {
        $personalBoard = $player->getPersonalBoard();
        $tokens = $personalBoard->getTokens();
        if ($tokens->count() < SPLService::$MAX_POSSIBLE_COUNT_TOKENS)
        {
            $game = $player->getGameSPL();
            $mainBoard = $game->getMainBoard();
            if ($this->getNumberOfTokenAtColorAtMainBoard($mainBoard,
                SPLService::$LABEL_JOKER) > 0)
            {
                $joker = $this->getJokerToken($mainBoard);
                $personalBoard->addToken($joker);
                $mainBoard->removeToken($joker);
            }
        }
    }

    private function getNumberOfTokenAtColorAtMainBoard(MainBoardSPL $mainBoard, string $color) : int
    {
        $tokens = $mainBoard->getTokens();
        return $this->getNumberOfTokenAtColor($tokens, $color);
    }

    private function getNumberOfTokenAtColor(Collection $tokens, string $color) : int
    {
        $count = 0;
        for ($i = 0; $i < $tokens->count(); $i++)
        {
            $token = $tokens->get($i);
            if ($token->getColor() === $color)
            {
                $count += 1;
            }
        }
        return $count;
    }

    private function getNumberOfTokenAtColorAtPlayer(PlayerSPL $player, string $color) : int
    {
        $personalBoard = $player->getPersonalBoard();
        $tokens = $personalBoard->getTokens();
        return $this->getNumberOfTokenAtColor($tokens, $color);
    }

    private function whereIsThisCard(MainBoardSPL $mainBoard, DevelopmentCardsSPL $card) : int
    {
        $level = $card->getLevel();
        $row = $mainBoard->getRowsSPL()->get($level - 1);
        if ($row->getDevelopmentCards()->contains($card))
        {
            return SPLService::$COMES_OF_THE_ROWS;
        }

        $discards = $mainBoard->getDrawCards()->get($level - 1);
        if ($discards->getDevelopmentCards()->contains($card))
        {
            return SPLService::$COMES_OF_THE_DISCARDS;
        }

        return -1;
    }

    private function getJokerToken(MainBoardSPL $mainBoard) : TokenSPL
    {
        $token = null;
        $tokens = $mainBoard->getTokens();
        for ($i = 0; $i < $tokens->count(); $i++)
        {
            $token = $tokens->get($i);
            if ($token->getColor() === SPLService::$LABEL_JOKER)
            {
                break;
            }
        }
        return $token;
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
        $selectedTokens = $playerSPL->getPersonalBoard()->getTokens();
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