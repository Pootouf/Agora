<?php

namespace App\Entity\Game\Myrmes;

use App\Repository\Game\Myrmes\NurseMYRRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NurseMYRRepository::class)]
class NurseMYR
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
