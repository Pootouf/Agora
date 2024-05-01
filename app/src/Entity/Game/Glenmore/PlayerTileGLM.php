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

    #[ORM\Column]
    private ?int $coordX = null;

    #[ORM\Column]
    private ?int $coordY = null;

    #[ORM\Column]
    private ?bool $activated = false;

    #[ORM\OneToMany(targetEntity: SelectedResourceGLM::class,
        mappedBy: 'playerTile',
        orphanRemoval: true,
        cascade: ["persist"])]
    private Collection $selectedResources;

    public function __construct()
    {
        $this->adjacentTiles = new ArrayCollection();
        $this->playerTileResource = new ArrayCollection();
        $this->selectedResources = new ArrayCollection();
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

    public function addAdjacentTile(self $adjacentTile, int $direction): static
    {
        if (!$this->adjacentTiles->contains($adjacentTile)) {
            $this->adjacentTiles->set($direction, $adjacentTile);
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
        if ($this->playerTileResource->removeElement($playerTileResource)
            && $playerTileResource->getPlayerTileGLM() === $this) {
            $playerTileResource->setPlayerTileGLM(null);
        }

        return $this;
    }

    public function getCoordX(): ?int
    {
        return $this->coordX;
    }

    public function setCoordX(int $coordX): static
    {
        $this->coordX = $coordX;

        return $this;
    }

    public function getCoordY(): ?int
    {
        return $this->coordY;
    }

    public function setCoordY(int $coordY): static
    {
        $this->coordY = $coordY;

        return $this;
    }

    public function isActivated(): ?bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): static
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * @return Collection<int, SelectedResourceGLM>
     */
    public function getSelectedResources(): Collection
    {
        return $this->selectedResources;
    }

    public function addSelectedResource(SelectedResourceGLM $selectedResource): static
    {
        if (!$this->selectedResources->contains($selectedResource)) {
            $this->selectedResources->add($selectedResource);
            $selectedResource->setPlayerTile($this);
        }

        return $this;
    }

    public function removeSelectedResource(SelectedResourceGLM $selectedResource): static
    {
        if ($this->selectedResources->removeElement($selectedResource)
            && $selectedResource->getPlayerTile() === $this) {
            $selectedResource->setPlayerTile(null);
        }

        return $this;
    }
}
