<?php

namespace App\Entity;

use App\Repository\FriendsListRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FriendsListRepository::class)
 */
class FriendsList
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=footballer::class, inversedBy="friendsLists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $footballer;

    /**
     * @ORM\ManyToOne(targetEntity=footballer::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $friend;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accept;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFootballer(): ?footballer
    {
        return $this->footballer;
    }

    public function setFootballer(?footballer $footballer): self
    {
        $this->footballer = $footballer;

        return $this;
    }

    public function getFriend(): ?footballer
    {
        return $this->friend;
    }

    public function setFriend(?footballer $friend): self
    {
        $this->friend = $friend;

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

    public function getAccept(): ?bool
    {
        return $this->accept;
    }

    public function setAccept(bool $accept): self
    {
        $this->accept = $accept;

        return $this;
    }
}
