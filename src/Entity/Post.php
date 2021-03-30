<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
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
    private $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=footballer::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $footballer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastModify;

    /**
     * @ORM\OneToMany(targetEntity=PostComments::class, mappedBy="post", orphanRemoval=true)
     * @ORM\OrderBy({"creationDate" = "DESC"})
     */
    private $postComments;

    /**
     * @ORM\OneToMany(targetEntity=PostLikes::class, mappedBy="post", orphanRemoval=true)
     */
    private $postLikes;

    /**
     * @ORM\Column(type="smallint")
     */
    private $liked;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $img;

    public function __construct()
    {
        $this->postComments = new ArrayCollection();
        $this->postLikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getLastModify(): ?\DateTimeInterface
    {
        return $this->lastModify;
    }

    public function setLastModify(?\DateTimeInterface $lastModify): self
    {
        $this->lastModify = $lastModify;

        return $this;
    }

    /**
     * @return Collection|PostComments[]
     */
    public function getPostComments(): Collection
    {
        return $this->postComments;
    }

    public function addPostComment(PostComments $postComment): self
    {
        if (!$this->postComments->contains($postComment)) {
            $this->postComments[] = $postComment;
            $postComment->setPost($this);
        }

        return $this;
    }

    public function removePostComment(PostComments $postComment): self
    {
        if ($this->postComments->contains($postComment)) {
            $this->postComments->removeElement($postComment);
            // set the owning side to null (unless already changed)
            if ($postComment->getPost() === $this) {
                $postComment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PostLikes[]
     */
    public function getPostLikes(): Collection
    {
        return $this->postLikes;
    }

    public function addPostLike(PostLikes $postLike): self
    {
        if (!$this->postLikes->contains($postLike)) {
            $this->postLikes[] = $postLike;
            $postLike->setPost($this);
        }

        return $this;
    }

    public function removePostLike(PostLikes $postLike): self
    {
        if ($this->postLikes->contains($postLike)) {
            $this->postLikes->removeElement($postLike);
            // set the owning side to null (unless already changed)
            if ($postLike->getPost() === $this) {
                $postLike->setPost(null);
            }
        }

        return $this;
    }

    public function getLiked(): ?int
    {
        return $this->liked;
    }

    public function setLiked(int $liked): self
    {
        $this->liked = $liked;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }
}
