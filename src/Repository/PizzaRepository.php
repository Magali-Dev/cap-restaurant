<?php

namespace App\Repository;

use App\Entity\Pizza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * Repository Pizza - Gestion des requêtes database pour les pizzas
 * 
 * Ce repository fournit les méthodes pour interagir avec la table des pizzas :
 * - Récupération des pizzas avec différents critères de filtrage
 * - Recherche de pizzas spécifiques par nom, ingrédients ou prix
 * - Filtrage par caractéristiques (végétariennes, allergènes)
 * - Tri par nom, prix ou popularité
 * - Limitation des résultats pour l'affichage du menu
 * 
 * Étend ServiceEntityRepository pour bénéficier des méthodes Doctrine standards
 * et permet d'ajouter des méthodes personnalisées pour des requêtes spécifiques
 * comme la récupération des pizzas végétariennes ou avec certains suppléments
 * 
 * @extends ServiceEntityRepository<Pizza>
 */

class PizzaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pizza::class);
    }

    //    /**
    //     * @return Pizza[] Returns an array of Pizza objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Pizza
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
