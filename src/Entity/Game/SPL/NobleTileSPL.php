<?php

namespace App\Entity\Game\SPL;

use App\Entity\Game\DTO\Tile;
use App\Repository\Game\SPL\NobleTileSPLRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NobleTileSPLRepository::class)]
class NobleTileSPL extends Tile
{
    #[ORM\Column]
    private ?int $prestigePoints = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $costCardsColor = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $countCostCardsColor = [];

    #[ORM\ManyToOne(inversedBy: 'nobleTiles')]
    private ?PersonalBoardSPL $personalBoardSPL = null;

    #[ORM\ManyToOne(inversedBy: 'nobleTiles')]
    private ?MainBoardSPL $mainBoardSPL = null;

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

    public function getPersonalBoardSPL(): ?PersonalBoardSPL
    {
        return $this->personalBoardSPL;
    }

    public function setPersonalBoardSPL(?PersonalBoardSPL $personalBoardSPL): static
    {
        $this->personalBoardSPL = $personalBoardSPL;

        return $this;
    }

    public function getMainBoardSPL(): ?MainBoardSPL
    {
        return $this->mainBoardSPL;
    }

    public function setMainBoardSPL(?MainBoardSPL $mainBoardSPL): static
    {
        $this->mainBoardSPL = $mainBoardSPL;

        return $this;
    }
}
