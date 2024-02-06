<?php

namespace App\Entity\Game\SPL;

use App\Entity\Game\DTO\Component;
use App\Repository\Game\SPL\PersonalBoardSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalBoardSPLRepository::class)]
class PersonalBoardSPL extends Component
{

    #[ORM\OneToMany(targetEntity: TokenSPL::class, mappedBy: 'personalBoardSPL')]
    private Collection $tokens;

    #[ORM\OneToMany(targetEntity: NobleTileSPL::class, mappedBy: 'personalBoardSPL')]
    private Collection $nobleTiles;

    #[ORM\OneToOne(mappedBy: 'personalBoard', cascade: ['persist', 'remove'])]
    private ?PlayerSPL $playerSPL = null;

    #[ORM\ManyToOne(inversedBy: 'personalBoardsSPL')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameSPL $game = null;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
        $this->nobleTiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $token->setPersonalBoardSPL($this);
        }

        return $this;
    }

    public function removeToken(TokenSPL $token): static
    {
        if ($this->tokens->removeElement($token)) {
            // set the owning side to null (unless already changed)
            if ($token->getPersonalBoardSPL() === $this) {
                $token->setPersonalBoardSPL(null);
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
            $nobleTile->setPersonalBoardSPL($this);
        }

        return $this;
    }

    public function removeNobleTile(NobleTileSPL $nobleTile): static
    {
        if ($this->nobleTiles->removeElement($nobleTile)) {
            // set the owning side to null (unless already changed)
            if ($nobleTile->getPersonalBoardSPL() === $this) {
                $nobleTile->setPersonalBoardSPL(null);
            }
        }

        return $this;
    }

    public function getPlayerSPL(): ?PlayerSPL
    {
        return $this->playerSPL;
    }

    public function setPlayerSPL(PlayerSPL $playerSPL): static
    {
        // set the owning side of the relation if necessary
        if ($playerSPL->getPersonalBoard() !== $this) {
            $playerSPL->setPersonalBoard($this);
        }

        $this->playerSPL = $playerSPL;

        return $this;
    }

    public function getGame(): ?GameSPL
    {
        return $this->game;
    }

    public function setGame(?GameSPL $game): static
    {
        $this->game = $game;

        return $this;
    }
}
