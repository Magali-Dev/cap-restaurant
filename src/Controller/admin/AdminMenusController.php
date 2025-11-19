<?php

namespace App\Controller\admin;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Contrôleur d'administration pour gérer les menus du restaurant.
// Ce contrôleur permet :
// - d'afficher la liste de tous les menus (index)
// - d'ajouter un nouveau menu (ajouter)
// - de modifier un menu existant (modifier)
// - de supprimer un menu (supprimer)
// Il utilise le MenuRepository pour récupérer les données et l'EntityManager pour gérer la persistance.
// Les routes sont préfixées par /admin/menus et chaque action a sa propre route nommée.

#[Route('/admin/menus')]
class AdminMenusController extends AbstractController
{
    #[Route('/', name: 'admin_menus')]
    public function index(MenuRepository $menuRepository): Response
    {
        $menus = $menuRepository->findAll();
        return $this->render('admin/menus.html.twig', [
            'menus' => $menus,
        ]);
    }

    #[Route('/ajouter', name: 'admin_menus_add', methods: ['POST'])]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        $nom = $request->request->get('nom');
        
        if (empty($nom)) {
            $this->addFlash('error', 'Le nom du menu est obligatoire.');
            return $this->redirectToRoute('admin_menus');
        }

        $menu = new Menu();
        $menu->setTitre($nom);
        $menu->setDescription($request->request->get('description'));

        
        $fichier = $request->files->get('image_file');
        if ($fichier) {
            $nomFichier = 'menu-' . uniqid() . '.jpg';
            $dossierUpload = $this->getParameter('kernel.project_dir') . '/public/uploads/menus/';
            
            
            if (!is_dir($dossierUpload)) {
                mkdir($dossierUpload, 0777, true);
            }
            
            
            $fichier->move($dossierUpload, $nomFichier);
            $menu->setNomFichier($nomFichier);
        }

        $em->persist($menu);
        $em->flush();

        $this->addFlash('success', 'Menu ajouté avec succès !');
        return $this->redirectToRoute('admin_menus');
    }

    #[Route('/{id}/modifier', name: 'admin_menus_edit', methods: ['POST'])]
    public function modifier(Menu $menu, Request $request, EntityManagerInterface $em): Response
    {
        $nom = $request->request->get('nom');
        
        if (empty($nom)) {
            $this->addFlash('error', 'Le nom du menu est obligatoire.');
            return $this->redirectToRoute('admin_menus');
        }

        $menu->setTitre($nom);
        $menu->setDescription($request->request->get('description'));

        $em->flush();
        $this->addFlash('success', 'Menu modifié avec succès !');
        
        return $this->redirectToRoute('admin_menus');
    }

    #[Route('/{id}/supprimer', name: 'admin_menus_delete', methods: ['POST'])]
    public function supprimer(Menu $menu, EntityManagerInterface $em): Response
    {
        $em->remove($menu);
        $em->flush();

        $this->addFlash('success', 'Menu supprimé avec succès !');
        return $this->redirectToRoute('admin_menus');
    }
}