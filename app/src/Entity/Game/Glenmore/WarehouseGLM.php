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

    #[ORM\OneToOne(mappedBy: 'warehouse', cascade: ['persist', 'remove'])]
    private ?MainBoardGLM $mainBoardGLM = null;

    #[ORM\OneToMany(targetEntity: WarehouseLineGLM::class, mappedBy: 'warehouseGLM', orphanRemoval: true)]
    private Collection $warehouseLine;

    public function __construct()
    {
        $this->warehouseLine = new ArrayCollection();
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
     * @return Collection<int, WarehouseLineGLM>
     */
    public function getWarehouseLine(): Collection
    {
        return $this->warehouseLine;
    }

    public function addWarehouseLine(WarehouseLineGLM $warehouseLine): static
    {
        if (!$this->warehouseLine->contains($warehouseLine)) {
            $this->warehouseLine->add($warehouseLine);
            $warehouseLine->setWarehouseGLM($this);
        }

        return $this;
    }

    public function removeWarehouseLine(WarehouseLineGLM $warehouseLine): static
    {
        if ($this->warehouseLine->removeElement($warehouseLine)
            && $warehouseLine->getWarehouseGLM() === $this) {
            $warehouseLine->setWarehouseGLM(null);
        }

        return $this;
    }
}
