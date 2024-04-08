<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpDocumentor\Reflection\Types\Collection;

class EventMYRService
{

    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * upBonus : the player spends a larva, if he owns one, to get chosen bonus
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    public function upBonus(PlayerMYR $player) : void
    {
        if ($player->getPhase() != MyrmesParameters::PHASE_EVENT) {
            throw new Exception("Player is not in event phase");
        }
        $personalBoard = $player->getPersonalBoardMYR();
        $larvaeCount = $personalBoard->getLarvaCount();
        $selectedLarvae = $personalBoard->getSelectedEventLarvaeAmount();
        $playerBonus = $personalBoard->getBonus();
        if ($playerBonus + 1 > MyrmesParameters::BONUS_WORKER) {
            throw new Exception("this bonus does not exist");
        }
        $seasons = $player->getGameMyr()->getMainBoardMYR()->getSeasons();
        $gameBonus = 0;
        foreach ($seasons as $season) {
            if ($season->isActualSeason()) {
                $gameBonus = $season->getDiceResult();
                break;
            }
        }
        $newBonus = $playerBonus + 1;
        if ($playerBonus < $gameBonus) {
            $personalBoard->setSelectedEventLarvaeAmount($selectedLarvae - 1);
        } else {
            if ($larvaeCount + $gameBonus < $newBonus) {
                throw new Exception("Player can't choose this bonus, not enough larvae owned");
            }
            $personalBoard->setSelectedEventLarvaeAmount($selectedLarvae + 1);
        }
        $personalBoard->setBonus($newBonus);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    /**
     * lowerBonus : the player spends a larva, if he owns one, to get chosen bonus
     * @param PlayerMYR $player
     * @return void
     * @throws Exception
     */
    public function lowerBonus(PlayerMYR $player) : void
    {
        if ($player->getPhase() != MyrmesParameters::PHASE_EVENT) {
            throw new Exception("Player is not in event phase");
        }
        $personalBoard = $player->getPersonalBoardMYR();
        $larvaeCount = $personalBoard->getLarvaCount();
        $selectedLarvae = $personalBoard->getSelectedEventLarvaeAmount();
        $playerBonus = $personalBoard->getBonus();
        if ($playerBonus - 1 < MyrmesParameters::BONUS_LEVEL) {
            throw new Exception("this bonus does not exist");
        }
        $gameBonus = 0;
        $seasons = $player->getGameMyr()->getMainBoardMYR()->getSeasons();
        foreach ($seasons as $season) {
            if ($season->isActualSeason()) {
                $gameBonus = $season->getDiceResult();
                break;
            }
        }

        $newBonus = $playerBonus - 1;
        if ($playerBonus > $gameBonus) {
            $personalBoard->setSelectedEventLarvaeAmount($selectedLarvae - 1);
        } else {
            if ($gameBonus - $larvaeCount > $newBonus) {
                throw new Exception("Player can't choose this bonus, not enough larvae owned");
            }
            $personalBoard->setSelectedEventLarvaeAmount($selectedLarvae + 1);
        }
        $personalBoard->setBonus($newBonus);
        $this->entityManager->persist($personalBoard);
        $this->entityManager->flush();
    }

    /**
     * confirmBonus : player confirm his bonus and ends his event phase
     * @param PlayerMYR $player
     * @return void
     */
    public function confirmBonus(PlayerMYR $player) : void
    {
        $player->setPhase(MyrmesParameters::PHASE_BIRTH);
        $bonus = $player->getPersonalBoardMYR()->getBonus();
        if($bonus == MyrmesParameters::BONUS_HARVEST) {
            $player->setRemainingHarvestingBonus(MyrmesParameters::HARVESTED_TILE_BONUS);
        }
        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }
}