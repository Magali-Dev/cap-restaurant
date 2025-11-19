<?php

namespace App\Controller\admin;

use App\Entity\Supplement;
use App\Form\SupplementType;
use App\Repository\SupplementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * AdminSupplementController
 * 
 * Ce contrôleur permet aux administrateurs de gérer les suppléments disponibles pour les pizzas ou menus.
 * Toutes les actions sont accessibles uniquement aux administrateurs.
 * 
 * Fonctionnalités principales :
 * 
 * - Affichage de tous les suppléments (index)
 * - Création d'un nouveau supplément avec formulaire
 * - Modification d'un supplément existant avec formulaire
 * - Suppression d'un supplément avec protection CSRF
 */
#[Route('/admin/supplements')]
#[IsGranted('ROLE_ADMIN')]
class AdminSupplementController extends AbstractController
{
    #[Route('/', name: 'admin_supplements_index', methods: ['GET'])]
    public function index(SupplementRepository $supplementRepository): Response
    {
        $supplements = $supplementRepository->findAll();

        return $this->render('admin/supplement/index.html.twig', [
            'supplements' => $supplements,
        ]);
    }

    #[Route('/new', name: 'admin_supplements_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $supplement = new Supplement();
        $form = $this->createForm(SupplementType::class, $supplement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($supplement);
            $em->flush();

            $this->addFlash('success', '✅ Supplément ajouté avec succès !');
            return $this->redirectToRoute('admin_supplements_index');
        }

        return $this->render('admin/supplement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_supplements_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Supplement $supplement, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SupplementType::class, $supplement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', '✏️ Supplément modifié avec succès !');
            return $this->redirectToRoute('admin_supplements_index');
        }

        return $this->render('admin/supplement/edit.html.twig', [
            'supplement' => $supplement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_supplements_delete', methods: ['POST'])]
    public function delete(Request $request, Supplement $supplement, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$supplement->getId(), $request->request->get('_token'))) {
            $em->remove($supplement);
            $em->flush();
            $this->addFlash('success', '🗑️ Supplément supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_supplements_index');
    }
}