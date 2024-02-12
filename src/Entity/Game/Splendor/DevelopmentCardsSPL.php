<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Card;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevelopmentCardsSPLRepository::class)]
class DevelopmentCardsSPL extends Card
{
    #[ORM\Column]
    private ?int $prestigePoints = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    /**
     * @type Collection<CardCostSPL>
     */
    #[ORM\Column]
    private Collection $cardCost;

    #[ORM\ManyToOne(inversedBy: 'developmentCards')]
    private ?DrawCardsSPL $drawCardsSPL = null;

    #[ORM\ManyToOne(inversedBy: 'developmentCards')]
    private ?RowSPL $rowSPL = null;

    public function __construct(Collection $cardCost)
    {
        $this->cardCost = $cardCost;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrestigePoints(): ?int
    {
        return $this->prestigePoints;
    }

    public function setPrestigePoints(int $prestigePoints): static
    {
        $this->prestigePoints = $prestigePoints;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getCardCost(): Collection
    {
        return $this->cardCost;
    }

    public function getDrawCardsSPL(): ?DrawCardsSPL
    {
        return $this->drawCardsSPL;
    }

    public function setDrawCardsSPL(?DrawCardsSPL $drawCardsSPL): static
    {
        $this->drawCardsSPL = $drawCardsSPL;

        return $this;
    }

    public function getRowSPL(): ?RowSPL
    {
        return $this->rowSPL;
    }

    public function setRowSPL(?RowSPL $rowSPL): static
    {
        $this->rowSPL = $rowSPL;

        return $this;
    }
}
