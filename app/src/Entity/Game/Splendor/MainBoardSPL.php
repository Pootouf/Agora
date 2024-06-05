<?php

namespace App\Entity\Game\Splendor;

use App\Repository\Game\Splendor\MainBoardSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MainBoardSPLRepository::class)]
class MainBoardSPL
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: RowSPL::class, mappedBy: 'mainBoardSPL', orphanRemoval: true)]
    private Collection $rowsSPL;

    #[ORM\ManyToMany(targetEntity: TokenSPL::class)]
    private Collection $tokens;

    #[ORM\ManyToMany(targetEntity: NobleTileSPL::class)]
    private Collection $nobleTiles;

    #[ORM\OneToMany(targetEntity: DrawCardsSPL::class, mappedBy: 'mainBoardSPL', orphanRemoval: true)]
    private Collection $drawCards;

    #[ORM\OneToOne(mappedBy: 'mainBoard', cascade: ['persist', 'remove'])]
    private ?GameSPL $gameSPL = null;

    public function __construct()
    {
        $this->rowsSPL = new ArrayCollection();
        $this->tokens = new ArrayCollection();
        $this->nobleTiles = new ArrayCollection();
        $this->drawCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, RowSPL>
     */
    public function getRowsSPL(): Collection
    {
        return $this->rowsSPL;
    }

    public function addRowsSPL(RowSPL $rowsSPL): static
    {
        if (!$this->rowsSPL->contains($rowsSPL)) {
            $this->rowsSPL->add($rowsSPL);
            $rowsSPL->setMainBoardSPL($this);
        }

        return $this;
    }

    public function removeRowsSPL(RowSPL $rowsSPL): static
    {
        if ($this->rowsSPL->removeElement($rowsSPL) && $rowsSPL->getMainBoardSPL() === $this) {
            $rowsSPL->setMainBoardSPL(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, TokenSPL>
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(TokenSPL $token): static
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens->add($token);
        }

        return $this;
    }

    public function removeToken(TokenSPL $token): static
    {
        $this->tokens->removeElement($token);

        return $this;
    }

    /**
     * @return Collection<int, NobleTileSPL>
     */
    public function getNobleTiles(): Collection
    {
        return $this->nobleTiles;
    }

    public function addNobleTile(NobleTileSPL $nobleTile): static
    {
        if (!$this->nobleTiles->contains($nobleTile)) {
            $this->nobleTiles->add($nobleTile);
        }

        return $this;
    }

    public function removeNobleTile(NobleTileSPL $nobleTile): static
    {
        $this->nobleTiles->removeElement($nobleTile);

        return $this;
    }

    /**
     * @return Collection<int, DrawCardsSPL>
     */
    public function getDrawCards(): Collection
    {
        return $this->drawCards;
    }

    public function addDrawCard(DrawCardsSPL $drawCard): static
    {
        if (!$this->drawCards->contains($drawCard)) {
            $this->drawCards->add($drawCard);
            $drawCard->setMainBoardSPL($this);
        }

        return $this;
    }

    public function removeDrawCard(DrawCardsSPL $drawCard): static
    {
        if ($this->drawCards->removeElement($drawCard) && $drawCard->getMainBoardSPL() === $this) {
            $drawCard->setMainBoardSPL(null);
        }

        return $this;
    }

    public function getGameSPL(): ?GameSPL
    {
        return $this->gameSPL;
    }

    public function setGameSPL(GameSPL $gameSPL): static
    {
        // set the owning side of the relation if necessary
        if ($gameSPL->getMainBoard() !== $this) {
            $gameSPL->setMainBoard($this);
        }

        $this->gameSPL = $gameSPL;

        return $this;
    }
}
