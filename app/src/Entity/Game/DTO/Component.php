<?php

namespace App\Entity\Game\DTO;

use App\Entity\Game\Help;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class Component
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\OneToOne]
    protected ?Help $help = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHelp(): ?Help
    {
        return $this->help;
    }

    public function setHelp(?Help $help): static
    {
        $this->help = $help;

        return $this;
    }
}
