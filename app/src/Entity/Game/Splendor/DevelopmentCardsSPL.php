<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Card;
use App\Repository\Game\Splendor\DevelopmentCardsSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevelopmentCardsSPLRepository::class)]
class DevelopmentCardsSPL extends Card
{
    public static int $LEVEL_ONE = 1;
    public static int $LEVEL_TWO = 2;
    public static int $LEVEL_THREE = 3;

    #[ORM\Column]
    private ?int $prestigePoints = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\ManyToMany(targetEntity: CardCostSPL::class)]
    private Collection $cardCost;

    #[ORM\Column]
    private ?int $level = null;

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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, CardCostSPL>
     */
    public function getCardCost(): Collection
    {
        return $this->cardCost;
    }

    /**
     * @codeCoverageIgnore
     */
    private function addCardCost(CardCostSPL $cardCost): static
    {
        if (!$this->cardCost->contains($cardCost)) {
            $this->cardCost->add($cardCost);
        }

        return $this;
    }
    /**
     * @codeCoverageIgnore
     */
    private function removeCardCost(CardCostSPL $cardCost): static
    {
        $this->cardCost->removeElement($cardCost);

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }
}
