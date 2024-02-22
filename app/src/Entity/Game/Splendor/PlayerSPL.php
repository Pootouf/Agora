<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Player;
use App\Repository\Game\Splendor\PlayerSPLRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerSPLRepository::class)]
class PlayerSPL extends Player
{

    #[ORM\OneToOne(inversedBy: 'playerSPL', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardSPL $personalBoard = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSPL $gameSPL = null;

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
