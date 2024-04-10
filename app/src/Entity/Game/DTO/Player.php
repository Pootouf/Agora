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

    #[ORM\Column(length: 255)]
    protected ?string $username = null;

    #[ORM\Column(nullable: true)]
    protected ?bool $turnOfPlayer = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function isTurnOfPlayer() : ?bool
    {
        return $this->turnOfPlayer;
    }

    public function setTurnOfPlayer(bool $turnOfPlayer) : static
    {
        $this->turnOfPlayer = $turnOfPlayer;

        return $this;
    }
}
