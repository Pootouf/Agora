<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\BoardTileGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoardTileGLMRepository::class)]
class BoardTileGLM extends Component
{
    #[ORM\Column]
    private ?int $position = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileGLM $tile = null;

    #[ORM\ManyToOne(inversedBy: 'boardTiles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MainBoardGLM $mainBoardGLM = null;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

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
