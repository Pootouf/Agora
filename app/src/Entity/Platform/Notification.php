<?php

namespace App\Entity\Platform;

use App\Repository\Platform\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?User $receiver = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTime $createdAt;

    #[ORM\Column]
    private ?bool $isRead;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;


    public  static string $TYPE_MESSAGE = "Message";
    public  static string $TYPE_INVITATION = "Invitation";

    /**
     * Gets the ID of the notification.
     *
     * @return int|null The ID of the notification.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the receiver of the notification.
     *
     * @return User|null The receiver of the notification.
     */
    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    /**
     * Sets the receiver of the notification.
     *
     * @param User|null $receiver The receiver of the notification.
     * @return static
     */
    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Gets the content of the notification.
     *
     * @return string|null The content of the notification.
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Sets the content of the notification.
     *
     * @param string $content The content of the notification.
     * @return static
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gets the creation date of the notification.
     *
     * @return \DateTime|null The creation date of the notification.
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation date of the notification.
     *
     * @param \DateTime $createdAt The creation date of the notification.
     * @return static
     */
    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Checks if the notification is read.
     *
     * @return bool|null True if the notification is read, false otherwise.
     */
    public function isIsRead(): ?bool
    {
        return $this->isRead;
    }

    /**
     * Sets whether the notification is read.
     *
     * @param bool $isRead Whether the notification is read.
     * @return static
     */
    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }

    
    /**
     * Constructs a new Notification object.
     */
    public function __construct
    ()
    {
        $this->createdAt = new \DateTime();
        $this->isRead = false;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
