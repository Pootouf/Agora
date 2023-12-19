<?php

namespace App\Entity\Game\SixQP;

use App\Repository\Game\SixQP\PlayerSixQPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerSixQPRepository::class)]
class PlayerSixQP
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'playerSixQPs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSixQP $game = null;

    #[ORM\ManyToMany(targetEntity: CardSixQP::class)]
    private Collection $cards;

    #[ORM\OneToOne(mappedBy: 'player', cascade: ['persist', 'remove'])]
    private ?ChosenCardSixQP $chosenCardSixQP = null;

    #[ORM\OneToOne(mappedBy: 'player', cascade: ['persist', 'remove'])]
    private ?DiscardSixQP $discardSixQP = null;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getChosenCardSixQP(): ?ChosenCardSixQP
    {
        return $this->chosenCardSixQP;
    }

    public function setChosenCardSixQP(ChosenCardSixQP $chosenCardSixQP): static
    {
        // set the owning side of the relation if necessary
        if ($chosenCardSixQP->getPlayer() !== $this) {
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
