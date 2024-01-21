<?php

namespace App\Service\Game;

use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\GameSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use App\Repository\Game\SixQP\CardSixQPRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use function PHPUnit\Framework\isNull;

class SixQPService
{
    private EntityManagerInterface $entityManager;

    private CardSixQPRepository $cardSixQPRepository;

    public function __construct(EntityManagerInterface $entityManager, CardSixQPRepository $cardSixQPRepository)
    {
        $this->entityManager = $entityManager;
        $this->cardSixQPRepository = $cardSixQPRepository;
    }

    /**
     * initializeNewRound : initialize a new round with random cards for central board and players
     * @param GameSixQP $gameSixQP
     * @return void
     * @throws Exception if not valid number of players or rows
     */
    public function initializeNewRound(GameSixQP $gameSixQP): void
    {
        if(count($gameSixQP->getRowSixQPs()) != RowSixQP::$NUMBER_OF_ROWS_BY_GAME) {
            throw new Exception('Invalid number of rows');
        }
        $numberOfPlayers = count($gameSixQP->getPlayerSixQPs());
        if($numberOfPlayers > 10 || $numberOfPlayers < 2) {
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
     * @return int 0 if the card has been placed, -1 otherwise
     */
    public function placeCard(ChosenCardSixQP $chosenCardSixQP): int
    {
        $game = $chosenCardSixQP->getGame();
        $rows = $game->getRowSixQPs();
        $player = $chosenCardSixQP->getPlayer();

        $row = $this->getValidRowForCard($chosenCardSixQP, $rows);
        if (isNull($row)) {
            return -1;
        }

        $row->getCards()->add($chosenCardSixQP->getCard());
        if ($row->getCards()->count() == 6) {
            $this->addRowToDiscardOfPlayer($player, $row);
        }

        $this->entityManager->persist($row);
        $this->entityManager->remove($chosenCardSixQP);
        $this->entityManager->flush();
        return 0;
    }

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
    public function revealCard(ChosenCardSixQP $chosenCardSixQP) : void
    {
        $chosenCardSixQP->setVisible(true);
        $this->entityManager->persist($chosenCardSixQP);
        $this->entityManager->flush();
    }

    /**
     * getValidRowForCard : calculate the row with the nearest value to the chosen card,
     *                      with the chosen card value greater than the value of the row
     * @param ChosenCardSixQP $chosenCardSixQP the chosen card
     * @param Collection $rows the rows of the game
     * @return RowSixQP the valid row, null if no valid row in the game
     */
    private function getValidRowForCard(ChosenCardSixQP $chosenCardSixQP, Collection $rows): RowSixQP
    {
        $rowResult = null;
        $lastSmallestDistance = INF;
        foreach ($rows as $row) {
            $cards = $row->getCards();

            $chosenCardValue = $chosenCardSixQP->getCard()->getValue();
            $rowValue = $cards->get($cards->count() - 1)->getValue();
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
    private function addRowToDiscardOfPlayer(PlayerSixQP $player, RowSixQP $row): void
    {
        for ($i = 0; $i < 5; $i++) {
            $card = $row->getCards()->get(0);
            $row->getCards()->remove(0); //On supprime la position 0 pour supprimer les 5 premières cartes
            $player->getDiscardSixQP()->addCard($card);
        }
        $this->entityManager->persist($player->getDiscardSixQP());
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
}