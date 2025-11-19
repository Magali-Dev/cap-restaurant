<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * GalleryController
 * 
 * Contrôleur pour gérer la galerie publique du site.
 * 
 * Fonctionnalités principales :
 * - Affichage de toutes les images de la galerie
 * - Les images sont triées par date de création décroissante
 * 
 * Accès :
 * - Disponible publiquement via la route "/gallery"
 */
class GalleryController extends AbstractController
{
    #[Route('/gallery', name: 'gallery_public')]
    public function index(ImageRepository $imageRepository): Response
    {
        $images = $imageRepository->findBy([], ['dateCreation' => 'DESC']);

        return $this->render('home/gallery.html.twig', [
            'images' => $images,
        ]);
    }
}
