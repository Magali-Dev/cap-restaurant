<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
/**
 * Entité Commande - Représente une commande passée par un utilisateur
 * 
 * Cette entité stocke les informations principales d'une commande :
 * - L'utilisateur qui a passé la commande
 * - Le montant total de la commande
 * - Le statut actuel de la commande (En attente, Payée, Annulée, etc.)
 * - La date de création de la commande
 * - L'ID de session Stripe pour le suivi du paiement
 * - La liste des items commandés (relation avec CommandeItem)
 * 
 * Une commande peut contenir plusieurs items (pizzas, desserts, boissons)
 * et est liée à un utilisateur unique.
 */
#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: CommandeItem::class, cascade: ['persist'])]
    private Collection $items;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSessionId = null;

    public function __construct() {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection|CommandeItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(CommandeItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCommande($this);
        }
        return $this;
    }

    public function removeItem(CommandeItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getCommande() === $this) {
                $item->setCommande(null);
            }
        }
        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(?string $stripeSessionId): static
    {
        $this->stripeSessionId = $stripeSessionId;
        return $this;
    }
}