<?php

namespace App\Entity\Game\SPL;

use App\Entity\Game\DTO\Game;
use App\Repository\Game\SPL\GameSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameSPLRepository::class)]
class GameSPL extends Game
{
    #[ORM\OneToMany(targetEntity: PlayerSPL::class, mappedBy: 'gameSPL', orphanRemoval: true)]
    private Collection $players;

    #[ORM\OneToMany(targetEntity: PersonalBoardSPL::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $personalBoardsSPL;

    #[ORM\OneToMany(targetEntity: DrawCardsSPL::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $drawCardsSPL;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->personalBoardsSPL = new ArrayCollection();
        $this->drawCardsSPL = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGameSPL() === $this) {
                $player->setGameSPL(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PersonalBoardSPL>
     */
    public function getPersonalBoardsSPL(): Collection
    {
        return $this->personalBoardsSPL;
    }

    public function addPersonalBoardsSPL(PersonalBoardSPL $personalBoardsSPL): static
    {
        if (!$this->personalBoardsSPL->contains($personalBoardsSPL)) {
            $this->personalBoardsSPL->add($personalBoardsSPL);
            $personalBoardsSPL->setGame($this);
        }

        return $this;
    }

    public function removePersonalBoardsSPL(PersonalBoardSPL $personalBoardsSPL): static
    {
        if ($this->personalBoardsSPL->removeElement($personalBoardsSPL)) {
            // set the owning side to null (unless already changed)
            if ($personalBoardsSPL->getGame() === $this) {
                $personalBoardsSPL->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DrawCardsSPL>
     */
    public function getDrawCardsSPL(): Collection
    {
        return $this->drawCardsSPL;
    }

    public function addDrawCardsSPL(DrawCardsSPL $drawCardsSPL): static
    {
        if (!$this->drawCardsSPL->contains($drawCardsSPL)) {
            $this->drawCardsSPL->add($drawCardsSPL);
            $drawCardsSPL->setGame($this);
        }

        return $this;
    }

    public function removeDrawCardsSPL(DrawCardsSPL $drawCardsSPL): static
    {
        if ($this->drawCardsSPL->removeElement($drawCardsSPL)) {
            // set the owning side to null (unless already changed)
            if ($drawCardsSPL->getGame() === $this) {
                $drawCardsSPL->setGame(null);
            }
        }

        return $this;
    }
}
