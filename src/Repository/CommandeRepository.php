<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * Repository Commande - Gestion des requêtes database pour les commandes
 * 
 * Ce repository fournit les méthodes pour interagir avec la table des commandes :
 * - Récupération des commandes avec différents critères de filtrage
 * - Recherche de commandes spécifiques par leurs attributs
 * - Tri et limitation des résultats de requêtes
 * 
 * Étend ServiceEntityRepository pour bénéficier des méthodes Doctrine standards
 * (find, findAll, findBy, findOneBy, etc.) et permet d'ajouter des méthodes
 * personnalisées pour des requêtes métier spécifiques
 * 
 * @extends ServiceEntityRepository<Commande>
 */

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    //    /**
    //     * @return Commande[] Returns an array of Commande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Commande
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
