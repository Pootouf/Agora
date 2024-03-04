<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\WarehouseResourceGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarehouseResourceGLMRepository::class)]
class WarehouseResourceGLM extends Component
{
    #[ORM\ManyToOne(inversedBy: 'warehouseResource')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WarehouseGLM $warehouse = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ResourceGLM $resource = null;

    public function __construct(WarehouseGLM $warehouse, ResourceGLM $resource)
    {
        $this->warehouse = $warehouse;
        $this->resource = $resource;
    }

    public function getWarehouse(): ?WarehouseGLM
    {
        return $this->warehouse;
    }

    public function setWarehouse(?WarehouseGLM $warehouse): static
    {
        $this->warehouse = $warehouse;

        return $this;
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
}
