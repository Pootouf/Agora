<?php

namespace App\Entity\Game\Splendor;

use App\Repository\Game\Splendor\DrawCardsSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrawCardsSPLRepository::class)]
class DrawCardsSPL
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\ManyToMany(targetEntity: DevelopmentCardsSPL::class)]
    private Collection $developmentCards;

    #[ORM\ManyToOne(inversedBy: 'drawCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MainBoardSPL $mainBoardSPL = null;

    public function __construct()
    {
        $this->developmentCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, DevelopmentCardsSPL>
     */
    public function getDevelopmentCards(): Collection
    {
        return $this->developmentCards;
    }

    public function addDevelopmentCard(DevelopmentCardsSPL $developmentCard): static
    {
        if (!$this->developmentCards->contains($developmentCard)) {
            $this->developmentCards->add($developmentCard);
        }

        return $this;
    }

    public function removeDevelopmentCard(DevelopmentCardsSPL $developmentCard): static
    {
        $this->developmentCards->removeElement($developmentCard);

        return $this;
    }

    public function getMainBoardSPL(): ?MainBoardSPL
    {
        return $this->mainBoardSPL;
    }

    public function setMainBoardSPL(?MainBoardSPL $mainBoardSPL): static
    {
        $this->mainBoardSPL = $mainBoardSPL;

        return $this;
    }
}
