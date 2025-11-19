<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * Repository Image - Gestion des requêtes database pour les images
 * 
 * Ce repository fournit les méthodes pour interagir avec la table des images :
 * - Récupération des images avec différents critères de filtrage
 * - Recherche d'images spécifiques par titre, description ou nom de fichier
 * - Tri par date de création ou autre attribut
 * - Limitation des résultats pour la galerie ou l'affichage
 * 
 * Étend ServiceEntityRepository pour bénéficier des méthodes Doctrine standards
 * et permet d'ajouter des méthodes personnalisées pour des requêtes spécifiques
 * comme la récupération des images récentes ou la recherche par mot-clé
 * 
/**
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    //    /**
    //     * @return Image[] Returns an array of Image objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Image
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
