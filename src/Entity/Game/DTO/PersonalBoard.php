<?php

namespace App\Entity\Game\DTO;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
class PersonalBoard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'personalBoard', cascade: ['persist', 'remove'])]
    protected ?Player $player = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player): static
    {
        // set the owning side of the relation if necessary
        if ($player->getPersonalBoard() !== $this) {
            $player->setPersonalBoard($this);
        }

        $this->player = $player;

        return $this;
    }
}
