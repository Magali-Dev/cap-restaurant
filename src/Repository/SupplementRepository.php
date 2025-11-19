<?php

namespace App\Repository;

use App\Entity\Supplement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * Repository Supplement - Gestion des requêtes database pour les suppléments
 * 
 * Ce repository fournit les méthodes pour interagir avec la table des suppléments :
 * - Récupération des suppléments avec différents critères de filtrage
 * - Recherche de suppléments par nom, prix ou association avec des pizzas
 * - Tri par nom, prix ou popularité d'utilisation
 * - Gestion des relations ManyToMany avec les entités Pizza
 * 
 * Étend ServiceEntityRepository pour bénéficier des méthodes Doctrine standards
 * et permet d'ajouter des méthodes personnalisées pour des requêtes spécifiques
 * comme la récupération des suppléments les plus populaires ou par catégorie
 * 
 * @extends ServiceEntityRepository<Supplement>
 */

class SupplementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Supplement::class);
    }
}
