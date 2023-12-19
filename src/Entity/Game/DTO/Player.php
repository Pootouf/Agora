<?php

namespace App\Entity\Game\DTO;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'player', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    protected ?PersonalBoard $personalBoard = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Game $game = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonalBoard(): ?PersonalBoard
    {
        return $this->personalBoard;
    }

    public function setPersonalBoard(PersonalBoard $personalBoard): static
    {
        $this->personalBoard = $personalBoard;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }
}
