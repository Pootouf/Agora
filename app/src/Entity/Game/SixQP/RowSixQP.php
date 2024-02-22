<?php

namespace App\Entity\Game\SixQP;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\SixQP\RowSixQPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RowSixQPRepository::class)]
class RowSixQP extends Component
{

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'rowSixQPs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSixQP $game = null;

    #[ORM\ManyToMany(targetEntity: CardSixQP::class)]
    private Collection $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

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

    public function isCardInRow(CardSixQP $card): bool
    {
        return $this->cards->contains($card);
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
}
