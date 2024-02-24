<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PlayerCardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use App\Repository\Game\Splendor\DrawCardsSPLRepository;
use App\Repository\Game\Splendor\NobleTileSPLRepository;
use App\Repository\Game\Splendor\PlayerCardSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Repository\Game\Splendor\TokenSPLRepository;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\RowSPL;
use App\Service\Game\AbstractGameManagerService;
use Doctrine\DBAL\Exception;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
class SPLService
{
    public static string $LABEL_JOKER = "yellow";
    public static int $MAX_POSSIBLE_COUNT_TOKENS = 10;
    public static int $MIN_COUNT_PLAYER = 2;
    public static int $MAX_COUNT_PLAYER = 4;
    public static int $COMES_OF_DRAW_CARD = 1;
    public static int $COMES_OF_ROWS = 2;
    public static int $MAX_COUNT_RESERVED_CARDS = 3;
    public static int $MAX_PRESTIGE_POINTS = 15;
    public static int $MIN_AVAILABLE_TOKENS = 4;

    public function __construct(private EntityManagerInterface $entityManager,
        private PlayerSPLRepository $playerSPLRepository,
        private TokenSPLRepository $tokenSPLRepository,
        private NobleTileSPLRepository $nobleTileSPLRepository,
        private DevelopmentCardsSPLRepository $developmentCardsSPLRepository,
        private PlayerCardSPLRepository $playerCardSPLRepository,
        private DrawCardsSPLRepository $drawCardsSPLRepository)
    { }

    /**
     * getCardFromDraw: return a random card from the draw or null if the draw is empty
     * @param DrawCardsSPL $draw
     * @return DevelopmentCardsSPL|null
     */
    public function getCardFromDraw(DrawCardsSPL $draw) : ?DevelopmentCardsSPL
    {
        $cards = $draw->getDevelopmentCards()->toArray();
        shuffle($cards);
        return sizeof($cards) == 0 ? null : $cards[0];
    }

