<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\GardenTileMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GardenTileMYRRepository::class)]
class GardenTileMYR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileTypeMYR $type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ResourceMYR $resource = null;

    #[ORM\ManyToOne(inversedBy: 'gardenTileMYRs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerMYR $player = null;

    #[ORM\ManyToMany(targetEntity: TileMYR::class)]
    private Collection $tile;

    #[ORM\Column]
    private ?bool $harvested = null;

    public function __construct()
    {
        $this->tile = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?TileTypeMYR
    {
        return $this->type;
    }

    public function setType(TileTypeMYR $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getResource(): ?ResourceMYR
    {
        return $this->resource;
    }

    public function setResource(?ResourceMYR $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function getPlayer(): ?PlayerMYR
    {
        return $this->player;
    }

    public function setPlayer(?PlayerMYR $player): static
    {
        $this->player = $player;

        return $this;
    }

    /**
     * @return Collection<int, TileMYR>
     */
    public function getTile(): Collection
    {
        return $this->tile;
    }

    public function addTile(TileMYR $tile): static
    {
        if (!$this->tile->contains($tile)) {
            $this->tile->add($tile);
        }

        return $this;
    }

    public function removeTile(TileMYR $tile): static
    {
        $this->tile->removeElement($tile);

        return $this;
    }

    public function isHarvested(): ?bool
    {
        return $this->harvested;
    }

    public function setHarvested(bool $harvested): static
    {
        $this->harvested = $harvested;

        return $this;
    }
}
