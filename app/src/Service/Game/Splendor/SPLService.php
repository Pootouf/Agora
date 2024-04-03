<?php

namespace App\Service\Game\Splendor;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use App\Entity\Game\Splendor\DrawCardsSPL;
use App\Entity\Game\Splendor\GameSPL;
use App\Entity\Game\Splendor\PlayerCardSPL;
use App\Entity\Game\Splendor\PlayerSPL;
use App\Entity\Game\Splendor\SplendorParameters;
use App\Entity\Game\Splendor\TokenSPL;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use App\Repository\Game\Splendor\DrawCardsSPLRepository;
use App\Repository\Game\Splendor\NobleTileSPLRepository;
use App\Repository\Game\Splendor\PlayerCardSPLRepository;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use App\Repository\Game\Splendor\RowSPLRepository;
use App\Repository\Game\Splendor\TokenSPLRepository;
use App\Entity\Game\Splendor\MainBoardSPL;
use App\Entity\Game\Splendor\RowSPL;
use App\Service\Game\AbstractGameManagerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SPLService
{

    public function __construct(private EntityManagerInterface $entityManager,
        private PlayerSPLRepository $playerSPLRepository,
        private RowSPLRepository $rowSPLRepository,
        private NobleTileSPLRepository $nobleTileSPLRepository,
        private DevelopmentCardsSPLRepository $developmentCardsSPLRepository,
        private PlayerCardSPLRepository $playerCardSPLRepository,
        private DrawCardsSPLRepository $drawCardsSPLRepository)
    { }

    /**
     * getRowsFromGame : returns all rows from the game
     * @param GameSPL $game
     * @return array<RowSPL>
     */
    public function getRowsFromGame(GameSPL $game): array
    {
        return $this->rowSPLRepository->findBy(['mainBoardSPL' => $game->getMainBoard()->getId()]);
    }

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

    /**
     * getDrawFromGameAndLevel : returns a draw from a game and a level of draw tiles
     * @param GameSPL $game
     * @param int     $level
     * @return ?DrawCardsSPL
     */
    public function getDrawFromGameAndLevel(GameSPL $game, int $level) : ?DrawCardsSPL
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
            if ($this->getPrestigePoints($player) >= SplendorParameters::$MAX_PRESTIGE_POINTS) {
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
     * @return array<PlayerSPL>
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
     * getDrawCardsByLevel : return a list of development cards from the draw indicated by its level
     * @param int $level
     * @param GameSPL $game
     * @return Collection<Int, DevelopmentCardsSPL>
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
     * getPurchasableCardsOnBoard : return a list of purchasable development cards for the player on the main board
     * @param GameSPL $gameSPL
     * @param PlayerSPL $playerSPL
     * @return ArrayCollection<Int, DevelopmentCardsSPL>
     */
    public function getPurchasableCardsOnBoard(GameSPL $gameSPL, PlayerSPL $playerSPL): ArrayCollection
    {
        $purchasableCards = new ArrayCollection();
        foreach($gameSPL->getMainBoard()->getRowsSPL() as $row) {
            foreach($row->getDevelopmentCards() as $card) {
                if($this->hasEnoughMoney($playerSPL, $card)) {
                    $purchasableCards->add($card);
                }
            }
        }
        return $purchasableCards;
    }

    /**
     * getPurchasableCardsOnPersonalBoard : return a list of purchasable development cards for the player
     *      between its reserved cards.
     * @param PlayerSPL $playerSPL
     * @return ArrayCollection<Int, DevelopmentCardsSPL>
     */
    public function getPurchasableCardsOnPersonalBoard(PlayerSPL $playerSPL): ArrayCollection
    {
        $purchasableCards = new ArrayCollection();
        $reservedCards = $this->getReservedCards($playerSPL);
        foreach($reservedCards as $card) {
            if($this->hasEnoughMoney($playerSPL, $card->getDevelopmentCard())) {
                $purchasableCards->add($card->getDevelopmentCard());
            }
        }
        return $purchasableCards;
    }

    /**
     * doesPlayerAlreadyHaveMaxNumberOfReservedCard : indicate if the player can reserve a card or not
     * @param PlayerSPL $player
     * @return bool
     */
    public function doesPlayerAlreadyHaveMaxNumberOfReservedCard(PlayerSPL $player): bool
    {
        $reservedCardsOfPlayer = $this->getReservedCards($player);
        return sizeof($reservedCardsOfPlayer)
            >= SplendorParameters::$MAX_COUNT_RESERVED_CARDS;
    }

    /**
     * getNumberOfTokenAtColor : return the number of tokens of the selected color
     * @param Collection<Int, TokenSPL> $tokens
     * @param string $color
     * @return int
     */
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

    /**
     * initializeNewGame: initialize a new Splendor game
     * @param GameSPL $game
     * @return void
     */
    public function initializeNewGame(GameSPL $game): void
    {
        $mainBoard = $game->getMainBoard();
        $nobleTiles = $this->nobleTileSPLRepository->findAll();
        shuffle($nobleTiles);
        for ($i = 0; $i < $game->getPlayers()->count() + 1; $i++) {
            $mainBoard->addNobleTile($nobleTiles[$i]);
        }
        $levelOneCards = $this->developmentCardsSPLRepository->findBy(
            ['level' => SplendorParameters::$DEVELOPMENT_CARD_LEVEL_ONE]
        );
        $levelTwoCards = $this->developmentCardsSPLRepository->findBy(
            ['level' => SplendorParameters::$DEVELOPMENT_CARD_LEVEL_TWO]
        );
        $levelThreeCards = $this->developmentCardsSPLRepository->findBy(
            ['level' => SplendorParameters::$DEVELOPMENT_CARD_LEVEL_THREE]
        );
        shuffle($levelOneCards);
        shuffle($levelTwoCards);
        shuffle($levelThreeCards);
        $rows = $mainBoard->getRowsSPL();
        for ($i = 0; $i < 4; $i++) {
            $rows[SplendorParameters::$DRAW_CARD_LEVEL_ONE]->addDevelopmentCard($levelOneCards[$i]);
            $rows[SplendorParameters::$DRAW_CARD_LEVEL_TWO]->addDevelopmentCard($levelTwoCards[$i]);
            $rows[SplendorParameters::$DRAW_CARD_LEVEL_THREE]->addDevelopmentCard($levelThreeCards[$i]);
        }
        array_splice($levelOneCards, 0, 4);
        array_splice($levelTwoCards, 0, 4);
        array_splice($levelThreeCards, 0, 4);
        $this->entityManager->persist($rows[SplendorParameters::$DRAW_CARD_LEVEL_ONE]);
        $this->entityManager->persist($rows[SplendorParameters::$DRAW_CARD_LEVEL_TWO]);
        $this->entityManager->persist($rows[SplendorParameters::$DRAW_CARD_LEVEL_THREE]);
        $drawCardLevelOne = $mainBoard->getDrawCards()->get(SplendorParameters::$DRAW_CARD_LEVEL_ONE);
        $drawCardLevelTwo = $mainBoard->getDrawCards()->get(SplendorParameters::$DRAW_CARD_LEVEL_TWO);
        $drawCardLevelThree = $mainBoard->getDrawCards()->get(SplendorParameters::$DRAW_CARD_LEVEL_THREE);
        foreach ($levelOneCards as $card) {
            $drawCardLevelOne->addDevelopmentCard($card);
        }
        foreach ($levelTwoCards as $card) {
            $drawCardLevelTwo->addDevelopmentCard($card);
        }
        foreach ($levelThreeCards as $card) {
            $drawCardLevelThree->addDevelopmentCard($card);
        }
        foreach ($game->getPlayers() as $player) {
            $player->setTurnOfPlayer(false);
            $this->entityManager->persist($player);
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
     * reserveCard : a player reserve a development card
     * @param PlayerSPL $player
     * @param DevelopmentCardsSPL $card
     * @return array<boolean>
     * @throws Exception
     */
    public function reserveCard(PlayerSPL $player, DevelopmentCardsSPL $card) : array
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
        $cardFromDraw = null;
        if ($from === SplendorParameters::$COMES_OF_THE_DISCARDS)
        {
            $this->manageDiscard($mainBoard, $card);
        } else {
            $cardFromDraw = $this->manageRow($mainBoard, $card);
        }
        $returnedData = array(
            "isJokerTaken" => $this->manageJokerToken($player),
            "cardFromDraw" => $cardFromDraw,
        );

        $this->entityManager->flush();
        return $returnedData;
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
            if(!$card->isIsReserved()) {
                $total += $card->getDevelopmentCard()->getPrestigePoints();
            }
        }
        $player->setTotalPoints($total);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * endRoundOfPlayer : ends player's round and gives it to next player
     * @param GameSPL $gameSPL
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
     * buyCard : check if player can buy a card and remove the card from the main board
     * @param PlayerSPL $playerSPL
     * @param DevelopmentCardsSPL $developmentCardsSPL
     * @return array<String, ?DevelopmentCardsSPL> y retrievePlayerMoney => money removed from the player,
     *               newDevCard => the card added on the main board
     * @throws \Exception if it's not the card of the player
     */
    public function buyCard(PlayerSPL $playerSPL,
                            DevelopmentCardsSPL $developmentCardsSPL): array
    {
        $playerCardSPL = $this->getPlayerCardFromDevelopmentCard($playerSPL->getGameSPL(), $developmentCardsSPL);
        if ($playerCardSPL != null
            && $playerCardSPL->getPersonalBoardSPL()->getPlayerSPL()->getId() != $playerSPL->getId()
        ) {
            throw new \Exception('Not the card of the player');
        }
        if ($playerSPL->getPersonalBoard()->getPlayerCards()->contains($playerCardSPL)
        && !$playerCardSPL->isIsReserved()) {
            throw new \Exception('Player already bought this card');
        }
        if($this->hasEnoughMoney($playerSPL, $developmentCardsSPL)){
            if ($playerCardSPL == null) {
                $playerCardSPL = new PlayerCardSPL($playerSPL, $developmentCardsSPL, false);
                $playerSPL->getPersonalBoard()->addPlayerCard($playerCardSPL);
                $this->entityManager->persist($playerCardSPL);
                $this->entityManager->persist($playerSPL->getPersonalBoard());
            }

            $retrievePlayerMoney = $this->retrievePlayerMoney($playerSPL, $developmentCardsSPL);

            $newDevCardInRow = null;
            if($playerCardSPL->isIsReserved()) {
                $playerCardSPL->setIsReserved(false);
                $this->entityManager->persist($playerCardSPL);
            } else {
                $mainBoard = $playerSPL->getGameSPL()->getMainBoard();
                $game = $playerSPL->getGameSPL();
                $row = $game->getMainBoard()->getRowsSPL()->get($developmentCardsSPL->getLevel() - 1);
                //Remove the bought card from row
                $row->removeDevelopmentCard($developmentCardsSPL);
                $this->entityManager->persist($row);

                //Add a new card in the row
                $levelCard = $developmentCardsSPL->getLevel();
                $levelDraw = $mainBoard->getDrawCards()->get($levelCard - 1);

                if ($levelDraw->getDevelopmentCards()->count() > 0) {
                    $devCards = $levelDraw->getDevelopmentCards()->toArray();
                    shuffle($devCards);
                    $newDevCardInRow = $devCards[0];
                    $row->addDevelopmentCard($newDevCardInRow);
                    //Remove the new card from draw
                    $levelDraw->removeDevelopmentCard($newDevCardInRow);
                    $this->entityManager->persist($levelDraw);
                }
                $this->entityManager->persist($row);
            }

            $this->calculatePrestigePoints($playerSPL);
            return array(
                "retrievePlayerMoney" => $retrievePlayerMoney,
                "newDevCard" => $newDevCardInRow,
            );
        } else {
            throw new Exception('Not enough money to buy this card');
        }
    }

    /**
     * addBuyableNobleTilesToPlayer: add the first noble tiles the player can afford to his stock
     * @param GameSPL $game
     * @param PlayerSPL $player
     * @return int the id of the bought noble tile, -1 if no noble tile bought
     */
    public function addBuyableNobleTilesToPlayer(GameSPL $game, PlayerSPL $player): int
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
                $this->calculatePrestigePoints($player);
                return $tile->getId();
            }
        }
        return -1;
    }

    /**
     * filterCardsByColor: take an array of playerCards and filter it by color
     * @param Collection<Int, PlayerCardSPL> $playerCards
     * @return array<String, Collection<Int, PlayerCardSPL>> an array associating color with the cards of the player
     */
    private function filterCardsByColor(Collection $playerCards): array {
        return [
            SplendorParameters::$COLOR_BLUE => $this->getCardOfColor($playerCards, SplendorParameters::$COLOR_BLUE),
            SplendorParameters::$COLOR_BLACK => $this->getCardOfColor($playerCards, SplendorParameters::$COLOR_BLACK),
            SplendorParameters::$COLOR_GREEN => $this->getCardOfColor($playerCards, SplendorParameters::$COLOR_GREEN),
            SplendorParameters::$COLOR_RED => $this->getCardOfColor($playerCards, SplendorParameters::$COLOR_RED),
            SplendorParameters::$COLOR_WHITE => $this->getCardOfColor($playerCards, SplendorParameters::$COLOR_WHITE),
        ];
    }

    /**
     * getCardOfColor: select all the $playerCards with the $color
     * @param Collection<Int, PlayerCardSPL> $playerCards
     * @param string $color
     * @return Collection<Int, PlayerCardSPL> the card of the selected color
     */
    private function getCardOfColor(Collection $playerCards, string $color): Collection
    {
        return $playerCards->filter(function (PlayerCardSPL $cards) use ($color) {
            return $cards->getDevelopmentCard()->getColor() == $color;
        });
    }

    /**
     * canReserveCard : checks if a player can reserve a card
     * @param PlayerSPL           $player
     * @param DevelopmentCardsSPL $card
     * @return bool
     */
    private function canReserveCard(PlayerSPL $player, DevelopmentCardsSPL $card): bool
    {
        $playerCard = $this->getPlayerCardFromDevelopmentCard($player->getGameSPL(), $card);
        return !$this->doesPlayerAlreadyHaveMaxNumberOfReservedCard($player)
            && $playerCard == null;
    }

    /**
     * manageRow : puts a new card from the row on the main board
     * @param MainBoardSPL        $mainBoard
     * @param DevelopmentCardsSPL $card
     * @return DevelopmentCardsSPL
     */
    private function manageRow(MainBoardSPL $mainBoard, DevelopmentCardsSPL $card): DevelopmentCardsSPL
    {
        $level = $card->getLevel() - 1;
        $row = $mainBoard->getRowsSPL()->get($level);
        $row->removeDevelopmentCard($card);

        // get first cards of discards associate at level
        $discardsOfLevel = $mainBoard->getDrawCards()->get($level);
        $cardsInDiscard = $discardsOfLevel->getDevelopmentCards();
        $numberOfCard = $cardsInDiscard->count();
        $discard = null;
        if ($numberOfCard > 0)
        {
            $discard = $cardsInDiscard->get($numberOfCard - 1);
            $row->addDevelopmentCard($discard);
            $discardsOfLevel->removeDevelopmentCard($discard);
            $this->entityManager->persist($discardsOfLevel);
        }
        $this->entityManager->persist($row);
        return $discard;
    }

    /**
     * manageDiscard : puts a card from the discard on the main board
     * @param MainBoardSPL        $mainBoard
     * @param DevelopmentCardsSPL $card
     * @return void
     */
    private function manageDiscard(MainBoardSPL $mainBoard, DevelopmentCardsSPL $card): void
    {
        $level = $card->getLevel();
        $discardsAtLevel = $mainBoard->getDrawCards()->get($level - 1);
        $discardsAtLevel->removeDevelopmentCard($card);
        $this->entityManager->persist($discardsAtLevel);
    }

    /**
     * manageJokerToken : gives a joker token to the player, if possible
     * @param PlayerSPL $player
     * @return bool
     */
    private function manageJokerToken(PlayerSPL $player): bool
    {
        $personalBoard = $player->getPersonalBoard();
        $tokens = $personalBoard->getTokens();
        if ($tokens->count() < SplendorParameters::$PLAYER_MAX_TOKEN)
        {
            $game = $player->getGameSPL();
            $mainBoard = $game->getMainBoard();
            if ($this->getNumberOfTokenAtColorAtMainBoard($mainBoard,
                SplendorParameters::$LABEL_JOKER) > 0)
            {
                $joker = $this->getJokerToken($mainBoard);
                $personalBoard->addToken($joker);
                $mainBoard->removeToken($joker);
                $this->entityManager->persist($personalBoard);
                $this->entityManager->persist($mainBoard);
                return true;
            }
        }
        return false;
    }

    /**
     * getNumberOfTokenAtColorAtMainBoard : returns all tokens from a color available on the main board
     * @param MainBoardSPL $mainBoard
     * @param string       $color
     * @return int
     */
    private function getNumberOfTokenAtColorAtMainBoard(MainBoardSPL $mainBoard, string $color) : int
    {
        $tokens = $mainBoard->getTokens();
        return $this->getNumberOfTokenAtColor($tokens, $color);
    }


    /**
     * whereIsThisCard : indicates where the card comes from
     * @param MainBoardSPL        $mainBoard
     * @param DevelopmentCardsSPL $card
     * @return int
     */
    private function whereIsThisCard(MainBoardSPL $mainBoard, DevelopmentCardsSPL $card) : int
    {
        $level = $card->getLevel() - 1;
        $row = $mainBoard->getRowsSPL()->get($level);
        if ($row->getDevelopmentCards()->contains($card))
        {
            return SplendorParameters::$COMES_OF_THE_ROWS;
        }

        $discards = $mainBoard->getDrawCards()->get($level);
        if ($discards->getDevelopmentCards()->contains($card))
        {
            return SplendorParameters::$COMES_OF_THE_DISCARDS;
        }

        return -1;
    }

    /**
     * getJokerToken : returns a joker token
     * @param MainBoardSPL $mainBoard
     * @return TokenSPL
     */
    private function getJokerToken(MainBoardSPL $mainBoard) : TokenSPL
    {
        $token = null;
        $tokens = $mainBoard->getTokens();
        for ($i = 0; $i < $tokens->count(); $i++)
        {
            $token = $tokens->get($i);
            if ($token->getColor() === SplendorParameters::$LABEL_JOKER)
            {
                break;
            }
        }
        return $token;
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
                $difference += ($amount - $playerMoney[$color]);
            }
        }
        if($playerMoney[SplendorParameters::$COLOR_YELLOW] >= $difference){
            return true;
        }
        return false;
    }

    /**
     * computePlayerMoney : calculate player money from his cards and his tokens
     * @param PlayerSPL $playerSPL
     * @return array<String, Int>
     */
    private function computePlayerMoney(PlayerSPL $playerSPL): array
    {
        $money = $this->initializeColorTab();
        $playerCards = $playerSPL->getPersonalBoard()->getPlayerCards();
        foreach ($playerCards as $playerCard){
            if(!$playerCard->isIsReserved()) {
                $cardColor = $playerCard->getDevelopmentCard()->getColor();
                $money[$cardColor] += 1;
            }
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
     * @return array<String, Int>
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
     * @return array<string> the type of the removed tokens
     */
    private function retrievePlayerMoney(PlayerSPL $playerSPL, DevelopmentCardsSPL $developmentCardsSPL): array
    {

        $cardPrice = $this->computeCardPrice($developmentCardsSPL);

        // retrieve to the price the amount of card of same type
        foreach ($cardPrice as $color => $amount) {
            $playerCards = $playerSPL->getPersonalBoard()->getPlayerCards();
            foreach ($playerCards as $playerCard) {
                if($playerCard->getDevelopmentCard()->getColor() == $color && $amount > 0) {
                    $amount -= 1;
                }
            }
        }

        $selectedTokensForAnimation = [];

        // remove non gold token
        foreach ($cardPrice as $color => $amount){
            $tokens = $playerSPL->getPersonalBoard()->getTokens();
            foreach ($tokens as $token){
                if($token->getColor() == $color && $token->getColor() != SplendorParameters::$COLOR_YELLOW
                    && $amount > 0){
                    $playerSPL->getPersonalBoard()->removeToken($token);
                    $amount -= 1;
                    $playerSPL->getGameSPL()->getMainBoard()->addToken($token);
                    $selectedTokensForAnimation[] = $token->getType();
                }
            }
        }

        // remove gold token if it is needed
        foreach ($cardPrice as $amount){
            if($amount > 0){
                $tokens = $playerSPL->getPersonalBoard()->getTokens();
                foreach ($tokens as $token){
                    if($token->getColor() == SplendorParameters::$COLOR_YELLOW && $amount > 0){
                        $playerSPL->getPersonalBoard()->removeToken($token);
                        $amount -= 1;
                        $playerSPL->getGameSPL()->getMainBoard()->addToken($token);
                        $selectedTokensForAnimation[] = $token->getType();
                    }
                }
            }
        }
        return $selectedTokensForAnimation;
    }

    /**
     * initializeColorTab : initializes all tokens with amount 0
     * @return array<String, int>
     */
    private function initializeColorTab():array
    {
        $array[SplendorParameters::$COLOR_YELLOW] = 0;
        $array[SplendorParameters::$COLOR_RED] = 0;
        $array[SplendorParameters::$COLOR_BLUE] = 0;
        $array[SplendorParameters::$COLOR_BLACK] = 0;
        $array[SplendorParameters::$COLOR_GREEN] = 0;
        $array[SplendorParameters::$COLOR_WHITE] = 0;
        return $array;
    }
}