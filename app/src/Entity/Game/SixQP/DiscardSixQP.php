<?php

namespace App\Entity\Game\SixQP;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\SixQP\DiscardSixQPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscardSixQPRepository::class)]
class DiscardSixQP extends Component
{
    #[ORM\OneToOne(inversedBy: 'discardSixQP', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerSixQP $player;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSixQP $game;

    #[ORM\ManyToMany(targetEntity: CardSixQP::class)]
    private Collection $cards;


    public function __construct(PlayerSixQP $player, GameSixQP $game)
    {
        $this->player = $player;
        $this->game = $game;
        $this->cards = new ArrayCollection();
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

    /**
     * @return Collection<int, CardSixQP>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(CardSixQP $card): static
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
        }

        return $this;
    }

    public function removeCard(CardSixQP $card): static
    {
        $this->cards->removeElement($card);

        return $this;
    }
}
