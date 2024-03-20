<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\MyrmesParameters;
use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class BirthMYRService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly MYRService $MYRService,
                                private readonly NurseMYRRepository $nurseMYRRepository
    )
    {}

    /**
     * placeNurse : place the nurse in $newPosition
     * @param NurseMYR $nurseMYR
     * @param int $newPosition
     * @return void
     */
    public function placeNurse(NurseMYR $nurseMYR, int $newPosition) : void
    {
        $nurseMYR->setPosition($newPosition);
        $this->entityManager->persist($nurseMYR);
        $this->entityManager->flush();
    }

    /**
     * giveBirthBonus : give player birth bonus depending on nurses on areas
     * @param PlayerMYR $playerMYR
     * @return void
     * @throws \Exception
     */
    public function giveBirthBonus(PlayerMYR $playerMYR) : void
    {
        for($i = 1; $i < MyrmesParameters::$AREA_COUNT; $i += 1) {
            $nurses = $this->getNursesAtPosition($playerMYR ,$i);
            switch ($i) {
                case MyrmesParameters::$LARVAE_AREA;
                $this->activateLarvaeArea($playerMYR, $nurses);
                break;
            }
        }
    }

    /**
     * getNursesAtPosition : return nurses which is in $position
     * @param PlayerMYR $player
     * @param int $position
     * @return ArrayCollection
     */
    private function getNursesAtPosition(PlayerMYR $player, int $position): ArrayCollection
    {
        $nurses =  $this->nurseMYRRepository->findBy(["position" => $position,
            "player" => $player]);
        return new ArrayCollection($nurses);
    }

    /**
     * activateLarvaeArea : gives player the larva in function of nurses in area
     * @param PlayerMYR $playerMYR
     * @param ArrayCollection $array
     * @return void
     * @throws \Exception
     */
    private function activateLarvaeArea(PlayerMYR $playerMYR, ArrayCollection $array) : void
    {
        $playerBoard = $playerMYR->getPersonalBoardMYR();
        $currentLarvaCount = $playerBoard->getLarvaCount();
        switch ($array->count()) {
            case 0 : return;
            case 1 : $playerBoard->setLarvaCount($currentLarvaCount + 1);
            break;
            case 2 : $playerBoard->setLarvaCount($currentLarvaCount + 3);
            break;
            case 3 : $playerBoard->setLarvaCount($currentLarvaCount + 5);
            break;
            default :
                throw new \Exception("TOO MUCH NURSES ON THIS PLACE");
        }
    }
}