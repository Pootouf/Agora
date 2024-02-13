<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Tile;
use App\Repository\Game\Splendor\NobleTileSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NobleTileSPLRepository::class)]
class NobleTileSPL extends Tile
{
    #[ORM\Column]
    private ?int $prestigePoints = null;

    #[ORM\ManyToOne(inversedBy: 'nobleTiles')]
    private ?PersonalBoardSPL $personalBoardSPL = null;

    #[ORM\ManyToOne(inversedBy: 'nobleTiles')]
    private ?MainBoardSPL $mainBoardSPL = null;

    #[ORM\ManyToMany(targetEntity: CardCostSPL::class)]
    private Collection $cardCost;

    public function __construct()
    {
        $this->cardCost = new ArrayCollection();
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

    /**
     * @return Collection<int, CardCostSPL>
     */
    public function getCardCost(): Collection
    {
        return $this->cardCost;
    }

    public function addCardCost(CardCostSPL $cardCost): static
    {
        if (!$this->cardCost->contains($cardCost)) {
            $this->cardCost->add($cardCost);
        }

        return $this;
    }

    public function removeCardCost(CardCostSPL $cardCost): static
    {
        $this->cardCost->removeElement($cardCost);

        return $this;
    }
}
