<?php

namespace App\Entity;

use App\Repository\FootballerCarrerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FootballerCarrerRepository::class)
 */
class FootballerCarrer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $club;

    /**
     * @ORM\ManyToOne(targetEntity=footballer::class, inversedBy="footballerCarrers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $footballer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $goalNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $matchNumber;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $saisonDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClub(): ?string
    {
        return $this->club;
    }

    public function setClub(string $club): self
    {
        $this->club = $club;

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

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position): void
    {
        $this->position = $position;
    }

    public function getCategorie()
    {
        return $this->categorie;
    }

    public function setCategorie($categorie): void
    {
        $this->categorie = $categorie;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getGoalNumber(): ?int
    {
        return $this->goalNumber;
    }

    public function setGoalNumber(?int $goalNumber): self
    {
        $this->goalNumber = $goalNumber;

        return $this;
    }

    public function getMatchNumber(): ?int
    {
        return $this->matchNumber;
    }

    public function setMatchNumber(?int $matchNumber): self
    {
        $this->matchNumber = $matchNumber;

        return $this;
    }

    public function getSaisonDate(): ?string
    {
        return $this->saisonDate;
    }

    public function setSaisonDate(?string $saisonDate): self
    {
        $this->saisonDate = $saisonDate;

        return $this;
    }

}
