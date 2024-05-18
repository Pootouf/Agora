<?php

namespace App\Entity\Game\Glenmore;

use App\Repository\Game\Glenmore\BuyingTileGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuyingTileGLMRepository::class)]
class BuyingTileGLM
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?BoardTileGLM $boardTile = null;

    #[ORM\OneToOne(mappedBy: 'buyingTile')]
    private ?PersonalBoardGLM $personalBoardGLM = null;

    #[ORM\Column(nullable: true)]
    private ?int $coordX = null;

    #[ORM\Column(nullable: true)]
    private ?int $coordY = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBoardTile(): ?BoardTileGLM
    {
        return $this->boardTile;
    }

    public function setBoardTile(BoardTileGLM $boardTile): static
    {
        $this->boardTile = $boardTile;

        return $this;
    }

    public function getPersonalBoardGLM(): ?PersonalBoardGLM
    {
        return $this->personalBoardGLM;
    }

    public function setPersonalBoardGLM(?PersonalBoardGLM $personalBoardGLM): static
    {
        // unset the owning side of the relation if necessary
        if ($personalBoardGLM === null && $this->personalBoardGLM !== null) {
            $this->personalBoardGLM->setBuyingTile(null);
        }

        // set the owning side of the relation if necessary
        if ($personalBoardGLM !== null && $personalBoardGLM->getBuyingTile() !== $this) {
            $personalBoardGLM->setBuyingTile($this);
        }

        $this->personalBoardGLM = $personalBoardGLM;

        return $this;
    }

    public function getCoordX(): ?int
    {
        return $this->coordX;
    }

    public function setCoordX(?int $coordX): static
    {
        $this->coordX = $coordX;

        return $this;
    }

    public function getCoordY(): ?int
    {
        return $this->coordY;
    }

    public function setCoordY(?int $coordY): static
    {
        $this->coordY = $coordY;

        return $this;
    }
}
