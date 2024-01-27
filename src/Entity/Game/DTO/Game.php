<?php

namespace App\Entity\Game\DTO;

use App\Entity\Game\Help;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;


    public function getId(): ?int
    {
        return $this->id;
    }
}
