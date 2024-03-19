<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\GardenWorkerMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GardenWorkerMYRRepository::class)]
class GardenWorkerMYR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'gardenWorkerMYRs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerMYR $player = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileMYR $tile = null;

    #[ORM\Column]
    private ?int $shiftsCount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?PlayerMYR
    {
        return $this->player;
    }

    public function setPlayer(?PlayerMYR $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getTile(): ?TileMYR
    {
        return $this->tile;
    }

    public function setTile(?TileMYR $tile): static
    {
        $this->tile = $tile;

        return $this;
    }

    public function getShiftsCount(): ?int
    {
        return $this->shiftsCount;
    }

    public function setShiftsCount(int $shiftsCount): static
    {
        $this->shiftsCount = $shiftsCount;

        return $this;
    }
}
