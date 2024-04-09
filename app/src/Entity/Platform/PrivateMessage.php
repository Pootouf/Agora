<?php

namespace App\Entity\Platform;

use App\Repository\Platform\PrivateMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrivateMessageRepository::class)]
class PrivateMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'sents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'recieveds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recepient = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $messageTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecepient(): ?User
    {
        return $this->recepient;
    }

    public function setRecepient(?User $recepient): static
    {
        $this->recepient = $recepient;

        return $this;
    }

    public function getMessageTime(): ?\DateTimeInterface
    {
        return $this->messageTime;
    }

    public function setMessageTime(\DateTimeInterface $messageTime): static
    {
        $this->messageTime = $messageTime;

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
