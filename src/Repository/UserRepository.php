<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * Repository User - Gestion des requêtes database pour les utilisateurs
 * 
 * Ce repository fournit les méthodes pour interagir avec la table des utilisateurs :
 * - Récupération des utilisateurs avec différents critères de filtrage
 * - Recherche d'utilisateurs par email, nom, prénom ou téléphone
 * - Filtrage par rôle (utilisateurs standard, administrateurs)
 * - Tri par date d'inscription, nom ou autre attribut
 * - Gestion des relations avec les commandes des utilisateurs
 * 
 * Étend ServiceEntityRepository pour bénéficier des méthodes Doctrine standards
 * et permet d'ajouter des méthodes personnalisées pour des requêtes spécifiques
 * comme la recherche d'utilisateurs par commandes récentes ou statistiques
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // Tu peux ajouter ici tes méthodes personnalisées si besoin
}
