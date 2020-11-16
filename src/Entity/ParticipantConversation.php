<?php

namespace App\Entity;

use App\Repository\ParticipantConversationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParticipantConversationRepository::class)
 */
class ParticipantConversation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Footballer::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $footballer;

    /**
     * @ORM\ManyToOne(targetEntity=Conversation::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $conversation;

    /**
     * @ORM\Column(type="array", length=255)
     */
    private $participants;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFootballer(): ?Footballer
    {
        return $this->footballer;
    }

    public function setFootballer(?Footballer $footballer): self
    {
        $this->footballer = $footballer;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getParticipants(): ?array
    {
        return $this->participants;
    }

    public function setParticipants(array $participants): self
    {
        $this->participants = $participants;

        return $this;
    }
}
