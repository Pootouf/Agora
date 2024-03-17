<?php

namespace App\Entity\Platform;

use App\Repository\Platform\BoardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: BoardRepository::class)]
class Board
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbUserMax = null;

    #[ORM\Column(length: 20)]
    private ?string $status
        = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $invitationTimer = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $inactivityTimer = null;

    #[ORM\Column(length: 255)]
    private ?string $invitationHash = null;

    #[ORM\Column]
    private ?int $nbInvitations = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'boards')]
    private Collection $listUsers;

    #[ORM\Column]
    private ?int $inactivityHours = null;



    #[ORM\ManyToOne(inversedBy: 'boards')]
    private ?Game $game = null;

    #[ORM\Column]
    private int $partyId;



    public function __construct()
    {
        $this->status = "WAITING";
        $this->invitationHash = sha1(random_bytes(10));
        $this->listUsers = new ArrayCollection();
    }
    /**
     * Gets the ID of the board.
     *
     * @return int|null The ID of the board.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the maximum number of users allowed on the board.
     *
     * @return int|null The maximum number of users allowed on the board.
     */
    public function getNbUserMax(): ?int
    {
        return $this->nbUserMax;
    }

    /**
     * Sets the maximum number of users allowed on the board.
     *
     * @param int $nbUserMax The maximum number of users allowed on the board.
     * @return static
     */
    public function setNbUserMax(int $nbUserMax): static
    {
        $this->nbUserMax = $nbUserMax;

        return $this;
    }

    /**
     * Gets the status of the board.
     *
     * @return string|null The status of the board.
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
    
    /**
     * Sets the status of the board.
     *
     * @param string $status The status of the board.
     * @return static
     */
    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

   /**
     * Gets the creation date of the board.
     *
     * @return \DateTimeInterface|null The creation date of the board.
     */
    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    /**
     * Sets the creation date of the board.
     *
     * @param \DateTimeInterface $creationDate The creation date of the board.
     * @return static
     */
    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getInvitationTimer(): ?\DateTimeInterface
    {
        return $this->invitationTimer;
    }

    public function setInvitationTimer(\DateTimeInterface $invitationTimer): static
    {
        $this->invitationTimer = $invitationTimer;

        return $this;
    }

    public function getInactivityTimer(): ?\DateTimeInterface
    {
        return $this->inactivityTimer;
    }

    public function setInactivityTimer(\DateTimeInterface $inactivityTimer): static
    {
        $this->inactivityTimer = $inactivityTimer;

        return $this;
    }

    public function getInvitationHash(): ?string
    {
        return $this->invitationHash;
    }

    public function setInvitationHash(string $invitationHash): static
    {
        $this->invitationHash = $invitationHash;

        return $this;
    }

    public function getNbInvitations(): ?int
    {
        return $this->nbInvitations;
    }

    public function setNbInvitations(int $nbInvitations): static
    {
        $this->nbInvitations = $nbInvitations;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getListUsers(): Collection
    {
        return $this->listUsers;
    }

    //Add user to the Board
    //Pre : $user not in $this->listUsers
    //      $this->listUsers->count < $this->nbMaxUser
    //Post : $user in $this->listUsers
    //       $this->listUser->count += 1
    public function addListUser(User $user): int
    {
        if (!$this->listUsers->contains($user) && $this->isAvailble() ) {
            $this->listUsers->add($user);
            return 0;
        }

        return -1;
    }

    //return the number of players who have joined the table

    public function getUsersNb(): ?int
    {
        return $this->getListUsers()->count();
    }

    //Remove user of the list of player
    public function removeListUser(User $user): static
    {
        if ($this->listUsers->contains($user)) {
            $this->listUsers->removeElement($user);
        }

        return $this;
    }

    //Return true if the board is availble for a player to join
    public function isAvailble(){
        return $this->status!="IN_GAME" && $this->listUsers->count() + $this->nbInvitations < $this->nbUserMax;
    }

    public function getInactivityHours(): ?int
    {
        return $this->inactivityHours;
    }

    public function setInactivityHours(int $inactivityHours): static
    {
        $this->inactivityHours = $inactivityHours;

        return $this;
    }

    public function hasUser(User $user):bool
    {
        return $this->listUsers->contains($user);
    }

    
    //Return the actual number of availble slots of the board
    //getNbAvailbleSlots() == 0 => isAvailble() == false
    public function getNbAvailbleSlots():int{
        return $this->getNbUserMax() - ($this->listUsers->count() +
                $this->nbInvitations);
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game=$game;

        return $this;
    }

    public function getPartyId(): ?int
    {
        return $this->partyId;
    }

    public function setPartyId(int $partyId): static
    {
        $this->partyId = $partyId;

        return $this;
    }

    /**
     * Checks if all places on the board have been taken by players.
     *
     * @return bool Returns true if all places on the board have been taken by players, otherwise false.
     */
    public function isFull():bool
    {
        return $this->listUsers->count() == $this->nbUserMax;
    }

}

