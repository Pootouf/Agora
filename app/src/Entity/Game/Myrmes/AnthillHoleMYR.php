<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\AnthillHoleMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnthillHoleMYRRepository::class)]
class AnthillHoleMYR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TileMYR $tile = null;

    #[ORM\ManyToOne(inversedBy: 'anthillHoleMYRs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerMYR $player = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
