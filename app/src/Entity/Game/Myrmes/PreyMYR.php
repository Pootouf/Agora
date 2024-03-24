<?php

namespace App\Entity\Game\Myrmes;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Myrmes\PreyMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreyMYRRepository::class)]
class PreyMYR extends Component
{
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne]
    private ?TileMYR $tile = null;

    #[ORM\ManyToOne(inversedBy: 'preyMYRs')]
    private ?PlayerMYR $player = null;

    #[ORM\ManyToOne(inversedBy: 'preys')]
    private ?MainBoardMYR $mainBoardMYR = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getPlayer(): ?PlayerMYR
    {
        return $this->player;
    }

    public function setPlayer(?PlayerMYR $player): static
    {
        $this->player = $player;

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
