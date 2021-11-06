<?php

namespace App\Entity;

use App\Repository\SpecieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SpecieRepository::class)
 */
class Specie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $classification;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $skin_colors;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hair_colors;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $eye_colors;

    /**
     * @ORM\ManyToMany(targetEntity=Character::class, inversedBy="species")
     */
    private $characters;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
    }

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

    public function getClassification(): ?string
    {
        return $this->classification;
    }

    public function setClassification(?string $classification): self
    {
        $this->classification = $classification;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getSkinColors(): ?string
    {
        return $this->skin_colors;
    }

    public function setSkinColors(?string $skin_colors): self
    {
        $this->skin_colors = $skin_colors;

        return $this;
    }

    public function getHairColors(): ?string
    {
        return $this->hair_colors;
    }

    public function setHairColors(?string $hair_colors): self
    {
        $this->hair_colors = $hair_colors;

        return $this;
    }

    public function getEyeColors(): ?string
    {
        return $this->eye_colors;
    }

    public function setEyeColors(?string $eye_colors): self
    {
        $this->eye_colors = $eye_colors;

        return $this;
    }

    /**
     * @return Collection|Character[]
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(Character $character): self
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
        }

        return $this;
    }

    public function removeCharacter(Character $character): self
    {
        $this->characters->removeElement($character);

        return $this;
    }

    /**
     * @return string
     */
    public function getResumen()
    {
        return $this->getName(). ' ' . $this->getClassification() . ' ' . $this->getDesignation();
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getName();
    }
}
