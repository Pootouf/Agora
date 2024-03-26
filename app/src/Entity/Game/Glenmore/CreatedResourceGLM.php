<?php

namespace App\Entity\Game\Glenmore;

use App\Repository\Game\Glenmore\CreatedResourceGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreatedResourceGLMRepository::class)]
class CreatedResourceGLM
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ResourceGLM $resource = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'createdResources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PersonalBoardGLM $personalBoardGLM = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResource(): ?ResourceGLM
    {
        return $this->resource;
    }

    public function setResource(?ResourceGLM $resource): static
    {
        $this->resource = $resource;

        return $this;
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

    public function getPersonalBoardGLM(): ?PersonalBoardGLM
    {
        return $this->personalBoardGLM;
    }

    public function setPersonalBoardGLM(?PersonalBoardGLM $personalBoardGLM): static
    {
        $this->personalBoardGLM = $personalBoardGLM;

        return $this;
    }
}
