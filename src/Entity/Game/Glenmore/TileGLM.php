<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Tile;
use App\Repository\Game\Glenmore\TileGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TileGLMRepository::class)]
class TileGLM extends Tile
{

    #[ORM\OneToMany(targetEntity: TileCostGLM::class, mappedBy: 'tileGLM', orphanRemoval: true)]
    private Collection $buyPrice;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?TileBonusGLM $buyBonus = null;

    #[ORM\OneToMany(targetEntity: TileCostGLM::class, mappedBy: 'tileBonusGLM')]
    private Collection $activationPrice;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileBonusGLM $activationBonus = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $containingRiver = null;

    #[ORM\Column]
    private ?bool $containingRoad = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?CardGLM $card = null;

    public function __construct()
    {
        $this->buyPrice = new ArrayCollection();
        $this->activationPrice = new ArrayCollection();
    }

    /**
     * @return Collection<int, TileCostGLM>
     */
    public function getBuyPrice(): Collection
    {
        return $this->buyPrice;
    }

    public function addBuyPrice(TileCostGLM $buyPrice): static
    {
        if (!$this->buyPrice->contains($buyPrice)) {
            $this->buyPrice->add($buyPrice);
            $buyPrice->setTileGLM($this);
        }

        return $this;
    }

    public function removeBuyPrice(TileCostGLM $buyPrice): static
    {
        if ($this->buyPrice->removeElement($buyPrice)) {
            // set the owning side to null (unless already changed)
            if ($buyPrice->getTileGLM() === $this) {
                $buyPrice->setTileGLM(null);
            }
        }

        return $this;
    }

    public function getBuyBonus(): ?TileBonusGLM
    {
        return $this->buyBonus;
    }

    public function setBuyBonus(?TileBonusGLM $buyBonus): static
    {
        $this->buyBonus = $buyBonus;

        return $this;
    }

    /**
     * @return Collection<int, TileCostGLM>
     */
    public function getActivationPrice(): Collection
    {
        return $this->activationPrice;
    }

    public function addActivationPrice(TileCostGLM $activationPrice): static
    {
        if (!$this->activationPrice->contains($activationPrice)) {
            $this->activationPrice->add($activationPrice);
            $activationPrice->setTileBonusGLM($this);
        }

        return $this;
    }

    public function removeActivationPrice(TileCostGLM $activationPrice): static
    {
        if ($this->activationPrice->removeElement($activationPrice)) {
            // set the owning side to null (unless already changed)
            if ($activationPrice->getTileBonusGLM() === $this) {
                $activationPrice->setTileBonusGLM(null);
            }
        }

        return $this;
    }

    public function getActivationBonus(): ?TileBonusGLM
    {
        return $this->activationBonus;
    }

    public function setActivationBonus(TileBonusGLM $activationBonus): static
    {
        $this->activationBonus = $activationBonus;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isContainingRiver(): ?bool
    {
        return $this->containingRiver;
    }

    public function setContainingRiver(bool $containingRiver): static
    {
        $this->containingRiver = $containingRiver;

        return $this;
    }

    public function isContainingRoad(): ?bool
    {
        return $this->containingRoad;
    }

    public function setContainingRoad(bool $containingRoad): static
    {
        $this->containingRoad = $containingRoad;

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

    public function getCard(): ?CardGLM
    {
        return $this->card;
    }

    public function setCard(?CardGLM $card): static
    {
        $this->card = $card;

        return $this;
    }
}
