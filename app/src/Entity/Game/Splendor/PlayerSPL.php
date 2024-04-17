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
    private ?GameSPL $game = null;

    public function __construct(string $username, GameSPL $gameSPL)
    {
        $this->username = $username;
        $this->game = $gameSPL;
        $this->score = 0;
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

    public function getGame(): ?GameSPL
    {
        return $this->game;
    }

    public function setGame(?GameSPL $game): static
    {
        $this->game = $game;

        return $this;
    }
}
