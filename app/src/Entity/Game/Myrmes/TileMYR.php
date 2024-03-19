<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\TileMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TileMYRRepository::class)]
class TileMYR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $xMinCoord = null;

    #[ORM\Column]
    private ?int $xMaxCoord = null;

    #[ORM\Column]
    private ?int $yCoord = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileTypeMYR $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getXMinCoord(): ?int
    {
        return $this->xMinCoord;
    }

    public function setXMinCoord(int $xMinCoord): static
    {
        $this->xMinCoord = $xMinCoord;

        return $this;
    }

    public function getXMaxCoord(): ?int
    {
        return $this->xMaxCoord;
    }

    public function setXMaxCoord(int $xMaxCoord): static
    {
        $this->xMaxCoord = $xMaxCoord;

        return $this;
    }

    public function getYCoord(): ?int
    {
        return $this->yCoord;
    }

    public function setYCoord(int $yCoord): static
    {
        $this->yCoord = $yCoord;

        return $this;
    }

    public function getType(): ?TileTypeMYR
    {
        return $this->type;
    }

    public function setType(?TileTypeMYR $type): static
    {
        $this->type = $type;

        return $this;
    }
}
