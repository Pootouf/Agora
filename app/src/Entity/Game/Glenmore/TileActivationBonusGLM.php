<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\TileActivationBonusGLMRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TileActivationBonusGLMRepository::class)]
class TileActivationBonusGLM extends Component
{
    #[ORM\ManyToOne(targetEntity: ResourceGLM::class)]
    private ResourceGLM $resource;

    #[ORM\Column]
    private ?int $amount = null;

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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }
}
