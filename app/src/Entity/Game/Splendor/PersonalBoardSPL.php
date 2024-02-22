<?php

namespace App\Entity\Game\Splendor;

use App\Repository\Game\Splendor\PersonalBoardSPLRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalBoardSPLRepository::class)]
class PersonalBoardSPL
{
    public static int $MAX_TOKEN = 10;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: TokenSPL::class)]
    private Collection $tokens;

    #[ORM\ManyToMany(targetEntity: NobleTileSPL::class)]
    private Collection $nobleTiles;

    #[ORM\OneToMany(targetEntity: PlayerCardSPL::class, mappedBy: 'personalBoardSPL', orphanRemoval: true)]
    private Collection $playerCards;

    #[ORM\OneToOne(mappedBy: 'personalBoard', cascade: ['persist', 'remove'])]
    private ?PlayerSPL $playerSPL = null;

    #[ORM\OneToMany(targetEntity: SelectedTokenSPL::class, mappedBy: 'personalBoardSPL', orphanRemoval: true)]
    private Collection $selectedTokens;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
        $this->nobleTiles = new ArrayCollection();
        $this->playerCards = new ArrayCollection();
        $this->selectedTokens = new ArrayCollection();
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
     * @return Collection<int, PlayerCardSPL>
     */
    public function getPlayerCards(): Collection
    {
        return $this->playerCards;
    }

    public function addPlayerCard(PlayerCardSPL $playerCard): static
    {
        if (!$this->playerCards->contains($playerCard)) {
            $this->playerCards->add($playerCard);
            $playerCard->setPersonalBoardSPL($this);
        }

        return $this;
    }

    public function removePlayerCard(PlayerCardSPL $playerCard): static
    {
        if ($this->playerCards->removeElement($playerCard)) {
            // set the owning side to null (unless already changed)
            if ($playerCard->getPersonalBoardSPL() === $this) {
                $playerCard->setPersonalBoardSPL(null);
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

    /**
     * @return Collection<int, SelectedTokenSPL>
     */
    public function getSelectedTokens(): Collection
    {
        return $this->selectedTokens;
    }

    public function addSelectedToken(SelectedTokenSPL $selectedToken): static
    {
        if (!$this->selectedTokens->contains($selectedToken)) {
            $this->selectedTokens->add($selectedToken);
            $selectedToken->setPersonalBoardSPL($this);
        }

        return $this;
    }

    public function removeSelectedToken(SelectedTokenSPL $selectedToken): static
    {
        if ($this->selectedTokens->removeElement($selectedToken)) {
            // set the owning side to null (unless already changed)
            if ($selectedToken->getPersonalBoardSPL() === $this) {
                $selectedToken->setPersonalBoardSPL(null);
            }
        }

        return $this;
    }
}
