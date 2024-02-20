<?php

namespace App\Entity\Game\Splendor;

use App\Repository\Game\Splendor\PlayerCardSPLRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerCardSPLRepository::class)]
class PlayerCardSPL
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DevelopmentCardsSPL $developmentCard = null;

    #[ORM\Column]
    private ?bool $isReserved = null;

    #[ORM\ManyToOne(inversedBy: 'playerCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardSPL $personalBoardSPL = null;

    public function __construct(PlayerSPL $player, DevelopmentCardsSPL $card, bool $reserved) {
        $this->developmentCard = $card;
        $this->personalBoardSPL = $player->getPersonalBoard();
        $this->isReserved = $reserved;
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

    public function isIsReserved(): ?bool
    {
        return $this->isReserved;
    }

    public function setIsReserved(bool $isReserved): static
    {
        $this->isReserved = $isReserved;

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
