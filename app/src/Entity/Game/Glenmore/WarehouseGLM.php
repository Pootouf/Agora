<?php

namespace App\Entity\Game\Glenmore;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Glenmore\WarehouseGLMRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarehouseGLMRepository::class)]
class WarehouseGLM extends Component
{

    #[ORM\ManyToMany(targetEntity: ResourceGLM::class)]
    private Collection $resources;

    #[ORM\OneToOne(mappedBy: 'warehouse', cascade: ['persist', 'remove'])]
    private ?MainBoardGLM $mainBoardGLM = null;

    #[ORM\OneToMany(targetEntity: WarehouseResourceGLM::class, mappedBy: 'warehouse', orphanRemoval: true)]
    private Collection $warehouseResource;

    public function __construct()
    {
        $this->resources = new ArrayCollection();
        $this->warehouseResource = new ArrayCollection();
    }

    /**
     * @return Collection<int, ResourceGLM>
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function addResource(ResourceGLM $resource): static
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
        }

        return $this;
    }

    public function removeResource(ResourceGLM $resource): static
    {
        $this->resources->removeElement($resource);

        return $this;
    }

    public function getMainBoardGLM(): ?MainBoardGLM
    {
        return $this->mainBoardGLM;
    }

    public function setMainBoardGLM(MainBoardGLM $mainBoardGLM): static
    {
        // set the owning side of the relation if necessary
        if ($mainBoardGLM->getWarehouse() !== $this) {
            $mainBoardGLM->setWarehouse($this);
        }

        $this->mainBoardGLM = $mainBoardGLM;

        return $this;
    }

    /**
     * @return Collection<int, WarehouseResourceGLM>
     */
    public function getWarehouseResource(): Collection
    {
        return $this->warehouseResource;
    }

    public function addWarehouseResource(WarehouseResourceGLM $warehouseResource): static
    {
        if (!$this->warehouseResource->contains($warehouseResource)) {
            $this->warehouseResource->add($warehouseResource);
            $warehouseResource->setWarehouse($this);
        }

        return $this;
    }

    public function removeWarehouseResource(WarehouseResourceGLM $warehouseResource): static
    {
        if ($this->warehouseResource->removeElement($warehouseResource)) {
            // set the owning side to null (unless already changed)
            if ($warehouseResource->getWarehouse() === $this) {
                $warehouseResource->setWarehouse(null);
            }
        }

        return $this;
    }
}
