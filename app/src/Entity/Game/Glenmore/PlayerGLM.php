<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Player;
use App\Repository\Game\Glenmore\PlayerGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(targetEntity: PlayerTileResourceGLM::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $playerTileResourceGLMs;

    #[ORM\Column]
    private ?int $roundPhase = null;

    public function __construct(string $username, GameGLM $game)
    {
        $this->username = $username;
        $this->gameGLM = $game;
        $this->playerTileResourceGLMs = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, PlayerTileResourceGLM>
     */
    public function getPlayerTileResourceGLMs(): Collection
    {
        return $this->playerTileResourceGLMs;
    }

    public function addPlayerTileResourceGLM(PlayerTileResourceGLM $playerTileResourceGLM): static
    {
        if (!$this->playerTileResourceGLMs->contains($playerTileResourceGLM)) {
            $this->playerTileResourceGLMs->add($playerTileResourceGLM);
            $playerTileResourceGLM->setPlayer($this);
        }

        return $this;
    }

    public function removePlayerTileResourceGLM(PlayerTileResourceGLM $playerTileResourceGLM): static
    {
        if ($this->playerTileResourceGLMs->removeElement($playerTileResourceGLM)) {
            // set the owning side to null (unless already changed)
            if ($playerTileResourceGLM->getPlayer() === $this) {
                $playerTileResourceGLM->setPlayer(null);
            }
        }

        return $this;
    }

    public function getRoundPhase(): ?int
    {
        return $this->roundPhase;
    }

    public function setRoundPhase(int $roundPhase): static
    {
        $this->roundPhase = $roundPhase;

        return $this;
    }
}
