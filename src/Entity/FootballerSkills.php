<?php

namespace App\Entity;

use App\Repository\FootballerSkillsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FootballerSkillsRepository::class)
 */
class FootballerSkills
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=footballer::class, inversedBy="footballerSkills")
     * @ORM\JoinColumn(nullable=false)
     */
    private $footballer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
}
