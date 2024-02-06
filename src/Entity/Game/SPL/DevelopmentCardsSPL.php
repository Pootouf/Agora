<?php

namespace App\Entity\Game\SPL;

use App\Repository\Game\SPL\DevelopmentCardsSPLRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevelopmentCardsSPLRepository::class)]
class DevelopmentCardsSPL
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $prestigePoints = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $costTokensColor = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $countCostTokensColor = [];

    #[ORM\ManyToOne(inversedBy: 'developmentCards')]
    private ?DrawCardsSPL $drawCardsSPL = null;

    #[ORM\ManyToOne(inversedBy: 'developmentCards')]
    private ?RowSPL $rowSPL = null;

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

    public function getCostTokensColor(): array
    {
        return $this->costTokensColor;
    }

    public function setCostTokensColor(array $costTokensColor): static
    {
        $this->costTokensColor = $costTokensColor;

        return $this;
    }

    public function getCountCostTokensColor(): array
    {
        return $this->countCostTokensColor;
    }

    public function setCountCostTokensColor(array $countCostTokensColor): static
    {
        $this->countCostTokensColor = $countCostTokensColor;

        return $this;
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
