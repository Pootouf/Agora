<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Splendor\NobleTileSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NobleTileSPLRepository::class)]
class NobleTileSPL extends Component
{
    #[ORM\Column]
    private ?int $prestigePoints = null;

    #[ORM\ManyToMany(targetEntity: CardCostSPL::class)]
    private Collection $cardsCost;

    public function __construct()
    {
        $this->cardsCost = new ArrayCollection();
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

    /**
     * @return Collection<int, CardCostSPL>
     */
    public function getCardsCost(): Collection
    {
        return $this->cardsCost;
    }

    public function addCardsCost(CardCostSPL $cardsCost): static
    {
        if (!$this->cardsCost->contains($cardsCost)) {
            $this->cardsCost->add($cardsCost);
        }

        return $this;
    }

    public function removeCardsCost(CardCostSPL $cardsCost): static
    {
        $this->cardsCost->removeElement($cardsCost);

        return $this;
    }
}
