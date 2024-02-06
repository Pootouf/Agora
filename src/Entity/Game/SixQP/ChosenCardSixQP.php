<?php

namespace App\Entity\Game\SixQP;

use App\Entity\Game\DTO\Card;
use App\Entity\Game\DTO\Component;

use App\Entity\Game\SixQP\CardSixQP;
use App\Entity\Game\SixQP\PlayerSixQP;
use App\Entity\Game\SixQP\GameSixQP;

use App\Repository\Game\SixQP\ChosenCardSixQPRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChosenCardSixQPRepository::class)]
class ChosenCardSixQP extends Component
{

    #[ORM\OneToOne(inversedBy: 'chosenCardSixQP')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerSixQP $player = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSixQP $game = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CardSixQP $card = null;

    #[ORM\Column]
    private ?bool $visible = null;

    public function __construct(PlayerSixQP $player, GameSixQP $game, CardSixQP $card, bool $visible) {
        $this -> player = $player;
        $this -> game = $game;
        $this -> card = $card;
        $this -> visible = $visible;
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

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;

        return $this;
    }
}
