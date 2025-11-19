<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Ce contrôleur gère les actions liées au panier (page du panier).
 * Il est responsable de l'affichage du contenu du panier et pourra,
 * à terme, inclure des fonctionnalités comme l'ajout, la suppression
 * ou la modification des produits dans le panier.
 */
class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response
    {
        
        return $this->render('panier/index.html.twig', [
            
        ]);
    }
}
