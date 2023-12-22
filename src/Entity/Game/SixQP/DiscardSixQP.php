<?php

namespace App\Entity\Game\SixQP;

use App\Entity\Game\DTO\ListOfCards;
use App\Repository\Game\SixQP\DiscardSixQPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscardSixQPRepository::class)]
class DiscardSixQP extends ListOfCards
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'discardSixQP', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerSixQP $player = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSixQP $game = null;

    #[ORM\ManyToMany(targetEntity: CardSixQP::class)]
    private Collection $cards;

    #[ORM\Column]
    private ?int $totalPoints = null;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

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

    public function getTotalPoints(): ?int
    {
        return $this->totalPoints;
    }

    public function setTotalPoints(int $totalPoints): static
    {
        $this->totalPoints = $totalPoints;

        return $this;
    }
}
