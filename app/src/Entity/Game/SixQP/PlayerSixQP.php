<?php

namespace App\Entity\Game\SixQP;

use App\Entity\Game\DTO\Player;
use App\Repository\Game\SixQP\PlayerSixQPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerSixQPRepository::class)]
class PlayerSixQP extends Player
{
    #[ORM\ManyToOne(inversedBy: 'players')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSixQP $game;

    #[ORM\ManyToMany(targetEntity: CardSixQP::class)]
    private Collection $cards;

    #[ORM\OneToOne(mappedBy: 'player', cascade: ['persist', 'remove'])]
    private ?ChosenCardSixQP $chosenCardSixQP = null;

    #[ORM\OneToOne(mappedBy: 'player', cascade: ['persist', 'remove'])]
    private ?DiscardSixQP $discardSixQP = null;

    public function __construct(string $username, GameSixQP $gameSixQP)
    {
        $this->cards = new ArrayCollection();
        $this->username = $username;
        $this->game = $gameSixQP;
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

    public function clearCards(): static
    {
        $this->cards = new ArrayCollection();

        return $this;
    }

    public function getChosenCardSixQP(): ?ChosenCardSixQP
    {
        return $this->chosenCardSixQP;
    }

    public function setChosenCardSixQP(?ChosenCardSixQP $chosenCardSixQP): static
    {
        // set the owning side of the relation if necessary
        if ($chosenCardSixQP != null &&  $chosenCardSixQP->getPlayer() !== $this) {
            $chosenCardSixQP->setPlayer($this);
        }

        $this->chosenCardSixQP = $chosenCardSixQP;

        return $this;
    }

    public function getDiscardSixQP(): ?DiscardSixQP
    {
        return $this->discardSixQP;
    }

    public function setDiscardSixQP(DiscardSixQP $discardSixQP): static
    {
        // set the owning side of the relation if necessary
        if ($discardSixQP->getPlayer() !== $this) {
            $discardSixQP->setPlayer($this);
        }

        $this->discardSixQP = $discardSixQP;

        return $this;
    }
}
