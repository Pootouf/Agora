<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Splendor\PlayerCardSPLRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerCardSPLRepository::class)]
class PlayerCardSPL extends Component
{
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?DevelopmentCardsSPL $developmentCard = null;

    #[ORM\ManyToOne(inversedBy: 'playerCardsSPL')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSPL $game = null;

    #[ORM\OneToOne(inversedBy: 'playerCardSPL', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardSPL $personalBoard = null;

    #[ORM\Column]
    private ?bool $reserved = null;

    #[ORM\ManyToOne(inversedBy: 'playerCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardSPL $personalBoardSPL = null;

    public function __construct(PlayerSPL $player, DevelopmentCardsSPL $card, bool $reserved) {
        $this->developmentCard = $card;
        $this->game = $player->getGameSPL();
        $this->personalBoard = $player->getPersonalBoard();
        $this->reserved = $reserved;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevelopmentCard(): ?DevelopmentCardsSPL
    {
        return $this->developmentCard;
    }

    public function setDevelopmentCard(?DevelopmentCardsSPL $developmentCard): static
    {
        $this->developmentCard = $developmentCard;

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

    public function getPersonalBoard(): ?PersonalBoardSPL
    {
        return $this->personalBoard;
    }

    public function setPersonalBoard(PersonalBoardSPL $personalBoard): static
    {
        $this->personalBoard = $personalBoard;

        return $this;
    }

    public function isReserved(): ?bool
    {
        return $this->reserved;
    }

    public function setReserved(bool $reserved): static
    {
        $this->reserved = $reserved;

        return $this;
    }

    public function getPersonalBoardSPL(): ?PersonalBoardSPL
    {
        return $this->personalBoardSPL;
    }

    public function setPersonalBoardSPL(?PersonalBoardSPL $personalBoardSPL): static
    {
        $this->personalBoardSPL = $personalBoardSPL;

        return $this;
    }
}
