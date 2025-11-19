<?php namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
/**
 * Ce contrôleur gère les fonctionnalités liées à l’espace personnel de l’utilisateur.
 * 
 * Il permet :
 *  - d’afficher la page de profil de l’utilisateur connecté ;
 *  - de sécuriser l’accès à cette page (réservée aux utilisateurs authentifiés).
 * 
 * Ce contrôleur utilise :
 *  - le composant de sécurité de Symfony pour obtenir l’utilisateur courant ;
 *  - et un template Twig (`user/profile.html.twig`) pour afficher les informations du profil.
 */

class UserController extends AbstractController
{
    #[Route('/mon-compte', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(): Response
    {
        $user = $this->getUser();
        
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }
}