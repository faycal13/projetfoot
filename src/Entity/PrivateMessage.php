<?php

namespace App\Entity;

use App\Repository\PrivateMessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrivateMessageRepository::class)
 */
class PrivateMessage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Conversation::class, inversedBy="privateMessages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conversation;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\ManyToOne(targetEntity=footballer::class, inversedBy="privateMessages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConversation(): ?conversation
    {
        return $this->conversation;
    }

    public function setConversation(?conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getSender(): ?footballer
    {
        return $this->sender;
    }

    public function setSender(?footballer $sender): self
    {
        $this->sender = $sender;

        return $this;
    }
}
