<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerResourceMYRRepository::class)]
class PlayerResourceMYR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ResourceMYR $resource = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getResource(): ?ResourceMYR
    {
        return $this->resource;
    }

    public function setResource(?ResourceMYR $resource): static
    {
        $this->resource = $resource;

        return $this;
    }
}
