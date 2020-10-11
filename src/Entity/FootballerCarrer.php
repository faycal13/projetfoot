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
     * @ORM\Column(type="string", length=4)
     */
    private $startDate;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity=city::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $city;

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

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getCity(): ?city
    {
        return $this->city;
    }

    public function setCity(?city $city): self
    {
        $this->city = $city;

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

}
