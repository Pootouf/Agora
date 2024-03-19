<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\AnthillWorkerMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnthillWorkerMYRRepository::class)]
class AnthillWorkerMYR
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $workFloor = null;

    #[ORM\ManyToOne(inversedBy: 'anthillWorkerMYRs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlayerMYR $player = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkFloor(): ?int
    {
        return $this->workFloor;
    }

    public function setWorkFloor(int $workFloor): static
    {
        $this->workFloor = $workFloor;

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
