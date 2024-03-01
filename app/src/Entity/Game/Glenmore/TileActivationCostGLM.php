<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\TileActivationCostGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TileActivationCostGLMRepository::class)]
class TileActivationCostGLM extends Component
{
    #[ORM\ManyToOne(targetEntity: ResourceGLM::class)]
    private ResourceGLM $resource;

    #[ORM\Column]
    private ?int $price = null;

    /**
     * @return ResourceGLM
     */
    public function getResource(): ResourceGLM
    {
        return $this->resource;
    }

    public function setResource(ResourceGLM $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

}
