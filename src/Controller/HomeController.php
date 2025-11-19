<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\ImageRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * HomeController
 * 
 * Contrôleur pour la page d'accueil du site.
 * 
 * Fonctionnalités principales :
 * - Récupération et affichage des menus disponibles
 * - Récupération et affichage des images de la galerie
 * - Récupération et affichage des événements à venir
 * 
 * Accès :
 * - Disponible publiquement via la route "/"
 */
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        MenuRepository $menuRepository, 
        ImageRepository $imageRepository,
        EventRepository $eventRepository
    ): Response
    {
        $menus = $menuRepository->findBy([], ['id' => 'DESC']);
        $images = $imageRepository->findBy([], ['dateCreation' => 'DESC']);
        $events = $eventRepository->findBy([], ['dateEvenement' => 'ASC']); 

        return $this->render('home/index.html.twig', [
            'menus' => $menus,
            'images' => $images,
            'events' => $events,
        ]);
    }
}
