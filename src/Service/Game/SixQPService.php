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
    public static int $NUMBER_OF_CARDS_BY_PLAYER = 10;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * initializeNewRound : initialize a new round with random cards for central board and players
     * @param CardSixQPRepository $cardSixQPRepository
     * @param GameSixQP $gameSixQP
     * @return void
     */
    public function initializeNewRound(CardSixQPRepository $cardSixQPRepository, GameSixQP $gameSixQP): void
    {
        $cards = $cardSixQPRepository->findAll();
        shuffle($cards);
        $players = $gameSixQP->getPlayerSixQPs();
        $cardIndex = 0;
        foreach ($gameSixQP->getRowSixQPs() as $row) {
            $row->clearCards();
            $row->addCard($cards[$cardIndex++]);
        }

        foreach ($players as $player) {
            $player->clearCards();
            for ($i = 0; $i < SixQPService::$NUMBER_OF_CARDS_BY_PLAYER; $i++) {
                $player->addCard($cards[$cardIndex++]);
            }
        }
    }

    /**
     * chooseCard : players chooses a card which is now in state "chosen"
     * @param PlayerSixQP $player
     * @param CardSixQP $cardSixQP
     * @return void
     */
    public function chooseCard(PlayerSixQP $player, CardSixQP $cardSixQP): void
    {
        if (!$this->playerOwnsCard($player, $cardSixQP)) {
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

    public function revealCard(ChosenCardSixQP $chosenCardSixQP) : void
    {
        $chosenCardSixQP->setState(true);
        $this->entityManager->persist($chosenCardSixQP);
        $this->entityManager->flush();
    }

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
     * playerOwnsCard : check if player really owns the asked card
     * @param PlayerSixQP $player
     * @param CardSixQP $card
     * @return bool
     */
    private function playerOwnsCard(PlayerSixQP $player, CardSixQP $card): bool
    {
        $cards = $player->getCards();
        return $cards->contains($card);
    }
}