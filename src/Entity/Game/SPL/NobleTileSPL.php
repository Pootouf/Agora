<?php

namespace App\Entity\Game\SPL;

use App\Repository\Game\SPL\NobleTileSPLRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NobleTileSPLRepository::class)]
class NobleTileSPL
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $prestigePoints = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $costCardsColor = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $countCostCardsColor = [];

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

    public function getCostCardsColor(): array
    {
        return $this->costCardsColor;
    }

    public function setCostCardsColor(array $costCardsColor): static
    {
        $this->costCardsColor = $costCardsColor;

        return $this;
    }

    public function getCountCostCardsColor(): array
    {
        return $this->countCostCardsColor;
    }

    public function setCountCostCardsColor(array $countCostCardsColor): static
    {
        $this->countCostCardsColor = $countCostCardsColor;

        return $this;
    }
}
