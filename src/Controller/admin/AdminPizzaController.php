<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pizza;
use App\Repository\PizzaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * AdminPizzaController
 * 
 * Ce contrôleur permet aux administrateurs de gérer les pizzas du site.
 * Toutes les routes sont accessibles uniquement aux administrateurs.
 * 
 * Fonctionnalités principales :
 * 
 * - Affichage de toutes les pizzas (index)
 * - Ajout d’une nouvelle pizza avec :
 *     - Nom et prix obligatoires
 *     - Description, image, végétarien et allergènes optionnels
 * - Modification d’une pizza existante, avec possibilité de changer l’image
 * - Suppression d’une pizza, y compris l’image associée
 * 
 * Utilisation :
 *   - Accéder à /admin/pizzas/ pour voir la liste des pizzas
 *   - Ajouter une pizza via POST sur /admin/pizzas/ajouter
 *   - Modifier une pizza via POST sur /admin/pizzas/modifier/{id}
 *   - Supprimer une pizza via /admin/pizzas/supprimer/{id}
 */
#[Route('/admin/pizzas', name:'admin_pizzas_')]
class AdminPizzaController extends AbstractController
{
    #[Route('/', name:'index')]
    public function index(PizzaRepository $repo): Response
    {
        $pizzas = $repo->findAll();
        return $this->render('admin/pizzas.html.twig', ['pizzas'=>$pizzas]);
    }

    #[Route('/ajouter', name:'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prix = $request->request->get('prix');

            if (!$nom || !$prix) {
                $this->addFlash('danger', 'Nom et prix obligatoires.');
                return $this->redirectToRoute('admin_pizzas_add');
            }

            $pizza = new Pizza();
            $pizza->setNom($nom);
            $pizza->setPrix((float)$prix);
            $pizza->setVegetarien($request->request->get('vegetarien') ? true : false);
            $pizza->setDescription($request->request->get('description'));
            $allergenes = $request->request->get('allergenes');
            $pizza->setAllergenes($allergenes ? explode(',', $allergenes) : null);

            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $filename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('pizza_images_directory'), $filename);
                    $pizza->setImage($filename);
                } catch (FileException $e) {
                    $this->addFlash('danger','Erreur lors de l’upload de l’image.');
                    return $this->redirectToRoute('admin_pizzas_add');
                }
            }

            $em->persist($pizza);
            $em->flush();
            $this->addFlash('success','Pizza ajoutée avec succès !');
            return $this->redirectToRoute('admin_pizzas_index');
        }

        return $this->render('admin/ajouter_pizza.html.twig');
    }

    #[Route('/modifier/{id}', name:'edit')]
    public function edit(Pizza $pizza, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $pizza->setNom($request->request->get('nom') ?: $pizza->getNom());
            $pizza->setPrix((float)($request->request->get('prix') ?: $pizza->getPrix()));
            $pizza->setVegetarien($request->request->get('vegetarien') ? true : false);
            $pizza->setDescription($request->request->get('description'));
            $allergenes = $request->request->get('allergenes');
            $pizza->setAllergenes($allergenes ? explode(',', $allergenes) : null);

            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $filename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('pizza_images_directory'), $filename);
                    
                    if ($pizza->getImage()) {
                        $old = $this->getParameter('pizza_images_directory') . '/' . $pizza->getImage();
                        if (file_exists($old)) unlink($old);
                    }
                    $pizza->setImage($filename);
                } catch (FileException $e) {
                    $this->addFlash('danger','Erreur upload image');
                    return $this->redirectToRoute('admin_pizzas_edit', ['id'=>$pizza->getId()]);
                }
            }

            $em->flush();
            $this->addFlash('success','Pizza modifiée !');
            return $this->redirectToRoute('admin_pizzas_index');
        }

        return $this->render('admin/edit_pizza.html.twig',['pizza'=>$pizza]);
    }

    #[Route('/supprimer/{id}', name:'delete')]
    public function delete(Pizza $pizza, EntityManagerInterface $em): Response
    {
        if ($pizza->getImage()) {
            $path = $this->getParameter('pizza_images_directory') . '/' . $pizza->getImage();
            if (file_exists($path)) unlink($path);
        }

        $em->remove($pizza);
        $em->flush();
        $this->addFlash('success','Pizza supprimée !');
        return $this->redirectToRoute('admin_pizzas_index');
    }
}
