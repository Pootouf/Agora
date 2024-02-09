<?php

namespace App\Service\Game\SixQP;


use App\Entity\Game\DTO\Game;
use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\SixQP\CardSixQPRepository;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use App\Service\Game\AbstractGameManagerService;
use App\Service\Game\LogService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use function PHPUnit\Framework\isNull;

class SixQPService
{
    public static int $MAX_POINTS = 66;

    private EntityManagerInterface $entityManager;
    private CardSixQPRepository $cardSixQPRepository;
    private ChosenCardSixQPRepository $chosenCardSixQPRepository;
    private PlayerSixQPRepository $playerSixQPRepository;

    public function __construct(EntityManagerInterface $entityManager,
        CardSixQPRepository $cardSixQPRepository,
        ChosenCardSixQPRepository $chosenCardSixQPRepository,
        PlayerSixQPRepository $playerSixQPRepository)
    {
        $this->entityManager = $entityManager;
        $this->cardSixQPRepository = $cardSixQPRepository;
        $this->chosenCardSixQPRepository = $chosenCardSixQPRepository;
        $this->playerSixQPRepository = $playerSixQPRepository;
    }




    /**
     * initializeNewRound : initialize a new round with random cards for central board and players
     * @param GameSixQP $gameSixQP
     * @return void
     * @throws Exception if not valid number of players or rows
     */
    public function initializeNewRound(GameSixQP $gameSixQP): void
    {
        if (count($gameSixQP->getRowSixQPs()) != RowSixQP::$NUMBER_OF_ROWS_BY_GAME) {
            throw new Exception('Invalid number of rows');
        }
        $numberOfPlayers = count($gameSixQP->getPlayerSixQPs());
        if ($numberOfPlayers > 10 || $numberOfPlayers < 2) {
            throw new Exception('Invalid number of players');
        }

        $cards = $this->cardSixQPRepository->findAll();
        shuffle($cards);
        $players = $gameSixQP->getPlayerSixQPs();
        $cardIndex = 0;
        foreach ($gameSixQP->getRowSixQPs() as $row) {
            $row->clearCards();
            $row->addCard($cards[$cardIndex++]);
            $this->entityManager->persist($row);
        }
        foreach ($players as $player) {
            $player->clearCards();
            for ($i = 0; $i < PlayerSixQP::$NUMBER_OF_CARDS_BY_PLAYER; $i++) {
                $player->addCard($cards[$cardIndex++]);
            }
            $this->entityManager->persist($player);
        }
        $this->entityManager->flush();
    }

