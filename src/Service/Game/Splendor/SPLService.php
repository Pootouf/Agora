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
use App\Repository\Game\Splendor\GameSPLRepository;
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
    public static int $MAX_COUNT_RESERVED_CARDS = 3;
    public static int $MAX_PRESTIGE_POINTS = 15;
    private EntityManagerInterface $entityManager;
    private PlayerSPLRepository $playerSPLRepository;
    private TokenSPLRepository $tokenSPLRepository;
    private NobleTileSPLRepository $nobleTileSPLRepository;
    private DevelopmentCardsSPLRepository $developmentCardsSPLRepository;

    private MainBoardSPLRepository $mainBoardSPLRepository;

    public function __construct(EntityManagerInterface $entityManager,
        PlayerSPLRepository $playerSPLRepository,
        MainBoardSPLRepository $mainBoardSPLRepository,
                                TokenSPLRepository $tokenSPLRepository,
                                NobleTileSPLRepository $nobleTileSPLRepository,
                                DevelopmentCardsSPLRepository $developmentCardsSPLRepository)
    {
        $this->entityManager = $entityManager;
        $this->playerSPLRepository =  $playerSPLRepository;
        $this->mainBoardSPLRepository = $mainBoardSPLRepository;
        $this->tokenSPLRepository = $tokenSPLRepository;
        $this->nobleTileSPLRepository = $nobleTileSPLRepository;
        $this->developmentCardsSPLRepository = $developmentCardsSPLRepository;
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
        $playerSPL->getPersonalBoard()->addToken($tokenSPL);
        if($tokensPickable == 1){
            $selectedTokens = $playerSPL->getPersonalBoard()->getTokens();
            foreach ($selectedTokens as $selectedToken){
                $playerSPL->getPersonalBoard()->addToken($selectedToken);
            }
            $game = $playerSPL->getGameSPL();
            $this->endRoundOfPlayer($game, $playerSPL);
        }
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

        // Boards
        $game = $player->getGameSPL();
        $mainBoard = $this->mainBoardSPLRepository->findOneBy(
            ["game" => $game->getId()]);
        $personalBoard = $player->getPersonalBoard();

        // Reserve now => Manage cards
        $playerCard = new PlayerCardSPL($player, $card, true);
        $personalBoard->addPlayerCard($playerCard);

        // Manage cards
        // => I know that my card is in main board; so i remove this card from row or draw card
       if ($card->getDrawCardsSPL() != null)
       {
           $this->manageDiscard($card);
       } else
       {
            $this->manageRow($mainBoard, $card);
       }

       // Manage token
       $this->manageJokerToken($player);
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
     * getReserveCards : get development cards reserved by player
     * @param PlayerSPL $player
     * @return Collection<int, DevelopmentCardsSPL>
     */
    public function getReserveCards(PlayerSPL $player) : Collection
    {
        $personalBoard = $player->getPersonalBoard();
        $cardsOfPlayer = $personalBoard->getPlayerCards();

        $reservedCards = new ArrayCollection();
        for ($i = 0; $i < $cardsOfPlayer->count(); $i++)
        {
            $card = $cardsOfPlayer->get($i);
            if ($card->isReserved())
            {
                $reservedCards->add($card);
            }
        }
        return $reservedCards;
    }

    //TODO : METHOD TO SET TURN TO NEXT PLAYER AND ENSURE EVERY OTHER TURN IS FALSE

    /**
     * @param GameSPL $game
     * @param string $name
     * @return ?PlayerSPL
     */
    public function getPlayerFromNameAndGame(GameSPL $game, string $name) : ?PlayerSPL
    {
        return $this->playerSPLRepository->findOneBy(['gameSPL' => $game->getId(), 'username' => $name]);
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
    public function getPrestigePoints(PlayerSPL $player) : int
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

    private function canReserveCards(PlayerSPL $player, DevelopmentCardsSPL $card): Boolean
    {
        if (!$this->checkCardSidesPlayer($player, $card) ||
            !$this->checkCanReserveSideMainBoard($card) )
        {
            return false;
        }

        return true;
    }

    private function checkCardSidesPlayer(PlayerSPL $player, DevelopmentCardsSPL $card): Boolean
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

    private function checkCanReserveSideMainBoard(DevelopmentCardsSPL $card): Boolean
    {
        // check if exist in main board

        return $card->getDrawCardsSPL() != null
            || $card->getRowSPL() != null;
    }

    private function manageRow(MainBoardSPL $mainBoard, DevelopmentCardsSPL $card): void
    {
        $row = $card->getRowSPL();
        $row->removeDevelopmentCard($card);

        // get first cards of discards associate at level
        $level = $row->getCardLevel();
        $discardsOfLevel = $mainBoard->getDrawCards()->get($level);
        $discard = $discardsOfLevel->getDevelopmentCards()->get(0);
        $row->addDevelopmentCard($discard);
        $discardsOfLevel->removeDevelopmentCard($card);
    }

    private function manageDiscard(DevelopmentCardsSPL $card): void
    {
        $discards = $card->getDrawCardsSPL();
        $discards->removeDevelopmentCard($card);
    }

    private function manageJokerToken(PlayerSPL $player): void
    {
        $personalBoard = $player->getPersonalBoard();
        $tokens = $personalBoard->getTokens();
        if ($tokens->count() <= SPLService::$MAX_POSSIBLE_COUNT_TOKENS)
        {
            $game = $player->getGameSPL();
            $mainBoard = $this->mainBoardSPLRepository->findOneBy(
                ["game" => $game->getId()]);
            if ($this->getNumberOfJokerToken($mainBoard) > 0)
            {
                $joker = $this->getJokerToken($mainBoard);
                $personalBoard->addToken($joker);
            }
        }
    }

    private function getNumberOfJokerToken(MainBoardSPL $mainBoard) : int
    {
        $count = 0;
        $tokens = $mainBoard->getTokens();
        for ($i = 0; $i < $tokens->count(); $i++)
        {
            $token = $tokens->get($i);
            if ($token->getColor() === SPLService::$LABEL_JOKER)
            {
                $count += 1;
            }
        }
        return $count;
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
        $selectedTokens = $playerSPL->getPersonalBoard()->getTokens();
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