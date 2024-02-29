<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\TileCostGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TileCostGLMRepository::class)]
class TileCostGLM extends Component
{
    #[ORM\ManyToMany(targetEntity: ResourceGLM::class)]
    private Collection $resource;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'buyPrice')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileGLM $tileGLM = null;

    #[ORM\ManyToOne(inversedBy: 'activationPrice')]
    private ?TileGLM $tileBonusGLM = null;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTileGLM(): ?TileGLM
    {
        return $this->tileGLM;
    }

    public function setTileGLM(?TileGLM $tileGLM): static
    {
        $this->tileGLM = $tileGLM;

        return $this;
    }

    public function getTileBonusGLM(): ?TileGLM
    {
        return $this->tileBonusGLM;
    }

    public function setTileBonusGLM(?TileGLM $tileBonusGLM): static
    {
        $this->tileBonusGLM = $tileBonusGLM;

        return $this;
    }
}
