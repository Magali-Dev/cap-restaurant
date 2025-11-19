<?php

namespace App\Entity;

use App\Repository\PizzaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Supplement;
/**
 * Entité Pizza - Gestion des pizzas proposées par la pizzeria
 * 
 * Cette entité représente une pizza avec toutes ses caractéristiques :
 * - Nom et description de la pizza
 * - Prix de base de la pizza
 * - Image d'illustration de la pizza
 * - Indicateur végétarien pour le filtrage
 * - Liste des allergènes présents dans la pizza
 * - Suppléments disponibles pour personnaliser cette pizza
 * 
 * Relation ManyToMany avec les suppléments : une pizza peut avoir 
 * plusieurs suppléments optionnels, et un supplément peut être 
 * associé à plusieurs pizzas
 */
#[ORM\Entity(repositoryClass: PizzaRepository::class)]
class Pizza
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(nullable: true)]
    private ?bool $vegetarien = null;

    #[ORM\Column(nullable: true)]
    private ?array $allergenes = null;

    #[ORM\ManyToMany(targetEntity: Supplement::class, mappedBy: "pizzas")]
    private Collection $supplements;

    public function __construct()
    {
        $this->supplements = new ArrayCollection();
    }

    // --- Getters / Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function isVegetarien(): ?bool
    {
        return $this->vegetarien;
    }

    public function setVegetarien(?bool $vegetarien): static
    {
        $this->vegetarien = $vegetarien;
        return $this;
    }

    public function getAllergenes(): ?array
    {
        return $this->allergenes;
    }

    public function setAllergenes(?array $allergenes): static
    {
        $this->allergenes = $allergenes;
        return $this;
    }

    // --- Relation avec les suppléments ---

    public function getSupplements(): Collection
    {
        return $this->supplements;
    }

    public function addSupplement(Supplement $supplement): self
    {
        if (!$this->supplements->contains($supplement)) {
            $this->supplements[] = $supplement;
            $supplement->addPizza($this);
        }
        return $this;
    }

    public function removeSupplement(Supplement $supplement): self
    {
        if ($this->supplements->removeElement($supplement)) {
            $supplement->removePizza($this);
        }
        return $this;
    }
}
