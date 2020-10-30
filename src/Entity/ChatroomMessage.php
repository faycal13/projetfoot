<?php

namespace App\Entity;

use App\Repository\ChatroomMessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatroomMessageRepository::class)
 */
class ChatroomMessage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\ManyToOne(targetEntity=footballer::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity=ChatroomList::class)
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $ChatroomPeople;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $internalLink;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getChatroomPeople(): ?ChatroomList
    {
        return $this->ChatroomPeople;
    }

    public function setChatroomPeople(?ChatroomList $ChatroomPeople): self
    {
        $this->ChatroomPeople = $ChatroomPeople;

        return $this;
    }

    public function getInternalLink(): ?string
    {
        return $this->internalLink;
    }

    public function setInternalLink(?string $internalLink): self
    {
        $this->internalLink = $internalLink;

        return $this;
    }
}
