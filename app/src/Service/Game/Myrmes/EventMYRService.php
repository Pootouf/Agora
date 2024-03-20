<?php

namespace App\Service\Game\Myrmes;

use App\Entity\Game\Myrmes\NurseMYR;
use App\Entity\Game\Myrmes\PlayerMYR;
use App\Repository\Game\Myrmes\NurseMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Collection;

class EventMYRService
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
}