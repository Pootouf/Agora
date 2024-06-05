<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Pawn;
use App\Repository\Game\Glenmore\PawnGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PawnGLMRepository::class)]
class PawnGLM extends Pawn
{
    #[ORM\Column]
    private ?int $position = null;

    #[ORM\OneToOne(mappedBy: 'pawn', cascade: ['persist', 'remove'])]
    private ?PlayerGLM $playerGLM = null;

    #[ORM\ManyToOne(inversedBy: 'pawns')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MainBoardGLM $mainBoardGLM = null;

    #[ORM\Column]
    private ?bool $dice = false;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getPlayerGLM(): ?PlayerGLM
    {
        return $this->playerGLM;
    }

    public function setPlayerGLM(PlayerGLM $playerGLM): static
    {
        // set the owning side of the relation if necessary
        if ($playerGLM->getPawn() !== $this) {
            $playerGLM->setPawn($this);
        }

        $this->playerGLM = $playerGLM;

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

    public function isDice(): ?bool
    {
        return $this->dice;
    }

    public function setDice(bool $dice): static
    {
        $this->dice = $dice;

        return $this;
    }
}
