<?php

namespace App\Entity\Game\Glenmore;

use App\Repository\Game\Glenmore\WarehouseLineGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarehouseLineGLMRepository::class)]
class WarehouseLineGLM
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

    #[ORM\Column]
    private ?int $coinNumber = null;

    #[ORM\ManyToOne(inversedBy: 'warehouseLine')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WarehouseGLM $warehouseGLM = null;

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

    public function getCoinNumber(): ?int
    {
        return $this->coinNumber;
    }

    public function setCoinNumber(int $coinNumber): static
    {
        $this->coinNumber = $coinNumber;

        return $this;
    }

    public function getWarehouseGLM(): ?WarehouseGLM
    {
        return $this->warehouseGLM;
    }

    public function setWarehouseGLM(?WarehouseGLM $warehouseGLM): static
    {
        $this->warehouseGLM = $warehouseGLM;

        return $this;
    }
}
