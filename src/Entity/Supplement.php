<?php 

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Pizza;
use App\Repository\SupplementRepository; // <-- ajouter ceci
/**
 * Entité Supplement - Gestion des suppléments optionnels pour les pizzas
 * 
 * Cette entité représente un supplément qui peut être ajouté à une pizza :
 * - Nom du supplément (ex: "Extra fromage", "Champignons", "Jambon")
 * - Prix supplémentaire pour l'ajout de cet ingrédient
 * - Relation ManyToMany avec les pizzas : un supplément peut être 
 *   disponible pour plusieurs pizzas, et une pizza peut avoir 
 *   plusieurs suppléments optionnels
 * 
 * Permet la personnalisation des pizzas par les clients avec
 * des ingrédients supplémentaires au prix défini
 */
#[ORM\Entity(repositoryClass: SupplementRepository::class)] // <-- utiliser juste le nom de classe
class Supplement
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100)]
    private ?string $nom = null;

    #[ORM\Column(type:"float")]
    private ?float $prix = null;

    #[ORM\ManyToMany(targetEntity: Pizza::class, inversedBy: "supplements")]
    private Collection $pizzas;

    public function __construct()
    {
        $this->pizzas = new ArrayCollection();
    }

    // Getters & setters
    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getPrix(): ?float { return $this->prix; }
    public function setPrix(float $prix): self { $this->prix = $prix; return $this; }
    public function getPizzas(): Collection { return $this->pizzas; }
    public function addPizza(Pizza $pizza): self { 
        if (!$this->pizzas->contains($pizza)) $this->pizzas[] = $pizza;
        return $this; 
    }
    public function removePizza(Pizza $pizza): self { $this->pizzas->removeElement($pizza); return $this; }
}
