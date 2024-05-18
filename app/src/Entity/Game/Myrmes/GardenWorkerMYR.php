<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\GardenWorkerMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GardenWorkerMYRRepository::class)]
class GardenWorkerMYR extends Component
{
    #[ORM\ManyToOne(inversedBy: 'gardenWorkerMYRs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerMYR $player = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileMYR $tile = null;

    #[ORM\Column]
    private ?int $shiftsCount = null;

    #[ORM\ManyToOne(inversedBy: 'gardenWorkers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MainBoardMYR $mainBoardMYR = null;

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

    public function getMainBoardMYR(): ?MainBoardMYR
    {
        return $this->mainBoardMYR;
    }

    public function setMainBoardMYR(?MainBoardMYR $mainBoardMYR): static
    {
        $this->mainBoardMYR = $mainBoardMYR;

        return $this;
    }
}
