<?php

namespace App\Entity\Game\SixQP;

use App\Repository\Game\SixQP\CardSixQPRepository;
use App\Entity\Game\DTO\Card;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardSixQPRepository::class)]
class CardSixQP extends Card
{

    #[ORM\Column]
    private ?int $points = null;

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }
}
