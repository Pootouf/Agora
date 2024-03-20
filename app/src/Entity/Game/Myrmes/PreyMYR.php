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
    #[ORM\JoinColumn(nullable: false)]
    private ?TileMYR $tile = null;

    #[ORM\ManyToOne(inversedBy: 'preyMYRs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameMYR $game = null;

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

    public function getGame(): ?GameMYR
    {
        return $this->game;
    }

    public function setGame(?GameMYR $game): static
    {
        $this->game = $game;

        return $this;
    }
}
