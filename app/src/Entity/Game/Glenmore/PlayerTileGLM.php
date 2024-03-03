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

    #[ORM\ManyToMany(targetEntity: self::class)]
    private Collection $adjacentTiles;

    #[ORM\OneToMany(targetEntity: PlayerTileResourceGLM::class, mappedBy: 'playerTileGLM', orphanRemoval: true)]
    private Collection $playerTileResource;

    public function __construct()
    {
        $this->adjacentTiles = new ArrayCollection();
        $this->playerTileResource = new ArrayCollection();
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

    /**
     * @return Collection<int, PlayerTileResourceGLM>
     */
    public function getPlayerTileResource(): Collection
    {
        return $this->playerTileResource;
    }

    public function addPlayerTileResource(PlayerTileResourceGLM $playerTileResource): static
    {
        if (!$this->playerTileResource->contains($playerTileResource)) {
            $this->playerTileResource->add($playerTileResource);
            $playerTileResource->setPlayerTileGLM($this);
        }

        return $this;
    }

    public function removePlayerTileResource(PlayerTileResourceGLM $playerTileResource): static
    {
        if ($this->playerTileResource->removeElement($playerTileResource)) {
            // set the owning side to null (unless already changed)
            if ($playerTileResource->getPlayerTileGLM() === $this) {
                $playerTileResource->setPlayerTileGLM(null);
            }
        }

        return $this;
    }
}