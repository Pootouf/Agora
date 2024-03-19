<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\PlayerResourceMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerResourceMYRRepository::class)]
class PlayerResourceMYR extends Component
{

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ResourceMYR $resource = null;

    #[ORM\ManyToOne(inversedBy: 'playerResourceMYRs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardMYR $personalBoard = null;

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

    public function getPersonalBoard(): ?PersonalBoardMYR
    {
        return $this->personalBoard;
    }

    public function setPersonalBoard(?PersonalBoardMYR $personalBoard): static
    {
        $this->personalBoard = $personalBoard;

        return $this;
    }
}
