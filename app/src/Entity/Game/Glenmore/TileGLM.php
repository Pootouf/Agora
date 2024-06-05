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
    #[ORM\ManyToMany(targetEntity: TileBuyCostGLM::class)]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $buyPrice;

    #[ORM\ManyToMany(targetEntity: TileBuyBonusGLM::class)]
    private Collection $buyBonus;

    #[ORM\ManyToMany(targetEntity: TileActivationCostGLM::class)]
    private Collection $activationPrice;

    #[ORM\ManyToMany(targetEntity: TileActivationBonusGLM::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Collection|null $activationBonus = null;

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
        $this->activationBonus = new ArrayCollection();
        $this->buyBonus = new ArrayCollection();
    }

    /**
     * @return Collection<int, TileBuyCostGLM>
     */
    public function getBuyPrice(): Collection
    {
        return $this->buyPrice;
    }

    public function addBuyPrice(TileBuyCostGLM $buyPrice): static
    {
        if (!$this->buyPrice->contains($buyPrice)) {
            $this->buyPrice->add($buyPrice);
        }

        return $this;
    }

    public function removeBuyPrice(TileBuyCostGLM $buyPrice): static
    {
        $this->buyPrice->removeElement($buyPrice);

        return $this;
    }

    /**
     * @return Collection<int, TileBuyBonusGLM>
     */
    public function getBuyBonus(): Collection
    {
        return $this->buyBonus;
    }

    public function addBuyBonus(TileBuyBonusGLM $buyBonus): static
    {
        if (!$this->buyBonus->contains($buyBonus)) {
            $this->buyBonus->add($buyBonus);
        }

        return $this;
    }

    public function removeBuyBonus(TileBuyBonusGLM $buyBonus): static
    {
        $this->buyBonus->removeElement($buyBonus);

        return $this;
    }

    /**
     * @return Collection<int, TileActivationCostGLM>
     */
    public function getActivationPrice(): Collection
    {
        return $this->activationPrice;
    }

    public function addActivationPrice(TileActivationCostGLM $activationPrice): static
    {
        if (!$this->activationPrice->contains($activationPrice)) {
            $this->activationPrice->add($activationPrice);
        }

        return $this;
    }

    public function removeActivationPrice(TileActivationCostGLM $activationPrice): static
    {
        $this->activationPrice->removeElement($activationPrice);

        return $this;
    }

    /**
     * @return Collection<int, TileActivationBonusGLM>
     */
    public function getActivationBonus(): Collection
    {
        return $this->activationBonus;
    }

    public function addActivationBonus(TileActivationBonusGLM $activationBonus): static
    {
        if (!$this->activationBonus->contains($activationBonus)) {
            $this->activationBonus->add($activationBonus);
        }

        return $this;
    }

    public function removeActivationBonus(TileActivationBonusGLM $activationBonus): static
    {
        $this->activationBonus->removeElement($activationBonus);

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
