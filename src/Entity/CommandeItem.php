<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Entité CommandeItem - Représente un élément individuel dans une commande
 * 
 * Cette entité stocke les informations détaillées de chaque item commandé :
 * - La commande à laquelle l'item appartient
 * - La quantité commandée pour cet item
 * - Le nom de l'item (pizza, dessert, boisson)
 * - Le prix unitaire de l'item
 * - Les suppléments associés à l'item (stockés en JSON sous forme de texte)
 * 
 * Un CommandeItem est toujours lié à une Commande parent et représente
 * un produit spécifique avec ses options personnalisées (suppléments)
 */
#[ORM\Entity]
class CommandeItem
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Commande", inversedBy: "items")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\Column]
    private ?int $qty = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'float')]
    private ?float $prix = null;

    // 🔹 Nouveau champ pour stocker les suppléments sous forme de texte
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $supplements = null;

    // --- Getters & Setters ---
    public function getId(): ?int { return $this->id; }

    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): static { $this->commande = $commande; return $this; }

    public function getQty(): ?int { return $this->qty; }
    public function setQty(int $qty): static { $this->qty = $qty; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPrix(): ?float { return $this->prix; }
    public function setPrix(float $prix): static { $this->prix = $prix; return $this; }

    public function getSupplements(): ?string { return $this->supplements; }
    public function setSupplements(?string $supplements): static { $this->supplements = $supplements; return $this; }
}
