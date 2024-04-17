<?php

namespace App\Entity\Game\DTO;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
use phpDocumentor\Reflection\Types\Nullable;
use function PHPUnit\Framework\isNull;

#[MappedSuperclass]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "App\Generator\Game\PlayerIdGenerator")]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $username = null;

    #[ORM\Column(nullable: true)]
    protected ?bool $turnOfPlayer = null;

    #[ORM\Column]
    protected ?int $score = null;

    #[ORM\Column(nullable: false)]
    protected bool $excluded = false;

    public function isExcluded(): bool
    {
        return $this->excluded;
    }

    public function setExcluded(bool $excluded): void
    {
        $this->excluded = $excluded;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }
    public function addScore(int $score): static
    {
        $this->score += $score;

        return $this;
    }
}
