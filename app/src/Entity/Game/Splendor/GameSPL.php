<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Game;
use App\Repository\Game\Splendor\GameSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameSPLRepository::class)]
class GameSPL extends Game
{

    #[ORM\OneToMany(targetEntity: PlayerSPL::class, mappedBy: 'gameSPL', orphanRemoval: true)]
    private Collection $players;

    #[ORM\OneToOne(inversedBy: 'gameSPL', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?MainBoardSPL $mainBoard = null;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    /**
     * @return Collection<int, PlayerSPL>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(PlayerSPL $player): static
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setGameSPL($this);
        }

        return $this;
    }

    public function removePlayer(PlayerSPL $player): static
    {
        if ($this->players->removeElement($player) && $player->getGameSPL() === $this) {
            $player->setGameSPL(null);
        }

        return $this;
    }

    public function getMainBoard(): ?MainBoardSPL
    {
        return $this->mainBoard;
    }

    public function setMainBoard(MainBoardSPL $mainBoard): static
    {
        $this->mainBoard = $mainBoard;

        return $this;
    }
}
