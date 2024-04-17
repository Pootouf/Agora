<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Game;
use App\Repository\Game\Glenmore\GameGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameGLMRepository::class)]
class GameGLM extends Game
{
    #[ORM\OneToMany(mappedBy: 'game', targetEntity: PlayerGLM::class, orphanRemoval: true)]
    private Collection $players;

    #[ORM\OneToOne(inversedBy: 'gameGLM', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?MainBoardGLM $mainBoard = null;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    /**
     * @return Collection<int, PlayerGLM>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(PlayerGLM $player): static
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setGame($this);
        }

        return $this;
    }

    public function removePlayer(PlayerGLM $player): static
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGame() === $this) {
                $player->setGame(null);
            }
        }

        return $this;
    }

    public function getMainBoard(): ?MainBoardGLM
    {
        return $this->mainBoard;
    }

    public function setMainBoard(MainBoardGLM $mainBoard): static
    {
        $this->mainBoard = $mainBoard;

        return $this;
    }
}