    /**
     * chooseCard : players chooses a card which is now in state "chosen"
     * @param PlayerSixQP $player
     * @param CardSixQP $cardSixQP
     * @return void
     * @throws Exception if player doesn't own the card
     * @throws Exception if player has already chosen another card
     */
    public function chooseCard(PlayerSixQP $player, CardSixQP $cardSixQP): void
    {
        if (!$this->doesPlayerOwnsCard($player, $cardSixQP)) {
            throw new Exception("Player doesn't own this card");
        }

        if ($player->getChosenCardSixQP() != null) {
            throw new Exception("Player has already chosen a card");
        }

        $chosenCardSixQP = new ChosenCardSixQP($player, $player->getGame(), $cardSixQP, false);
        $player->removeCard($cardSixQP);
        $player->setChosenCardSixQP($chosenCardSixQP);
        $this->entityManager->persist($chosenCardSixQP);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * placeCard : place the chosen card into the right row, and update player's discard if necessary
     * @param ChosenCardSixQP $chosenCardSixQP
     * @return int position of the row if the card has been placed, -1 otherwise
     */
    public function placeCard(ChosenCardSixQP $chosenCardSixQP): int
    {
        $game = $chosenCardSixQP->getGame();
        $rows = $game->getRowSixQPs();
        $player = $chosenCardSixQP->getPlayer();

        $row = $this->getValidRowForCard($chosenCardSixQP, $rows);
        if ($row == null) {
            return -1;
        }

        $returnValue = $row->getPosition();
        if ($row->getCards()->count() == 5) {
            $this->addRowToDiscardOfPlayer($player, $row);
        }
        $row->getCards()->add($chosenCardSixQP->getCard());


        $this->entityManager->persist($row);
        $this->entityManager->flush();
        return $returnValue;
    }

    /**
     * getRanking : get the ranking by player points (ascending)
     * @param GameSixQP $gameSixQP
     * @return array
     * @throws Exception
     */
    public function getRanking(GameSixQP $gameSixQP): array
    {
        $numberOfPlayers = count($gameSixQP->getPlayerSixQPs());
        if ($numberOfPlayers > 10 || $numberOfPlayers < 2)
        {
            throw new Exception('Invalid number of players');
        }


        $array = $gameSixQP->getPlayerSixQPs()->toArray();
        usort($array,
            function (PlayerSixQP $player1, PlayerSixQP $player2) {
                return $player1->getDiscardSixQP()->getTotalPoints() - $player2->getDiscardSixQP()->getTotalPoints();
            });
        return $array;
    }

    /**
     * calculatePoints : update player points from his discard
     * @param DiscardSixQP $discardSixQP
     * @return void
     */
    public function calculatePoints(DiscardSixQP $discardSixQP): void
    {
        $cards = $discardSixQP->getCards();
        $totalPoints = 0;
        foreach ($cards as $card) {
            $totalPoints += $card->getPoints();
        }
        $discardSixQP->setTotalPoints($totalPoints);
        $this->entityManager->persist($discardSixQP);
        $this->entityManager->flush();
    }

    /**
     * revealCard : reveal the chosen card
     * @param ChosenCardSixQP $chosenCardSixQP the card to reveal
     * @return void
     */
    public function revealCard(ChosenCardSixQP $chosenCardSixQP): void
    {
        $chosenCardSixQP->setVisible(true);
        $this->entityManager->persist($chosenCardSixQP);
        $this->entityManager->flush();
    }

    public function doesAllPlayersHaveChosen(GameSixQP $game): bool {
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        return count($chosenCards) == count($game->getPlayerSixQPs());
    }

    public function doesPlayerAlreadyHasPlayed(PlayerSixQP $player): bool
    {
        $chosenCard = $this->chosenCardSixQPRepository->findOneBy(['player'=>$player->getId()]);
        return $chosenCard != null;
    }

    public function getNotPlacedCard(GameSixQP $game): array {
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        for ($i = 0; $i < count($chosenCards); $i++) {
            foreach ($game->getRowSixQPs() as $row) {
                if ($row->isCardInRow($chosenCards[$i]->getCard())) {
                    array_splice($chosenCards, $i, 1);
                    break;
                }
            }
        }
        return $chosenCards;
    }

    /**
     * isGameEnded : checks if game has ended
     * @param GameSixQP $gameSixQP 
     * @return bool
     */
    public function isGameEnded(GameSixQP $gameSixQP): bool
    {
        $players = $gameSixQP->getPlayerSixQPs();
        if ($this->hasCardLeft($players)) {
            return false;
        }
        return $this->hasPlayerLost($players);
    }

    public function getPlayerFromNameAndGame(GameSixQP $game, string $name)
    {
        return $this->playerSixQPRepository->findOneBy(['game' => $game->getId(), 'username' => $name]);
    }


    /**
     * getValidRowForCard : calculate the row with the nearest value to the chosen card,
     *                      with the chosen card value greater than the value of the row
     * @param ChosenCardSixQP $chosenCardSixQP the chosen card
     * @param Collection $rows the rows of the game
     * @return ?RowSixQP the valid row, null if no valid row in the game
     */
    public function getValidRowForCard(ChosenCardSixQP $chosenCardSixQP, Collection $rows): ?RowSixQP
    {
        $rowResult = null;
        $lastSmallestDistance = INF;
        foreach ($rows as $row) {
            $cards = $row->getCards();

            $chosenCardValue = $chosenCardSixQP->getCard()->getValue();
            $rowValue = $cards->last()->getValue();
            if (
                $rowValue < $chosenCardValue
                && ($chosenCardValue - $rowValue) < $lastSmallestDistance
            ) {
                $rowResult = $row;
                $lastSmallestDistance = $chosenCardValue - $rowValue;
            }
        }
        return $rowResult;
    }

    /**
     * addRowToDiscardOfPlayer : add all the cards of the row to the discard of the player
     * @param PlayerSixQP $player the player who will get the cards
     * @param RowSixQP $row the row with the cards to add to the discard
     * @return void
     */
    public function addRowToDiscardOfPlayer(PlayerSixQP $player, RowSixQP $row): void
    {
        $logService = new LogService($this->entityManager);
        $total = 0;
        foreach ($row->getCards() as $card) {
            $player->getDiscardSixQP()->addCard($card);
            $player->getDiscardSixQP()->addPoints($card->getPoints());
            $total += $card->getPoints();
            $row->removeCard($card);
        }
        $game = $player->getGame();
        $logService->sendPlayerLog($game, $player, $player->getUsername()
            . " picked up row " . $game->getRowSixQPs()->indexOf($row)
             . " and got " . $total . " points");
        $this->entityManager->persist($row);
        $this->entityManager->persist($player->getDiscardSixQP());
        $this->entityManager->flush();
    }

    /**
     * doesPlayerOwnsCard : check if player really owns the asked card
     * @param PlayerSixQP $player
     * @param CardSixQP $card
     * @return bool
     */
    private function doesPlayerOwnsCard(PlayerSixQP $player, CardSixQP $card): bool
    {
        $cards = $player->getCards();
        return $cards->contains($card);
    }

    /**
     * hasCardLeft : checks if at least one player still has a card
     * @param Collection $players : a collection of 6QP players
     */
    public function hasCardLeft(Collection $players): bool
    {
        foreach ($players as $player) {
            if (count($player->getCards()) != 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * getWinner : return the winner of the game
     * @param GameSixQP $game
     * @return ?PlayerSixQP if there is a winner, null otherwise
     */
    public function getWinner(GameSixQP $game): ?PlayerSixQP
    {
        $winner = null;
        $winnerScore = INF;
        foreach ($game->getPlayerSixQPs() as $player) {
            if ($player->getDiscardSixQP()->getTotalPoints() < $winnerScore) {
                $winner = $player;
                $winnerScore = $player->getDiscardSixQP()->getTotalPoints();
            } elseif ($player->getDiscardSixQP()->getTotalPoints() == $winnerScore) {
                $winner = null;
            }
        }
        return $winner;
    }

    public function clearCards(array $chosenCards): void
    {
        foreach ($chosenCards as $chosenCard) {
            $player = $chosenCard->getPlayer();
            $this->entityManager->remove($chosenCard);
            $player->setChosenCardSixQP(null);
            $this->entityManager->persist($player);
        }
        $this->entityManager->flush();
    }

     /**
     * hasPlayerLost : checks if at least one player has reached limit of points
     * @param Collection $players : a collection of 6QP players
     */
    private function hasPlayerLost(Collection $players): bool
    {
        foreach($players as $player) {
            if ($player -> getDiscardSixQP() -> getTotalPoints() >= SixQPService::$MAX_POINTS) {
                return true;
            }
        }
        return false;
    }

    private function getGameSixQPFromGame(Game $game): ?GameSixQP {
        /** @var GameSixQP $game */
        return $game->getGameName() == AbstractGameManagerService::$SIXQP_LABEL ? $game : null;
    }
}
