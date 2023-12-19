<?php

namespace App\Entity\Game\DTO;

use App\Repository\ComponentRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
use App\Entity\Game\Help;

#[MappedSuperclass]
class Component
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'component', cascade: ['persist', 'remove'])]
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
