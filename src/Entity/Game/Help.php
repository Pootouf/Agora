<?php

namespace App\Entity\Game;

use App\Repository\HelpRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Game\DTO\Component;

#[ORM\Entity(repositoryClass: HelpRepository::class)]
class Help
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $gameName = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToOne(mappedBy: 'help', cascade: ['persist', 'remove'])]
    private ?Component $component = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameName(): ?string
    {
        return $this->gameName;
    }

    public function setGameName(string $gameName): static
    {
        $this->gameName = $gameName;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getComponent(): ?Component
    {
        return $this->component;
    }

    public function setComponent(?Component $component): static
    {
        // unset the owning side of the relation if necessary
        if ($component === null && $this->component !== null) {
            $this->component->setHelp(null);
        }

        // set the owning side of the relation if necessary
        if ($component !== null && $component->getHelp() !== $this) {
            $component->setHelp($this);
        }

        $this->component = $component;

        return $this;
    }
}
