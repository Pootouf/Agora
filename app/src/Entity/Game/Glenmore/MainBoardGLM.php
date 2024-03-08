<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\MainBoardGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MainBoardGLMRepository::class)]
class MainBoardGLM extends Component
{
    #[ORM\OneToMany(targetEntity: BoardTileGLM::class, mappedBy: 'mainBoardGLM', orphanRemoval: true)]
    private Collection $boardTiles;

    #[ORM\OneToMany(targetEntity: DrawTilesGLM::class, mappedBy: 'mainBoardGLM', orphanRemoval: true)]
    private Collection $drawTiles;

    #[ORM\OneToOne(inversedBy: 'mainBoardGLM', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?WarehouseGLM $warehouse = null;

    #[ORM\OneToMany(targetEntity: PawnGLM::class, mappedBy: 'mainBoardGLM', orphanRemoval: true)]
    private Collection $pawns;

    #[ORM\OneToOne(mappedBy: 'mainBoard', cascade: ['persist', 'remove'])]
    private ?GameGLM $gameGLM = null;

    #[ORM\Column(nullable: true)]
    private ?int $lastPosition = null;

    public function __construct()
    {
        $this->boardTiles = new ArrayCollection();
        $this->drawTiles = new ArrayCollection();
        $this->pawns = new ArrayCollection();
    }

    /**
     * @return Collection<int, BoardTileGLM>
     */
    public function getBoardTiles(): Collection
    {
        return $this->boardTiles;
    }

    public function addBoardTile(BoardTileGLM $boardTile): static
    {
        if (!$this->boardTiles->contains($boardTile)) {
            $this->boardTiles->add($boardTile);
            $boardTile->setMainBoardGLM($this);
        }

        return $this;
    }

    public function removeBoardTile(BoardTileGLM $boardTile): static
    {
        if ($this->boardTiles->removeElement($boardTile)) {
            // set the owning side to null (unless already changed)
            if ($boardTile->getMainBoardGLM() === $this) {
                $boardTile->setMainBoardGLM(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DrawTilesGLM>
     */
    public function getDrawTiles(): Collection
    {
        return $this->drawTiles;
    }

    public function addDrawTile(DrawTilesGLM $drawTile): static
    {
        if (!$this->drawTiles->contains($drawTile)) {
            $this->drawTiles->add($drawTile);
            $drawTile->setMainBoardGLM($this);
        }

        return $this;
    }

    public function removeDrawTile(DrawTilesGLM $drawTile): static
    {
        if ($this->drawTiles->removeElement($drawTile)) {
            // set the owning side to null (unless already changed)
            if ($drawTile->getMainBoardGLM() === $this) {
                $drawTile->setMainBoardGLM(null);
            }
        }

        return $this;
    }

    public function getWarehouse(): ?WarehouseGLM
    {
        return $this->warehouse;
    }

    public function setWarehouse(WarehouseGLM $warehouse): static
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * @return Collection<int, PawnGLM>
     */
    public function getPawns(): Collection
    {
        return $this->pawns;
    }

    public function addPawn(PawnGLM $pawn): static
    {
        if (!$this->pawns->contains($pawn)) {
            $this->pawns->add($pawn);
            $pawn->setMainBoardGLM($this);
        }

        return $this;
    }

    public function removePawn(PawnGLM $pawn): static
    {
        if ($this->pawns->removeElement($pawn)) {
            // set the owning side to null (unless already changed)
            if ($pawn->getMainBoardGLM() === $this) {
                $pawn->setMainBoardGLM(null);
            }
        }

        return $this;
    }

    public function getGameGLM(): ?GameGLM
    {
        return $this->gameGLM;
    }

    public function setGameGLM(GameGLM $gameGLM): static
    {
        // set the owning side of the relation if necessary
        if ($gameGLM->getMainBoard() !== $this) {
            $gameGLM->setMainBoard($this);
        }

        $this->gameGLM = $gameGLM;

        return $this;
    }

    public function getLastPosition(): ?int
    {
        return $this->lastPosition;
    }

    public function setLastPosition(?int $lastPosition): static
    {
        $this->lastPosition = $lastPosition;

        return $this;
    }
}
