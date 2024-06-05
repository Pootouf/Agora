<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Splendor\CardCostSPLRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardCostSPLRepository::class)]
class CardCostSPL extends Component
{
    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\Column]
    private ?int $price = null;

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }
}
