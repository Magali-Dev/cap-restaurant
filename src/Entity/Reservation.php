<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
/**
 * Entité Reservation - Gestion des réservations de table en ligne
 * 
 * Cette entité permet aux clients de réserver une table dans la pizzeria :
 * - Informations personnelles (prénom, nom, téléphone, email)
 * - Détails de la réservation (nombre de personnes, date, heure)
 * - Informations complémentaires (allergies, message spécial)
 * - Suivi du statut de la réservation (En attente, Confirmée, Annulée)
 * - Date de création de la réservation
 * 
 * Le système gère automatiquement la conversion des heures et fournit
 * un statut par défaut "En attente" nécessitant une validation manuelle
 * par l'administrateur
 */
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $nombrePersonnes = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateReservation = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heureReservation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $creeLe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $allergie = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(length: 20)]
    private ?string $statut = 'En attente';

    // --- Getters & Setters ---
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getNombrePersonnes(): ?int
    {
        return $this->nombrePersonnes;
    }

    public function setNombrePersonnes(?int $nombrePersonnes): static
    {
        $this->nombrePersonnes = $nombrePersonnes;

        return $this;
    }

    public function getDateReservation(): ?\DateTime
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTime $dateReservation): static
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getHeureReservation(): ?\DateTime
    {
        return $this->heureReservation;
    }

    public function setHeureReservation($heure): static
    {
        if (is_string($heure)) {
            $converted = \DateTime::createFromFormat('H:i', $heure);
            if (false !== $converted) {
                $heure = $converted;
            }
        }
        $this->heureReservation = $heure;

        return $this;
    }

    public function getCreeLe(): ?\DateTime
    {
        return $this->creeLe;
    }

    public function setCreeLe(?\DateTime $creeLe): static
    {
        $this->creeLe = $creeLe;

        return $this;
    }

    public function getAllergie(): ?string
    {
        return $this->allergie;
    }

    public function setAllergie(?string $allergie): static
    {
        $this->allergie = $allergie;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}