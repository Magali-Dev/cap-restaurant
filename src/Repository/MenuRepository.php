<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * Repository Menu - Gestion des requêtes database pour les menus
 * 
 * Ce repository fournit les méthodes pour interagir avec la table des menus :
 * - Récupération des menus avec différents critères de filtrage
 * - Recherche de menus spécifiques par titre, description ou contenu
 * - Tri par date de création ou autre attribut
 * - Limitation des résultats pour l'affichage du catalogue
 * 
 * Étend ServiceEntityRepository pour bénéficier des méthodes Doctrine standards
 * et permet d'ajouter des méthodes personnalisées pour des requêtes spécifiques
 * comme la récupération des menus actifs ou la recherche par prix
 * 
/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    //    /**
    //     * @return Menu[] Returns an array of Menu objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Menu
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
