<?php

namespace App\Entity;

use App\Repository\BoardRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoardRepository::class)]
class Board
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbUserMax = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $invitationTimer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbUserMax(): ?int
    {
        return $this->nbUserMax;
    }

    public function setNbUserMax(int $nbUserMax): static
    {
        $this->nbUserMax = $nbUserMax;

        return $this;
    }

    public function getInvitationTimer(): ?\DateTimeInterface
    {
        return $this->invitationTimer;
    }

    public function setInvitationTimer(\DateTimeInterface $invitationTimer): static
    {
        $this->invitationTimer = $invitationTimer;

        return $this;
    }
}
