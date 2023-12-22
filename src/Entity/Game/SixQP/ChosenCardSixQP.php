<?php

namespace App\Entity\Game\SixQP;

use App\Entity\Game\DTO\Card;
use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChosenCardSixQPRepository::class)]
class ChosenCardSixQP extends Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'chosenCardSixQP', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerSixQP $player = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSixQP $game = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CardSixQP $card = null;

    #[ORM\Column]
    private ?bool $state = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?PlayerSixQP
    {
        return $this->player;
    }

    public function setPlayer(PlayerSixQP $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getGame(): ?GameSixQP
    {
        return $this->game;
    }

    public function setGame(?GameSixQP $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getCard(): ?CardSixQP
    {
        return $this->card;
    }

    public function setCard(?CardSixQP $card): static
    {
        $this->card = $card;

        return $this;
    }

    public function isState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): static
    {
        $this->state = $state;

        return $this;
    }
}
