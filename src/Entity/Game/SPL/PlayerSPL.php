<?php

namespace App\Entity\Game\SPL;

use App\Repository\Game\SPL\PlayerSPLRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerSPLRepository::class)]
class PlayerSPL
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'playerSPL', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardSPL $personalBoard = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSPL $gameSPL = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonalBoard(): ?PersonalBoardSPL
    {
        return $this->personalBoard;
    }

    public function setPersonalBoard(PersonalBoardSPL $personalBoard): static
    {
        $this->personalBoard = $personalBoard;

        return $this;
    }

    public function getGameSPL(): ?GameSPL
    {
        return $this->gameSPL;
    }

    public function setGameSPL(?GameSPL $gameSPL): static
    {
        $this->gameSPL = $gameSPL;

        return $this;
    }
}
