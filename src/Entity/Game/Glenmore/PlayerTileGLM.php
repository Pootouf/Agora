<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\PlayerTileGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerTileGLMRepository::class)]
class PlayerTileGLM extends Component
{

    #[ORM\ManyToOne(inversedBy: 'playerTiles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardGLM $personalBoard = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileGLM $tile = null;

    #[ORM\ManyToMany(targetEntity: ResourceGLM::class)]
    private Collection $resources;

    #[ORM\ManyToMany(targetEntity: self::class)]
    private Collection $adjacentTiles;

    public function __construct()
    {
        $this->resources = new ArrayCollection();
        $this->adjacentTiles = new ArrayCollection();
    }

    public function getPersonalBoard(): ?PersonalBoardGLM
    {
        return $this->personalBoard;
    }

    public function setPersonalBoard(?PersonalBoardGLM $personalBoard): static
    {
        $this->personalBoard = $personalBoard;

        return $this;
    }

    public function getTile(): ?TileGLM
    {
        return $this->tile;
    }

    public function setTile(?TileGLM $tile): static
    {
        $this->tile = $tile;

        return $this;
    }

    /**
     * @return Collection<int, ResourceGLM>
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function addResource(ResourceGLM $resource): static
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
        }

        return $this;
    }

    public function removeResource(ResourceGLM $resource): static
    {
        $this->resources->removeElement($resource);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAdjacentTiles(): Collection
    {
        return $this->adjacentTiles;
    }

    public function addAdjacentTile(self $adjacentTile): static
    {
        if (!$this->adjacentTiles->contains($adjacentTile)) {
            $this->adjacentTiles->add($adjacentTile);
        }

        return $this;
    }

    public function removeAdjacentTile(self $adjacentTile): static
    {
        $this->adjacentTiles->removeElement($adjacentTile);

        return $this;
    }
}
