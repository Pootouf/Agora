<?php

namespace App\Entity\Game\SPL;

use App\Repository\Game\SPL\PersonalBoardSPLRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalBoardSPLRepository::class)]
class PersonalBoardSPL
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
