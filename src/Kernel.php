<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
/**
 * Kernel de l'application - Point d'entrée principal de Symfony
 * 
 * Cette classe étend le Kernel de base de Symfony et utilise le MicroKernelTrait
 * pour fournir une configuration flexible de l'application.
 * 
 * Responsabilités :
 * - Chargement de la configuration de l'application
 * - Gestion de l'environnement (dev, prod, test)
 * - Initialisation des bundles et services
 * - Gestion du cache et du conteneur de dépendances
 * 
 * Utilise l'approche MicroKernel pour une configuration moderne et performante
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
