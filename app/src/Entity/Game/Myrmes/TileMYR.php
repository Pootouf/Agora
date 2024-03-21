<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Entity\Game\DTO\Tile;
use App\Repository\Game\Myrmes\TileMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TileMYRRepository::class)]
class TileMYR extends Component
{
    #[ORM\Column]
    private ?int $coord_X = null;

    #[ORM\Column]
    private ?int $coord_Y = null;

    public function getCoordX(): ?int
    {
        return $this->coord_X;
    }

    public function setCoordX(int $coord_X): static
    {
        $this->coord_X = $coord_X;

        return $this;
    }

    public function getCoordY(): ?int
    {
        return $this->coord_Y;
    }

    public function setCoordY(int $coord_Y): static
    {
        $this->coord_Y = $coord_Y;

        return $this;
    }
}
