<?php

namespace App\Entity\Game\DTO;

use App\Entity\Game\Help;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "App\Generator\Game\GameIdGenerator")]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column]
    protected bool $launched = false;

    #[ORM\Column]
    protected ?string $gameName = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function isLaunched(): ?bool
    {
        return $this->launched;
    }

    public function setLaunched(?bool $launched): void
    {
        $this->launched = $launched;
    }

    public function getGameName(): ?string
    {
        return $this->gameName;
    }

    public function setGameName(?string $gameName): void
    {
        $this->gameName = $gameName;
    }
}
