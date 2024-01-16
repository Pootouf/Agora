<?php

namespace App\Service\Game;

use App\Entity\Game\SixQP\ChosenCardSixQP;
use App\Entity\Game\SixQP\RowSixQP;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use function PHPUnit\Framework\isNull;

class SixQPService
{

    public function placeCard(EntityManagerInterface $entityManager, ChosenCardSixQP $chosenCardSixQP) : int {
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
            $entityManager->persist($player->getDiscardSixQP());
        }

        $entityManager->persist($row);
        $entityManager->remove($chosenCardSixQP);
        $entityManager->flush();
        return 0;
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

}