<?php

namespace App\Entity\Game\DTO;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column]
    protected ?int $value = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    protected ?ListOfCards $listOfCards = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getListOfCards(): ?ListOfCards
    {
        return $this->listOfCards;
    }

    public function setListOfCards(?ListOfCards $listOfCards): static
    {
        $this->listOfCards = $listOfCards;

        return $this;
    }
}
