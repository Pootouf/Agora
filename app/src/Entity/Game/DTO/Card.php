<?php

namespace App\Entity\Game\DTO;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class Card extends Component
{
    #[ORM\Column(nullable: true)]
    protected ?int $value = null;

    #[ORM\Column(nullable: true)]
    protected ?int $points = null;

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

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
