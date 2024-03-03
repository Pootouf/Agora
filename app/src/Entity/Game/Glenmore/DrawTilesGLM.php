<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\DrawTilesGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrawTilesGLMRepository::class)]
class DrawTilesGLM extends Component
{
    #[ORM\Column]
    private ?int $level = null;

    #[ORM\ManyToMany(targetEntity: TileGLM::class)]
    private Collection $tiles;

    #[ORM\ManyToOne(inversedBy: 'drawTiles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MainBoardGLM $mainBoardGLM = null;

    public function __construct()
    {
        $this->tiles = new ArrayCollection();
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

    /**
     * @return Collection<int, TileGLM>
     */
    public function getTiles(): Collection
    {
        return $this->tiles;
    }

    public function addTile(TileGLM $tile): static
    {
        if (!$this->tiles->contains($tile)) {
            $this->tiles->add($tile);
        }

        return $this;
    }

    public function removeTile(TileGLM $tile): static
    {
        $this->tiles->removeElement($tile);

        return $this;
    }

    public function getMainBoardGLM(): ?MainBoardGLM
    {
        return $this->mainBoardGLM;
    }

    public function setMainBoardGLM(?MainBoardGLM $mainBoardGLM): static
    {
        $this->mainBoardGLM = $mainBoardGLM;

        return $this;
    }
}
