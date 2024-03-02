<?php

namespace App\Entity\Game\Glenmore;

use App\Repository\Game\Glenmore\PlayerTileResourceGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerTileResourceGLMRepository::class)]
class PlayerTileResourceGLM
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

    #[ORM\ManyToOne(inversedBy: 'playerTileResource')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerTileGLM $playerTileGLM = null;

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

    public function getPlayerTileGLM(): ?PlayerTileGLM
    {
        return $this->playerTileGLM;
    }

    public function setPlayerTileGLM(?PlayerTileGLM $playerTileGLM): static
    {
        $this->playerTileGLM = $playerTileGLM;

        return $this;
    }
}
