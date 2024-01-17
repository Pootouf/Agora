<?php

namespace App\Service\Game;

use App\Entity\Game\SixQP\DiscardSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use function PHPUnit\Framework\isNull;

class SixQPService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this -> entityManager = $entityManager;
    } 

    public function chooseCard(PlayerSixQP $player, CardSixQP $cardSixQP) : void {
        if (!$this->playerOwnsCard($player, $cardSixQP)) {
            throw new Exception("Player doesn't own this card");
        }

        if ($player -> getChosenCardSixQP() != null) {
            throw new Exception("Player has already chosen a card");
        }

        $chosenCardSixQP = new ChosenCardSixQP($player, $player -> getGame(), $cardSixQP, false);
        $player -> removeCard($cardSixQP);
        $player -> setChosenCardSixQP($chosenCardSixQP);
        $this->entityManager -> persist($chosenCardSixQP);
        $this->entityManager -> persist($player);
        $this->entityManager -> flush();
    }

    public function placeCard(ChosenCardSixQP $chosenCardSixQP) : int {
        $game = $chosenCardSixQP->getGame();
        $rows = $game->getRowSixQPs();
        $player = $chosenCardSixQP->getPlayer();

        $row = $this->getValidRowForCard($chosenCardSixQP, $rows);
        if (isNull($row)) {
            return -1;
        }

        $row->getCards()->add($chosenCardSixQP->getCard());

        if ($row->getCards()->count() == 6) {
            for($i = 0; $i < 5; $i++) {
                $card = $row->getCards()->get(0);
                $row->getCards()->remove(0); //On supprime la position 0 pour supprimer les 5 premières cartes
                $player->getDiscardSixQP()->addCard($card);
            }
            $this->entityManager->persist($player->getDiscardSixQP());
        }

        $this->entityManager->persist($row);
        $this->entityManager->remove($chosenCardSixQP);
        $this->entityManager->flush();
        return 0;
    }

    public function calculatePoints(DiscardSixQP $discardSixQP) : void {
        $cards = $discardSixQP -> getCards();
        $totalPoints = 0;
        foreach($cards as $card) {
            $totalPoints += $card -> getPoints();
        }
        $discardSixQP -> setTotalPoints($totalPoints);
        $this -> entityManager -> persist($discardSixQP);
        $this -> entityManager -> flush();
    }

    private function getValidRowForCard(ChosenCardSixQP $chosenCardSixQP, Collection $rows) : RowSixQP {
        $rowResult = null;
        $lastSmallestDistance = INF;
        foreach ($rows as $row) {
            $cards = $row->getCards();

            $chosenCardValue = $chosenCardSixQP->getCard()->getValue();
            $rowValue = $cards->get($cards->count() - 1)->getValue();
            if($rowValue < $chosenCardValue
                && ($chosenCardValue - $rowValue) < $lastSmallestDistance) {
                $rowResult = $row;
                $lastSmallestDistance = $chosenCardValue - $rowValue;
            }
        }
        return $rowResult;
    }

    private function playerOwnsCard(PlayerSixQP $player, CardSixQP $card) : bool {
        $cards = $player -> getCards();
        return $cards -> contains($card);
    }
}