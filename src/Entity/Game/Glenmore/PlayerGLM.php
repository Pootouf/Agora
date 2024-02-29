<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Player;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerGLMRepository::class)]
class PlayerGLM extends Player
{
    #[ORM\OneToOne(inversedBy: 'playerGLM', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardGLM $personalBoard = null;

    #[ORM\Column]
    private ?int $points = null;

    #[ORM\OneToOne(inversedBy: 'playerGLM', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PawnGLM $pawn = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameGLM $gameGLM = null;

    public function getPersonalBoard(): ?PersonalBoardGLM
    {
        return $this->personalBoard;
    }

    public function setPersonalBoard(PersonalBoardGLM $personalBoard): static
    {
        $this->personalBoard = $personalBoard;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getPawn(): ?PawnGLM
    {
        return $this->pawn;
    }

    public function setPawn(PawnGLM $pawn): static
    {
        $this->pawn = $pawn;

        return $this;
    }

    public function getGameGLM(): ?GameGLM
    {
        return $this->gameGLM;
    }

    public function setGameGLM(?GameGLM $gameGLM): static
    {
        $this->gameGLM = $gameGLM;

        return $this;
    }
}
