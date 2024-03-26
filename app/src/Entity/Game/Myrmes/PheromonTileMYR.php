<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\PheromonTileMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PheromonTileMYRRepository::class)]
class PheromonTileMYR extends Component
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileMYR $tile = null;

    #[ORM\ManyToOne]
    private ?ResourceMYR $resource = null;

    #[ORM\ManyToOne(inversedBy: 'pheromonTiles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PheromonMYR $pheromonMYR = null;

    public function getTile(): ?TileMYR
    {
        return $this->tile;
    }

    public function setTile(?TileMYR $tile): static
    {
        $this->tile = $tile;

        return $this;
    }

    public function getResource(): ?ResourceMYR
    {
        return $this->resource;
    }

    public function setResource(?ResourceMYR $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function getPheromonMYR(): ?PheromonMYR
    {
        return $this->pheromonMYR;
    }

    public function setPheromonMYR(?PheromonMYR $pheromonMYR): static
    {
        $this->pheromonMYR = $pheromonMYR;

        return $this;
    }
}
