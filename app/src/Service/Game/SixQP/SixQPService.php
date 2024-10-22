<?php

namespace App\Service\Game\SixQP;

use App\Entity\Game\DTO\Game;
use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Entity\Game\SixQP\SixQPParameters;
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
    private EntityManagerInterface $entityManager;
    private CardSixQPRepository $cardSixQPRepository;
    private ChosenCardSixQPRepository $chosenCardSixQPRepository;
    private PlayerSixQPRepository $playerSixQPRepository;

    private LogService $logService;

    public function __construct(
        EntityManagerInterface $entityManager,
        CardSixQPRepository $cardSixQPRepository,
        ChosenCardSixQPRepository $chosenCardSixQPRepository,
        PlayerSixQPRepository $playerSixQPRepository,
        LogService $logService
    ) {
        $this->entityManager = $entityManager;
        $this->cardSixQPRepository = $cardSixQPRepository;
        $this->chosenCardSixQPRepository = $chosenCardSixQPRepository;
        $this->playerSixQPRepository = $playerSixQPRepository;
        $this->logService = $logService;
    }




    /**
     * initializeNewRound : initialize a new round with random cards for central board and players
     * @param GameSixQP $gameSixQP
     * @return void
     * @throws Exception if not valid number of players or rows
     */
    public function initializeNewRound(GameSixQP $gameSixQP): void
    {
        if (count($gameSixQP->getRowSixQPs()) != SixQPParameters::NUMBER_OF_ROWS_BY_GAME) {
            throw new Exception('Invalid number of rows');
        }
        $numberOfPlayers = count($gameSixQP->getPlayers());
        if ($numberOfPlayers > SixQPParameters::MAX_NUMBER_OF_PLAYER ||
                $numberOfPlayers < SixQPParameters::MIN_NUMBER_OF_PLAYER) {
            throw new Exception('Invalid number of players');
        }

        $cards = $this->cardSixQPRepository->findAll();
        shuffle($cards);
        $players = $gameSixQP->getPlayers();
        $cardIndex = 0;
        foreach ($gameSixQP->getRowSixQPs() as $row) {
            $row->clearCards();
            $row->addCard($cards[$cardIndex++]);
            $this->entityManager->persist($row);
        }
        foreach ($players as $player) {
            $player->clearCards();
            for ($i = 0; $i < SixQPParameters::NUMBER_OF_CARDS_BY_PLAYER; $i++) {
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

        $chosenCardSixQP = new ChosenCardSixQP($player, $player->getGame(), $cardSixQP);
        $player->removeCard($cardSixQP);
        $player->setChosenCardSixQP($chosenCardSixQP);
        $this->entityManager->persist($chosenCardSixQP);
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    /**
     * placeCard : place the chosen card into the selected row, and update player's discard if necessary
     * @param ChosenCardSixQP $chosenCardSixQP
     * @param RowSixQP $row
     * @return int 0 if the card has been placed, -1 if line retrieve
     */
    public function placeCardIntoRow(ChosenCardSixQP $chosenCardSixQP, RowSixQP $row): int
    {
        $player = $chosenCardSixQP->getPlayer();

        $returnValue = 0;
        if ($row->getCards()->count() == SixQPParameters::MAX_CARD_COUNT_IN_LINE) {
            $this->addRowToDiscardOfPlayer($player, $row);
            $returnValue = -1;
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
        $numberOfPlayers = count($gameSixQP->getPlayers());
        if ($numberOfPlayers > SixQPParameters::MAX_NUMBER_OF_PLAYER ||
                $numberOfPlayers < SixQPParameters::MIN_NUMBER_OF_PLAYER) {
            throw new Exception('Invalid number of players');
        }


        $array = $gameSixQP->getPlayers()->toArray();
        usort(
            $array,
            function (PlayerSixQP $player1, PlayerSixQP $player2) {
                return $player1->getScore() - $player2->getScore();
            }
        );
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
        $discardSixQP->getPlayer()->setScore($totalPoints);
        $this->entityManager->persist($discardSixQP->getPlayer());
        $this->entityManager->flush();
    }

    /**
     * doesAllPlayersHaveChosen: return true if all players have chosen
     * @param GameSixQP $game
     * @return bool
     */
    public function doesAllPlayersHaveChosen(GameSixQP $game): bool
    {
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        return count($chosenCards) == count($game->getPlayers());
    }

    /**
     * doesPlayerAlreadyHasPlayed: return true if the player already have chose a card
     * @param PlayerSixQP $player
     * @return bool
     */
    public function doesPlayerAlreadyHasPlayed(PlayerSixQP $player): bool
    {
        $chosenCard = $this->chosenCardSixQPRepository->findOneBy(['player' => $player->getId()]);
        return $chosenCard != null;
    }

    /**
     * getNotPlacedCard: return an array of the chosen cards which are not already placed
     * @param GameSixQP $game
     * @return array<ChosenCardSixQP> the chosen cards which are not placed
     */
    public function getNotPlacedCard(GameSixQP $game): array
    {
        $chosenCards = $this->chosenCardSixQPRepository->findBy(['game' => $game->getId()]);
        for ($i = 0; $i < count($chosenCards); $i++) {
            foreach ($game->getRowSixQPs() as $row) {
                if ($row->isCardInRow($chosenCards[$i]->getCard())) {
                    array_splice($chosenCards, $i, 1);
                    break;
                }
            }
        }
        usort($chosenCards, function (ChosenCardSixQP $a, ChosenCardSixQP $b) {
            return $a->getCard()->getValue() - $b->getCard()->getValue();
        });
        return $chosenCards;
    }

    /**
     * isGameEnded : checks if game has ended
     * @param GameSixQP $gameSixQP
     * @return bool
     */
    public function isGameEnded(GameSixQP $gameSixQP): bool
    {
        $players = $gameSixQP->getPlayers();
        if ($this->hasCardLeft($players)) {
            return false;
        }
        return $this->hasPlayerLost($players);
    }

    /**
     * getPlayerFromNameAndGame: search for the player with name $name in the game, null if not found
     * @param GameSixQP $game the game of the player
     * @param string $name
     * @return ?PlayerSixQP
     */
    public function getPlayerFromNameAndGame(GameSixQP $game, string $name): ?PlayerSixQP
    {
        return $this->playerSixQPRepository->findOneBy(['game' => $game->getId(), 'username' => $name]);
    }


    /**
     * getValidRowForCard : calculate the row with the nearest value to the chosen card,
     *                      with the chosen card value greater than the value of the row
     * @param ChosenCardSixQP $chosenCardSixQP the chosen card
     * @param GameSixQP $game the game
     * @return ?RowSixQP the valid row, null if no valid row in the game
     */
    public function getValidRowForCard(GameSixQP $game, ChosenCardSixQP $chosenCardSixQP): ?RowSixQP
    {
        $rowResult = null;
        $lastSmallestDistance = INF;
        foreach ($game->getRowSixQPs() as $row) {
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
        $total = 0;
        foreach ($row->getCards() as $card) {
            $player->getDiscardSixQP()->addCard($card);
            $player->addScore($card->getPoints());
            $total += $card->getPoints();
            $row->removeCard($card);
        }
        $game = $player->getGame();
        $this->logService->sendPlayerLog($game, $player, $player->getUsername()
            . " a choisi la ligne " . $game->getRowSixQPs()->indexOf($row)
             . " et a récolté " . $total . " points");
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
     * @param Collection<PlayerSixQP> $players : a collection of 6QP players
     * @return bool
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
        foreach ($game->getPlayers() as $player) {
            if ($player->getScore() < $winnerScore) {
                $winner = $player;
                $winnerScore = $player->getScore();
            } elseif ($player->getScore() == $winnerScore) {
                $winner = null;
            }
        }
        return $winner;
    }

    /**
     * clearCards: delete all the chosenCards in the array
     * @param array<ChosenCardSixQP> $chosenCards
     * @return void
     */
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
    * @param Collection<PlayerSixQP> $players : a collection of 6QP players
    * @return bool
    */
    private function hasPlayerLost(Collection $players): bool
    {
        foreach($players as $player) {
            if ($player->getScore() >= SixQPParameters::MAX_POINTS) {
                return true;
            }
        }
        return false;
    }
}
