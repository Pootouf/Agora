<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\TileTypeMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TileTypeMYRRepository::class)]
class TileTypeMYR extends Component
{
    #[ORM\Column]
    private ?int $orientation = null;

    #[ORM\Column]
    private ?int $type = null;

    public function getOrientation(): ?int
    {
        return $this->orientation;
    }

    public function setOrientation(int $orientation): static
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }
}
