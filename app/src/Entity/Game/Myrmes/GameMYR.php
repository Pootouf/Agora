<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\GameMYRRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameMYRRepository::class)]
class GameMYR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'gameMYR', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerMYR $firstPlayer = null;

    #[ORM\OneToMany(targetEntity: PlayerMYR::class, mappedBy: 'gameMYR', orphanRemoval: true)]
    private Collection $players;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstPlayer(): ?PlayerMYR
    {
        return $this->firstPlayer;
    }

    public function setFirstPlayer(PlayerMYR $firstPlayer): static
    {
        $this->firstPlayer = $firstPlayer;

        return $this;
    }

    /**
     * @return Collection<int, PlayerMYR>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(PlayerMYR $player): static
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setGameMYR($this);
        }

        return $this;
    }

    public function removePlayer(PlayerMYR $player): static
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGameMYR() === $this) {
                $player->setGameMYR(null);
            }
        }

        return $this;
    }
}
