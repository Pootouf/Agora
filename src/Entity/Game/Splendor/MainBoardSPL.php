<?php

namespace App\Entity\Game\Splendor;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\Splendor\MainBoardSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MainBoardSPLRepository::class)]
class MainBoardSPL extends Component
{
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSPL $game = null;

    #[ORM\OneToMany(targetEntity: RowSPL::class, mappedBy: 'mainBoardSPL', orphanRemoval: true)]
    private Collection $rowsSPL;

    #[ORM\OneToMany(targetEntity: TokenSPL::class, mappedBy: 'mainBoardSPL')]
    private Collection $tokens;

    #[ORM\OneToMany(targetEntity: NobleTileSPL::class, mappedBy: 'mainBoardSPL')]
    private Collection $nobleTiles;

    #[ORM\OneToMany(targetEntity: DrawCardsSPL::class, mappedBy: 'mainBoardSPL', orphanRemoval: true)]
    private Collection $drawCards;

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

    public function getGame(): ?GameSPL
    {
        return $this->game;
    }

    public function setGame(GameSPL $game): static
    {
        $this->game = $game;

        return $this;
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
        if ($this->rowsSPL->removeElement($rowsSPL)) {
            // set the owning side to null (unless already changed)
            if ($rowsSPL->getMainBoardSPL() === $this) {
                $rowsSPL->setMainBoardSPL(null);
            }
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
            $token->setMainBoardSPL($this);
        }

        return $this;
    }

    public function removeToken(TokenSPL $token): static
    {
        if ($this->tokens->removeElement($token)) {
            // set the owning side to null (unless already changed)
            if ($token->getMainBoardSPL() === $this) {
                $token->setMainBoardSPL(null);
            }
        }

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
            $nobleTile->setMainBoardSPL($this);
        }

        return $this;
    }

    public function removeNobleTile(NobleTileSPL $nobleTile): static
    {
        if ($this->nobleTiles->removeElement($nobleTile)) {
            // set the owning side to null (unless already changed)
            if ($nobleTile->getMainBoardSPL() === $this) {
                $nobleTile->setMainBoardSPL(null);
            }
        }

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
        if ($this->drawCards->removeElement($drawCard)) {
            // set the owning side to null (unless already changed)
            if ($drawCard->getMainBoardSPL() === $this) {
                $drawCard->setMainBoardSPL(null);
            }
        }

        return $this;
    }
}
