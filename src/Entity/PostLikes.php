<?php

namespace App\Entity;

use App\Repository\PostLikesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostLikesRepository::class)
 */
class PostLikes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Footballer::class, inversedBy="postLikes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $footballer;

    /**
     * @ORM\Column(type="smallint")
     */
    private $love;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="postLikes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

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

    public function getLove(): ?int
    {
        return $this->love;
    }

    public function setLove(int $love): self
    {
        $this->love = $love;

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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
