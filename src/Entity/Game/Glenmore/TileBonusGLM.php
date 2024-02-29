<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\TileBonusGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TileBonusGLMRepository::class)]
class TileBonusGLM extends Component
{
    #[ORM\ManyToMany(targetEntity: ResourceGLM::class)]
    private Collection $resource;

    #[ORM\Column]
    private ?int $bonus = null;

    public function __construct()
    {
        $this->resource = new ArrayCollection();
    }

    /**
     * @return Collection<int, ResourceGLM>
     */
    public function getResource(): Collection
    {
        return $this->resource;
    }

    public function addResource(ResourceGLM $resource): static
    {
        if (!$this->resource->contains($resource)) {
            $this->resource->add($resource);
        }

        return $this;
    }

    public function removeResource(ResourceGLM $resource): static
    {
        $this->resource->removeElement($resource);

        return $this;
    }

    public function getBonus(): ?int
    {
        return $this->bonus;
    }

    public function setBonus(int $bonus): static
    {
        $this->bonus = $bonus;

        return $this;
    }
}
