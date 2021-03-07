<?php

namespace App\Entity;

use App\Repository\FootballerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FootballerRepository::class)
 */
class Footballer
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
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $goal;

    /**
     * @ORM\OneToMany(targetEntity=FootballerVideo::class, mappedBy="footballer", orphanRemoval=true)
     */
    private $footballerVideos;

    /**
     * @ORM\OneToMany(targetEntity=FootballerPhoto::class, mappedBy="footballer", orphanRemoval=true)
     */
    private $footballerPhotos;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="footballer", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $betterFoot;

    /**
     * @ORM\OneToMany(targetEntity=FriendsList::class, mappedBy="footballer", orphanRemoval=true)
     */
    private $friendsLists;

    /**
     * @ORM\OneToMany(targetEntity=FriendsList::class, mappedBy="friend", orphanRemoval=true)
     */
    private $friendsLists2;

    /**
     * @ORM\OneToMany(targetEntity=BlockFriendsList::class, mappedBy="footballer", orphanRemoval=true)
     */
    private $blockFriendsLists;

    /**
     * @ORM\OneToMany(targetEntity=FootballerSkills::class, mappedBy="footballer")
     */
    private $footballerSkills;

    /**
     * @ORM\OneToMany(targetEntity=FootballerCarrer::class, mappedBy="footballer")
     */
    private $footballerCarrers;

    /**
     * @ORM\OneToMany(targetEntity=PrivateMessage::class, mappedBy="sender")
     */
    private $privateMessages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Language;

    private $numberFriends;

    private $friend = false;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="footballer")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=PostLikes::class, mappedBy="footballer", orphanRemoval=true)
     */
    private $postLikes;

    public function __construct()
    {
        $this->footballerVideos = new ArrayCollection();
        $this->footballerPhotos = new ArrayCollection();
        $this->friendsLists = new ArrayCollection();
        $this->friendsLists2 = new ArrayCollection();
        $this->blockFriendsLists = new ArrayCollection();
        $this->footballerSkills = new ArrayCollection();
        $this->footballerCarrers = new ArrayCollection();
        $this->privateMessages = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->postLikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(?string $goal): self
    {
        $this->goal = $goal;

        return $this;
    }

    /**
     * @return Collection|FootballerVideo[]
     */
    public function getFootballerVideos(): Collection
    {
        return $this->footballerVideos;
    }

    public function addFootballerVideo(FootballerVideo $footballerVideo): self
    {
        if (!$this->footballerVideos->contains($footballerVideo)) {
            $this->footballerVideos[] = $footballerVideo;
            $footballerVideo->setFootballer($this);
        }

        return $this;
    }

    public function removeFootballerVideo(FootballerVideo $footballerVideo): self
    {
        if ($this->footballerVideos->contains($footballerVideo)) {
            $this->footballerVideos->removeElement($footballerVideo);
            // set the owning side to null (unless already changed)
            if ($footballerVideo->getFootballer() === $this) {
                $footballerVideo->setFootballer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FootballerPhoto[]
     */
    public function getFootballerPhotos(): Collection
    {
        return $this->footballerPhotos;
    }

    public function addFootballerPhoto(FootballerPhoto $footballerPhoto): self
    {
        if (!$this->footballerPhotos->contains($footballerPhoto)) {
            $this->footballerPhotos[] = $footballerPhoto;
            $footballerPhoto->setFootballer($this);
        }

        return $this;
    }

    public function removeFootballerPhoto(FootballerPhoto $footballerPhoto): self
    {
        if ($this->footballerPhotos->contains($footballerPhoto)) {
            $this->footballerPhotos->removeElement($footballerPhoto);
            // set the owning side to null (unless already changed)
            if ($footballerPhoto->getFootballer() === $this) {
                $footballerPhoto->setFootballer(null);
            }
        }

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getBetterFoot(): ?string
    {
        return $this->betterFoot;
    }

    public function setBetterFoot(?string $betterFoot): self
    {
        $this->betterFoot = $betterFoot;

        return $this;
    }

    /**
     * @return Collection|FriendsList[]
     */
    public function getFriendsLists(): Collection
    {
        return $this->friendsLists;
    }

    /**
     * @return Collection|FriendsList[]
     */
    public function getFriendsLists2(): Collection
    {
        return $this->friendsLists2;
    }
    /**
     * @return Collection|BlockFriendsList[]
     */
    public function getBlockFriendsLists(): Collection
    {
        return $this->blockFriendsLists;
    }

    public function addBlockFriendsList(BlockFriendsList $blockFriendsList): self
    {
        if (!$this->blockFriendsLists->contains($blockFriendsList)) {
            $this->blockFriendsLists[] = $blockFriendsList;
            $blockFriendsList->setFootballer($this);
        }

        return $this;
    }

    public function removeBlockFriendsList(BlockFriendsList $blockFriendsList): self
    {
        if ($this->blockFriendsLists->contains($blockFriendsList)) {
            $this->blockFriendsLists->removeElement($blockFriendsList);
            // set the owning side to null (unless already changed)
            if ($blockFriendsList->getFootballer() === $this) {
                $blockFriendsList->setFootballer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FootballerSkills[]
     */
    public function getFootballerSkills(): Collection
    {
        return $this->footballerSkills;
    }

    public function addFootballerSkill(FootballerSkills $footballerSkill): self
    {
        if (!$this->footballerSkills->contains($footballerSkill)) {
            $this->footballerSkills[] = $footballerSkill;
            $footballerSkill->setFootballer($this);
        }

        return $this;
    }

    public function removeFootballerSkill(FootballerSkills $footballerSkill): self
    {
        if ($this->footballerSkills->contains($footballerSkill)) {
            $this->footballerSkills->removeElement($footballerSkill);
            // set the owning side to null (unless already changed)
            if ($footballerSkill->getFootballer() === $this) {
                $footballerSkill->setFootballer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FootballerCarrer[]
     */
    public function getFootballerCarrers(): Collection
    {
        return $this->footballerCarrers;
    }

    public function addFootballerCarrer(FootballerCarrer $footballerCarrer): self
    {
        if (!$this->footballerCarrers->contains($footballerCarrer)) {
            $this->footballerCarrers[] = $footballerCarrer;
            $footballerCarrer->setFootballer($this);
        }

        return $this;
    }

    public function removeFootballerCarrer(FootballerCarrer $footballerCarrer): self
    {
        if ($this->footballerCarrers->contains($footballerCarrer)) {
            $this->footballerCarrers->removeElement($footballerCarrer);
            // set the owning side to null (unless already changed)
            if ($footballerCarrer->getFootballer() === $this) {
                $footballerCarrer->setFootballer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PrivateMessage[]
     */
    public function getPrivateMessages(): Collection
    {
        return $this->privateMessages;
    }

    public function addPrivateMessage(PrivateMessage $privateMessage): self
    {
        if (!$this->privateMessages->contains($privateMessage)) {
            $this->privateMessages[] = $privateMessage;
            $privateMessage->setSender($this);
        }

        return $this;
    }

    public function removePrivateMessage(PrivateMessage $privateMessage): self
    {
        if ($this->privateMessages->contains($privateMessage)) {
            $this->privateMessages->removeElement($privateMessage);
            // set the owning side to null (unless already changed)
            if ($privateMessage->getSender() === $this) {
                $privateMessage->setSender(null);
            }
        }

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->Language;
    }

    public function setLanguage(?string $Language): self
    {
        $this->Language = $Language;

        return $this;
    }

    public function getNumberFriends()
    {
        return $this->numberFriends;
    }

    public function setNumberFriends($numberFriends): void
    {
        $this->numberFriends = $numberFriends;
    }

    /**
     * @return mixed
     */
    public function getFriend()
    {
        return $this->friend;
    }

    /**
     * @param mixed $friend
     */
    public function setFriend($friend): void
    {
        $this->friend = $friend;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setFootballer($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getFootballer() === $this) {
                $post->setFootballer(null);
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
            $postLike->setFootballer($this);
        }

        return $this;
    }

    public function removePostLike(PostLikes $postLike): self
    {
        if ($this->postLikes->contains($postLike)) {
            $this->postLikes->removeElement($postLike);
            // set the owning side to null (unless already changed)
            if ($postLike->getFootballer() === $this) {
                $postLike->setFootballer(null);
            }
        }

        return $this;
    }


}
