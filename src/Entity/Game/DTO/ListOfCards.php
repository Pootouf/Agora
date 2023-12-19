<?php

namespace App\Entity\Game\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class ListOfCards
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'listOfCards', targetEntity: Card::class)]
    protected Collection $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): static
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
            $card->setListOfCards($this);
        }

        return $this;
    }

    public function removeCard(Card $card): static
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getListOfCards() === $this) {
                $card->setListOfCards(null);
            }
        }

        return $this;
    }
}
