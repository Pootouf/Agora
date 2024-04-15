<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\PheromonMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PheromonMYRRepository::class)]
class PheromonMYR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: PheromonTileMYR::class, mappedBy: 'pheromonMYR', orphanRemoval: true)]
    private Collection $pheromonTiles;

    #[ORM\Column]
    private ?bool $harvested = null;

    #[ORM\ManyToOne(inversedBy: 'pheromonMYRs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?PlayerMYR $player = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileTypeMYR $type = null;

    public function __construct()
    {
        $this->pheromonTiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, PheromonTileMYR>
     */
    public function getPheromonTiles(): Collection
    {
        return $this->pheromonTiles;
    }

    public function addPheromonTile(PheromonTileMYR $pheromonTile): static
    {
        if (!$this->pheromonTiles->contains($pheromonTile)) {
            $this->pheromonTiles->add($pheromonTile);
            $pheromonTile->setPheromonMYR($this);
        }

        return $this;
    }

    public function removePheromonTile(PheromonTileMYR $pheromonTile): static
    {
        if ($this->pheromonTiles->removeElement($pheromonTile)) {
            // set the owning side to null (unless already changed)
            if ($pheromonTile->getPheromonMYR() === $this) {
                $pheromonTile->setPheromonMYR(null);
            }
        }

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

    public function getPlayer(): ?PlayerMYR
    {
        return $this->player;
    }

    public function setPlayer(?PlayerMYR $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getType(): ?TileTypeMYR
    {
        return $this->type;
    }

    public function setType(?TileTypeMYR $type): static
    {
        $this->type = $type;

        return $this;
    }
}
