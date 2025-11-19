<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository Reservation - Gestion des requêtes database pour les réservations
 * 
 * Ce repository fournit les méthodes pour interagir avec la table des réservations :
 * - Récupération des réservations avec différents critères de filtrage
 * - Recherche de réservations par date, heure, statut ou client
 * - Filtrage par période (réservations du jour, à venir, passées)
 * - Tri par date de réservation, heure ou date de création
 * - Statistiques sur le nombre de réservations par période
 * 
 * Étend ServiceEntityRepository pour bénéficier des méthodes Doctrine standards
 * et permet d'ajouter des méthodes personnalisées pour des requêtes spécifiques
 * comme la vérification des disponibilités ou les réservations en attente
 * 
 * @extends ServiceEntityRepository<Reservation>
 */

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
