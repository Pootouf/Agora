<?php

namespace App\Entity\Game\SixQP;

use App\Entity\Game\DTO\Game;
use App\Repository\Game\SixQP\GameSixQPRepository;
use App\Service\Game\AbstractGameManagerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameSixQPRepository::class)]
class GameSixQP extends Game
{

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: RowSixQP::class, orphanRemoval: true)]
    private Collection $rowSixQPs;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: PlayerSixQP::class, orphanRemoval: true)]
    private Collection $players;

    public function __construct()
    {
        $this->setGameName(AbstractGameManagerService::SIXQP_LABEL);
        $this->rowSixQPs = new ArrayCollection();
        $this->players = new ArrayCollection();
    }

    /**
     * @return Collection<int, RowSixQP>
     */
    public function getRowSixQPs(): Collection
    {
        return $this->rowSixQPs;
    }

    public function addRowSixQP(RowSixQP $rowSixQP): static
    {
        if (!$this->rowSixQPs->contains($rowSixQP)) {
            $this->rowSixQPs->add($rowSixQP);
            $rowSixQP->setGame($this);
        }

        return $this;
    }

    public function removeRowSixQP(RowSixQP $rowSixQP): static
    {
        if ($this->rowSixQPs->removeElement($rowSixQP) && $rowSixQP->getGame() === $this) {
            $rowSixQP->setGame(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, PlayerSixQP>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(PlayerSixQP $player): static
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setGame($this);
        }

        return $this;
    }

    public function removePlayer(PlayerSixQP $player): static
    {
        if ($this->players->removeElement($player) && $player->getGame() === $this) {
            $player->setGame(null);
        }

        return $this;
    }
}
