<?php

namespace App\Entity\Platform;

use App\Repository\Platform\BoardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

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

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $invitationTimer = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $inactivityTimer = null;

    #[ORM\Column(length: 255)]
    private ?string $invitationHash = null;

    #[ORM\Column]
    private ?int $nbInvitations = 0;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'boards')]
    private Collection $listUsers;

    #[ORM\Column]
    private ?int $inactivityHours = null;



    #[ORM\ManyToOne(inversedBy: 'boards')]
    private ?Game $game = null;

    #[ORM\Column]
    private int $partyId;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: "user_invited")]
    private Collection $invitedContacts;

    //All status of a table
    private static string $STATUS_WAITING = "WAITING";
    private static string $STATUS_IN_GAME = "IN_GAME";
    private static string $STATUS_FINISHED = "FINISHED";


    public function __construct()
    {
        $this->status = $this::$STATUS_WAITING;
        $this->invitationHash = sha1(random_bytes(10));
        $this->listUsers = new ArrayCollection();
        $this->invitedContacts = new ArrayCollection();
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

    /**
     * Gets the invitation timer for the board.
     *
     * @return \DateTimeInterface|null The invitation timer for the board.
     */
    public function getInvitationTimer(): ?\DateTimeInterface
    {
        return $this->invitationTimer;
    }

    /**
     * Sets the invitation timer for the board.
     *
     * @param \DateTimeInterface $invitationTimer The invitation timer for the board.
     * @return static
     */
    public function setInvitationTimer(\DateTimeInterface $invitationTimer): static
    {
        $this->invitationTimer = $invitationTimer;

        return $this;
    }

    /**
     * Gets the inactivity timer for the board.
     *
     * @return \DateTimeInterface|null The inactivity timer for the board.
     */
    public function getInactivityTimer(): ?\DateTimeInterface
    {
        return $this->inactivityTimer;
    }

    /**
     * Sets the inactivity timer of the board.
     *
     * @param \DateTimeInterface $inactivityTimer The inativity timer to set.
     * @return static
     */
    public function setInactivityTimer(\DateTimeInterface $inactivityTimer): static
    {
        $this->inactivityTimer = $inactivityTimer;

        return $this;
    }

    /**
     * Gets the invitation hash for the board.
     *
     * @return string|null The invitation hash for the board.
     */
    public function getInvitationHash(): ?string
    {
        return $this->invitationHash;
    }

    /**
     * Sets the invitation hash for the board.
     *
     * @param string $invitationHash The invitation hash for the board.
     * @return static
     */
    public function setInvitationHash(string $invitationHash): static
    {
        $this->invitationHash = $invitationHash;

        return $this;
    }

    /**
     * Gets the number of invitations for the board.
     *
     * @return int|null The number of invitations for the board.
     */
    public function getNbInvitations(): ?int
    {
        return $this->getInvitedContacts()->count();
    }

    /**
     * Sets the number of invitations for the board.
     *
     * @param int $nbInvitations The number of invitations for the board.
     * @return static
     */
    public function setNbInvitations(int $nbInvitations): static
    {
        $this->nbInvitations = $nbInvitations;

        return $this;
    }

    /**
     * Gets the list of users associated with the board.
     *
     * @return Collection<int, User> The list of users associated with the board.
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
        if (!$this->listUsers->contains($user) && $this->isAvailble() || $this->getInvitedContacts()->contains($user)) {
            $this->listUsers->add($user);
            return 0;
        }

        return -1;
    }


    /**
     * Gets the number of users who have joined the board.
     *
     * @return int|null The number of users who have joined the board.
     */
    public function getUsersNb(): ?int
    {
        return $this->getListUsers()->count();
    }

    /**
     * Removes a user from the board's list of users.
     *
     * @param User $user The user to remove.
     * @return static
     */
    public function removeListUser(User $user): static
    {
        if ($this->listUsers->contains($user)) {
            $this->listUsers->removeElement($user);
        }

        return $this;
    }


    /**
     * Checks if the board is available for a player to join.
     *
     * @return bool Returns true if the board is available, false otherwise.
     */
    public function isAvailble()
    {
        return $this->isWaiting() && $this->listUsers->count() + $this->getNbInvitations() < $this->nbUserMax;
    }

    /**
     * Gets the inactivity hours for the board.
     *
     * @return int|null The inactivity hours for the board.
     */
    public function getInactivityHours(): ?int
    {
        return $this->inactivityHours;
    }

    /**
     * Sets the inactivity hours for the board.
     *
     * @param int $inactivityHours The inactivity hours for the board.
     * @return static
     */
    public function setInactivityHours(int $inactivityHours): static
    {
        $this->inactivityHours = $inactivityHours;

        return $this;
    }

    /**
     * Checks if a user exists in the board's list of users.
     *
     * @param User $user The user to check.
     * @return bool Returns true if the user exists in the list, false otherwise.
     */
    public function hasUser(User $user): bool
    {
        return $this->listUsers->contains($user);
    }


    /**
     * Gets the number of available slots on the board.
     *
     * @return int The number of available slots on the board.
     * getNbAvailbleSlots() == 0 => isAvailble() == false
     */
    public function getNbAvailbleSlots(): int
    {
        return $this->getNbUserMax() - ($this->listUsers->count() +
                $this->getNbInvitations());
    }

    /**
     * Gets the game associated with the board.
     *
     * @return Game|null The game associated with the board.
     */
    public function getGame(): ?Game
    {
        return $this->game;
    }

    /**
     * Sets the game associated with the board.
     *
     * @param Game|null $game The game associated with the board.
     * @return static
     */
    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Gets the party ID of the board.
     *
     * @return int|null The party ID of the board.
     */
    public function getPartyId(): ?int
    {
        return $this->partyId;
    }

    /**
     * Sets the party ID of the board.
     *
     * @param int $partyId The party ID of the board.
     * @return static
     */
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
    public function isFull(): bool
    {
        return $this->listUsers->count() == $this->nbUserMax;
    }


    /**
     * Set the board as "Waiting"
     *
     *
     */

    public function setWaiting(): void
    {
        $this->status = $this::$STATUS_WAITING;
    }

    /**
     * Check if the board as the status "Waiting"
     *
     *
     */

    public function isWaiting(): bool
    {
        return $this->status === $this::$STATUS_WAITING;
    }

    /**
     * Set the board as "In_Game"
     *
     *
     */

    public function setInGame(): void
    {
        $this->status = $this::$STATUS_IN_GAME;
    }

    /**
     * Check if the board as the status "In_Game"
     *
     *
     */

    public function isInGame(): bool
    {
        return $this->status === $this::$STATUS_IN_GAME;
    }


    /**
     * Set the board as "Finished"
     *
     *
     */
    public function setFinished(): void
    {
        $this->status = $this::$STATUS_FINISHED;
    }

    /**
     * Check if the board as the status "Finished"
     *
     *
     */

    public function isFinished(): bool
    {
        return $this->status === $this::$STATUS_FINISHED;
    }


    /**
     * @return Collection<int, User>
     */
    public function getInvitedContacts(): Collection
    {
        return $this->invitedContacts;
    }

    /**
     * Add user to the list of invited contacts
     *
     *
     */
    public function addInvitedContact(User $invitedContact): static
    {
        if (!$this->invitedContacts->contains($invitedContact)) {
            $this->invitedContacts->add($invitedContact);
            $this->setNbInvitations($this->getNbInvitations());
        }

        return $this;
    }

    /**
     * Remove user form the list of invited contacts
     *
     *
     */
    public function removeInvitedContact(User $invitedContact): static
    {
        $this->invitedContacts->removeElement($invitedContact);
        $this->setNbInvitations($this->getNbInvitations());

        return $this;
    }

    /**
     * Remove all invited contacts from the list
     *
     *
     */

    public function cleanInvitationList(): void
    {
        $this->invitedContacts->clear();
    }

    public function isInInvitationList(User $user) : bool
    {
        return $this->invitedContacts->contains($user);
    }
}
