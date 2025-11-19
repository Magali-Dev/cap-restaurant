<?php namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pizza;
use App\Repository\PizzaRepository;
use App\Repository\SupplementRepository;
/**
 * Ce contrôleur gère l'affichage des pizzas et leurs détails.
 * Il permet :
 *  - d'afficher la liste complète des pizzas disponibles,
 *  - de consulter la fiche détaillée d'une pizza spécifique,
 *  - et d'afficher la liste des suppléments associés.
 * 
 * Ce contrôleur interagit avec les dépôts (repositories) PizzaRepository
 * et SupplementRepository pour récupérer les données depuis la base.
 */
class PizzaController extends AbstractController
{
    #[Route('/pizzas', name: 'pizza_list')]
    public function index(PizzaRepository $pizzaRepository, SupplementRepository $supplementRepository): Response
    {
        $pizzas = $pizzaRepository->findAll();
        $supplements = $supplementRepository->findAll();

        return $this->render('pizza/pizzas.html.twig', [
            'pizzas' => $pizzas,
            'supplements' => $supplements,
        ]);
    }

    #[Route('/pizza/{id}', name: 'pizza_show')]
    public function show(Pizza $pizza, SupplementRepository $supplementRepository): Response
    {
        // Récupère tous les suppléments
        $supplements = $supplementRepository->findAll();

        return $this->render('pizza/show.html.twig', [
            'pizza' => $pizza,
            'supplements' => $supplements,
        ]);
    }
}