    public function getDrawFromGameAndLevel(GameSPL $game, int $level)
    {
        return $this->drawCardsSPLRepository->findOneBy([
            'mainBoardSPL' => $game->getMainBoard()->getId(),
            'level' => $level
        ]);
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
     * @return array<PlayerCardSPL>
     */
    public function getReservedCards(PlayerSPL $player) : array
    {
        $personalBoard = $player->getPersonalBoard();

        return $this->playerCardSPLRepository->findBy([
            'personalBoardSPL' => $personalBoard->getId(),
            'isReserved' => true,
        ]);
    }

    /**
     * getPurchasedCards : get development cards purchased by player
     * @param PlayerSPL $player
     * @return array<PlayerCardSPL>
     */
    public function getPurchasedCards(PlayerSPL $player) : array
    {
        $personalBoard = $player->getPersonalBoard();

        return $this->playerCardSPLRepository->findBy([
            'personalBoardSPL' => $personalBoard->getId(),
            'isReserved' => false,
        ]);
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
     * getPlayerFromNameAndGame : return the player associated with a username and a game
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
        $firstPlayer = $game->getPlayers()->first();
        $firstPlayer->setTurnOfPlayer(true);
        $this->entityManager->persist($firstPlayer);
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
     * @throws Exception
     */
    public function reserveCard(PlayerSPL $player, DevelopmentCardsSPL $card) : void
    {
        if (!$this->canReserveCard($player, $card))
        {
            throw new Exception("You can't reserve cards");
        }

        $game = $player->getGameSPL();
        $mainBoard = $game->getMainBoard();
        $personalBoard = $player->getPersonalBoard();

        // Indication of the origin of the card
        //      COMES_OF_ROWS if the player reserve a card from main board
        //      COMES_OF_DRAW_CARD if the player reserve a card directly from the draw card
        $from = $this->whereIsThisCard($mainBoard, $card);
        if ($from == -1)
        {
            throw new Exception("An error has been received");
        }

        // Creation of player card
        $playerCard = new PlayerCardSPL($player, $card, true);
        $personalBoard->addPlayerCard($playerCard);
        $this->entityManager->persist($playerCard);
        $this->entityManager->persist($personalBoard);

        if ($from === SPLService::$COMES_OF_DRAW_CARD)
        {
            $this->manageDiscard($mainBoard, $card);
        } else {
            $this->manageRow($mainBoard, $card);
        }

        $this->manageJokerToken($player);

        $this->entityManager->flush();
    }

    /**
     * calculatePrestigePoints : calculate total points accumulated by a player
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
     * endRoundOfPlayer : ends player's round and gives it to next player
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
            $this->entityManager->persist($player);
        }
        $nextPlayer->setTurnOfPlayer(true);
        $this->entityManager->persist($nextPlayer);
        $this->entityManager->flush();
    }

    /**
     * getActivePlayer : returns the player who has to play
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
     * @param PlayerSPL $playerSPL
     * @param DevelopmentCardsSPL $developmentCardsSPL
     * @return void
     * @throws \Exception if it's not the card of the player
     */
    public function buyCard(PlayerSPL $playerSPL,
                            DevelopmentCardsSPL $developmentCardsSPL): void
    {
        $playerCardSPL = $this->getPlayerCardFromDevelopmentCard($playerSPL->getGameSPL(), $developmentCardsSPL);
        if ($playerCardSPL != null
            && $playerCardSPL->getPersonalBoardSPL()->getPlayerSPL()->getId() != $playerSPL->getId()
            && !$playerCardSPL->isIsReserved()) {
            throw new \Exception('Not the card of the player');
        }
        if($this->hasEnoughMoney($playerSPL, $developmentCardsSPL)){
            if ($playerCardSPL == null) {
                $playerCardSPL = new PlayerCardSPL($playerSPL, $developmentCardsSPL, false);
                $playerSPL->getPersonalBoard()->addPlayerCard($playerCardSPL);
                $this->entityManager->persist($playerCardSPL);
                $this->entityManager->persist($playerSPL->getPersonalBoard());
            }
            $this->retrievePlayerMoney($playerSPL, $developmentCardsSPL);
            if($playerCardSPL->isIsReserved()) {
                $playerCardSPL->setIsReserved(false);
                $this->entityManager->persist($playerCardSPL);
            } else {
                $mainBoard = $playerSPL->getGameSPL()->getMainBoard();
                $game = $playerSPL->getGameSPL();
                $row = $game->getMainBoard()->getRowsSPL()->get($developmentCardsSPL->getLevel() - 1);
                //Remove the bought card from row
                $row->removeDevelopmentCard($developmentCardsSPL);

                //Add a new card in the row
                $levelCard = $developmentCardsSPL->getLevel();
                $levelDraw = $mainBoard->getDrawCards()->get($levelCard - 1);
                $row->addDevelopmentCard($levelDraw->getDevelopmentCards()->first());
                $this->entityManager->persist($playerSPL->getGameSPL()->getMainBoard());

                //Remove the new card from draw
                $levelDraw->removeDevelopmentCard($developmentCardsSPL);
                $this->entityManager->persist($levelDraw);
                $this->entityManager->persist($row);
            }
            $this->entityManager->flush();
        } else {
            throw new Exception('Not enough money to buy this card');
        }
    }

    /**
     * addBuyableNobleTilesToPlayer: add the first noble tiles the player can afford to his stock
     * @param GameSPL $game
     * @param PlayerSPL $player
     * @return void
     */
    public function addBuyableNobleTilesToPlayer(GameSPL $game, PlayerSPL $player): void
    {
        $playerCards = $player->getPersonalBoard()->getPlayerCards();
        $filteredCards = $this->filterCardsByColor($playerCards);
        foreach ($game->getMainBoard()->getNobleTiles() as $tile) {
            $costs = $tile->getCardsCost();
            $canBuy = true;
            foreach ($costs as $cost) {
                $color = $cost->getColor();
                if ($cost->getPrice() > sizeof($filteredCards[$color])) {
                    $canBuy = false;
                }
            }
            if ($canBuy) {
                $player->getPersonalBoard()->addNobleTile($tile);
                $game->getMainBoard()->removeNobleTile($tile);
                $this->entityManager->persist($player->getPersonalBoard());
                $this->entityManager->persist($game->getMainBoard());
                $this->entityManager->flush();
            }
        }
    }

    /**
     * getDrawCardsByLevel : return a list of development cards from the draw indicated by its level
     * @param int $level
     * @param GameSPL $game
     * @return Collection<DevelopmentCardsSPL>
     */
    public function getDrawCardsByLevel(int $level, GameSPL $game) : Collection
    {
        return $game->getMainBoard()->getDrawCards()->get($level)->getDevelopmentCards();
    }

    /**
     * getPlayerCardFromDevelopmentCard : return the player's card with a game and a development card
     * @param GameSPL $game
     * @param DevelopmentCardsSPL $developmentCard
     * @return PlayerCardSPL|null
     */
    public function getPlayerCardFromDevelopmentCard(
        GameSPL $game,
        DevelopmentCardsSPL $developmentCard): ?PlayerCardSPL
    {
        foreach($game->getPlayers() as $player) {
            $temp = $this->playerCardSPLRepository->findOneBy([
                'personalBoardSPL' => $player->getPersonalBoard(),
                'developmentCard' => $developmentCard->getId(),
            ]);
            if($temp != null) {
                return $temp;
            }
        }
        return null;
    }

    /**
     * canPlayerReserveCard: return true if the player can reserve the cards
     * @param GameSPL $game
     * @param DevelopmentCardsSPL $card
     * @return bool
     */
    public function canPlayerReserveCard(GameSPL $game, DevelopmentCardsSPL $card): bool
    {
        return $this->getPlayerCardFromDevelopmentCard($game, $card) == null;
    }


    /**
     * filterCardsByColor: take an array of playerCards and filter it by color
     * @param Collection $playerCards
     * @return array<String, Collection<PlayerCardSPL>> an array associating color with the cards of the player
     */
    private function filterCardsByColor(Collection $playerCards): array {
        return [
            TokenSPL::$COLOR_BLUE => $this->getCardOfColor($playerCards, TokenSPL::$COLOR_BLUE),
            TokenSPL::$COLOR_BLACK => $this->getCardOfColor($playerCards, TokenSPL::$COLOR_BLACK),
            TokenSPL::$COLOR_GREEN => $this->getCardOfColor($playerCards, TokenSPL::$COLOR_GREEN),
            TokenSPL::$COLOR_RED => $this->getCardOfColor($playerCards, TokenSPL::$COLOR_RED),
            TokenSPL::$COLOR_WHITE => $this->getCardOfColor($playerCards, TokenSPL::$COLOR_WHITE),
        ];
    }

    /**
     * getCardOfColor: select all the $playerCards with the $color
     * @param Collection<DevelopmentCardsSPL> $playerCards
     * @param string $color
     * @return Collection<DevelopmentCardsSPL> the card of the selected color
     */
    private function getCardOfColor(Collection $playerCards, string $color): Collection
    {
        return $playerCards->filter(function (PlayerCardSPL $cards) use ($color) {
            return $cards->getDevelopmentCard()->getColor() == $color;
        });
    }

    private function canReserveCard(PlayerSPL $player, DevelopmentCardsSPL $card): bool
    {
        $playerCard = $this->getPlayerCardFromDevelopmentCard($player->getGameSPL(), $card);
        return !$this->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player)
            && $playerCard == null;
    }

    private function doesPlayerAlreadyHaveMaxNumberOfReservedCard(PlayerSPL $player): bool
    {
        $reservedCardsOfPlayer = $this->getReservedCards($player);
        return sizeof($reservedCardsOfPlayer)
            >= SPLService::$MAX_COUNT_RESERVED_CARDS;
    }


    private function checkInRowAtLevel(MainBoardSPL $mainBoard
        , DevelopmentCardsSPL $card) : bool
    {
        $level = $card->getLevel() - 1;
        $rowAtLevel = $mainBoard->getRowsSPL()->get($level);
        return $this->isCardInRow($rowAtLevel, $card);
    }

    private function isCardInRow(RowSPL $row, DevelopmentCardsSPL $card): bool
    {
        return $row->getDevelopmentCards()->contains($card);
    }

    private function checkInDiscardAtLevel(MainBoardSPL $mainBoard
        , DevelopmentCardsSPL $card) : bool
    {
        $level = $card->getLevel() -1;
        $discardsAtLevel = $mainBoard->getDrawCards()->get($level);
        $testCard = $discardsAtLevel->getDevelopmentCards()->last();
        return $card === $testCard;
    }

    private function manageRow(MainBoardSPL $mainBoard, DevelopmentCardsSPL $card): void
    {
        $level = $card->getLevel() - 1;
        $row = $mainBoard->getRowsSPL()->get($level);
        $row->removeDevelopmentCard($card);

        // get first cards of discards associate at level
        $discardsOfLevel = $mainBoard->getDrawCards()->get($level);
        $cardsInDiscard = $discardsOfLevel->getDevelopmentCards();
        $numberOfCard = $cardsInDiscard->count();
        if ($numberOfCard > 0)
        {
            $discard = $cardsInDiscard->get($numberOfCard - 1);
            $row->addDevelopmentCard($discard);
            $discardsOfLevel->removeDevelopmentCard($discard);
            $this->entityManager->persist($discardsOfLevel);
        }
        $this->entityManager->persist($row);
    }

    private function manageDiscard(MainBoardSPL $mainBoard, DevelopmentCardsSPL $card): void
    {
        $level = $card->getLevel();
        $discardsAtLevel = $mainBoard->getDrawCards()->get($level - 1);
        $discardsAtLevel->removeDevelopmentCard($card);
        $this->entityManager->persist($discardsAtLevel);
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
                $this->entityManager->persist($personalBoard);
                $this->entityManager->persist($mainBoard);
            }
        }
    }

    private function getNumberOfTokenAtColorAtMainBoard(MainBoardSPL $mainBoard, string $color) : int
    {
        $tokens = $mainBoard->getTokens();
        return $this->getNumberOfTokenAtColor($tokens, $color);
    }

    public function getNumberOfTokenAtColor(Collection $tokens, string $color) : int
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
        $level = $card->getLevel() - 1;
        $row = $mainBoard->getRowsSPL()->get($level);
        if ($row->getDevelopmentCards()->contains($card))
        {
            return SPLService::$COMES_OF_ROWS;
        }

        $discards = $mainBoard->getDrawCards()->get($level);
        if ($discards->getDevelopmentCards()->contains($card))
        {
            return SPLService::$COMES_OF_DRAW_CARD;
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
        $money = $this->initializeColorTab();
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
        $price = $this->initializeColorTab();
        $cardCosts = $developmentCardsSPL->getCardCost();
        foreach ($cardCosts as $cardCost){
            $costColor = $cardCost->getColor();
            $price[$costColor] += $cardCost->getPrice();
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

        $cardPrice = $this->computeCardPrice($developmentCardsSPL);

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

    private function initializeColorTab():array
    {
        $array[TokenSPL::$COLOR_YELLOW] = 0;
        $array[TokenSPL::$COLOR_RED] = 0;
        $array[TokenSPL::$COLOR_BLUE] = 0;
        $array[TokenSPL::$COLOR_BLACK] = 0;
        $array[TokenSPL::$COLOR_GREEN] = 0;
        $array[TokenSPL::$COLOR_WHITE] = 0;
        return $array;
    }
}