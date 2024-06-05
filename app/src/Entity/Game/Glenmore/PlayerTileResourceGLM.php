<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\PlayerTileResourceGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerTileResourceGLMRepository::class)]
class PlayerTileResourceGLM extends Component
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ResourceGLM $resource = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'playerTileResource')]
    #[ORM\JoinColumn(nullable: true)]
    private ?PlayerTileGLM $playerTileGLM = null;

    #[ORM\ManyToOne(inversedBy: 'playerTileResourceGLMs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerGLM $player = null;
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

    public function getPlayer(): ?PlayerGLM
    {
        return $this->player;
    }

    public function setPlayer(?PlayerGLM $player): static
    {
        $this->player = $player;

        return $this;
    }
}
