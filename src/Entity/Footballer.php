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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profilPhoto;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $goal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverPhoto;

    /**
     * @ORM\OneToMany(targetEntity=FootballerVideo::class, mappedBy="footballer", orphanRemoval=true)
     */
    private $footballerVideos;

    /**
     * @ORM\OneToMany(targetEntity=FootballerPhoto::class, mappedBy="footballer", orphanRemoval=true)
     */
    private $footballerPhotos;

    /**
     * @ORM\OneToOne(targetEntity=user::class, inversedBy="footballer", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
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

    public function __construct()
    {
        $this->footballerVideos = new ArrayCollection();
        $this->footballerPhotos = new ArrayCollection();
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

    public function getProfilPhoto(): ?string
    {
        return $this->profilPhoto;
    }

    public function setProfilPhoto(?string $profilPhoto): self
    {
        $this->profilPhoto = $profilPhoto;

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

    public function getCoverPhoto(): ?string
    {
        return $this->coverPhoto;
    }

    public function setCoverPhoto(?string $coverPhoto): self
    {
        $this->coverPhoto = $coverPhoto;

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
}
