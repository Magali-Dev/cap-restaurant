<?php

namespace App\Controller\admin;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * AdminGalerieController
 * 
 * Ce contrôleur permet aux administrateurs de gérer la galerie d'images du site.
 * Toutes les routes sont protégées par le rôle ROLE_ADMIN.
 * 
 * Fonctionnalités principales :
 * 
 * - Affichage de toutes les images dans la galerie (index)
 * - Ajout d'une nouvelle image avec :
 *     - fichier image obligatoire
 *     - titre et description optionnels
 * - Suppression d'une image existante, avec suppression du fichier associé
 * 
 * Utilisation :
 *   - Accéder à /admin/galerie pour voir la liste des images
 *   - Ajouter une image via POST sur /admin/galerie/ajouter
 *   - Supprimer une image via POST sur /admin/galerie/{id}/supprimer
 */
#[Route('/admin/galerie')]
#[IsGranted('ROLE_ADMIN')]
class AdminGalerieController extends AbstractController
{
    private string $uploadDir = 'uploads/gallery';

    #[Route('/', name: 'admin_gallery')]
    public function index(ImageRepository $imageRepository): Response
    {
        $images = $imageRepository->findBy([], ['dateCreation' => 'DESC']);

        return $this->render('admin/gallery.html.twig', [
            'images' => $images,
        ]);
    }

    #[Route('/ajouter', name: 'admin_gallery_add', methods: ['POST'])]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        $fichier = $request->files->get('image_file');
        $titre = $request->request->get('titre');
        $description = $request->request->get('description');

        if ($fichier) {
            $nomFichier = uniqid() . '.' . $fichier->guessExtension();

            try {
                $fichier->move(
                    $this->getParameter('kernel.project_dir') . '/public/' . $this->uploadDir, 
                    $nomFichier
                );
            } catch (FileException $e) {
                $this->addFlash('danger', 'Erreur lors de l’upload du fichier.');
                return $this->redirectToRoute('admin_gallery');
            }

            $image = new Image();
            $image->setNomFichier($nomFichier);
            $image->setTitre($titre);
            $image->setDescription($description);

            $em->persist($image);
            $em->flush();

            $this->addFlash('success', 'Image ajoutée avec succès !');
        } else {
            $this->addFlash('warning', 'Veuillez sélectionner un fichier.');
        }

        return $this->redirectToRoute('admin_gallery');
    }

    #[Route('/{id}/supprimer', name: 'admin_gallery_delete', methods: ['POST'])]
    public function supprimer(Image $image, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete-image-' . $image->getId(), $request->request->get('_token'))) {
            $cheminFichier = $this->getParameter('kernel.project_dir') . '/public/' . $this->uploadDir . '/' . $image->getNomFichier();
            if (file_exists($cheminFichier)) {
                unlink($cheminFichier);
            }

            $em->remove($image);
            $em->flush();

            $this->addFlash('success', 'Image supprimée avec succès !');
        }

        return $this->redirectToRoute('admin_gallery');
    }
    #[Route('/{id}/modifier', name: 'admin_gallery_edit', methods: ['POST'])]
public function modifier(Image $image, Request $request, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('edit-image-' . $image->getId(), $request->request->get('_token'))) {
        $titre = $request->request->get('titre');
        $description = $request->request->get('description');
        $fichier = $request->files->get('image_file');

        // Mettre à jour les champs texte
        $image->setTitre($titre);
        $image->setDescription($description);

        // Gérer le changement de fichier si un nouveau fichier est uploadé
        if ($fichier && $fichier->getClientOriginalName() !== '') {
           $ancienFichier = $this->getParameter('kernel.project_dir') . '/public/' . $this->uploadDir . '/' . $image->getNomFichier();
            if (file_exists($ancienFichier)) {
                unlink($ancienFichier);
            }

            // Uploader le nouveau fichier
            $nomFichier = uniqid() . '.' . $fichier->guessExtension();
            try {
                $fichier->move(
                    $this->getParameter('kernel.project_dir') . '/public/' . $this->uploadDir, 
                    $nomFichier
                );
                $image->setNomFichier($nomFichier);
            } catch (FileException $e) {
                $this->addFlash('danger', 'Erreur lors du remplacement du fichier.');
                return $this->redirectToRoute('admin_gallery');
            }
        }

        $em->flush();
        $this->addFlash('success', 'Image modifiée avec succès !');
    }

    return $this->redirectToRoute('admin_gallery');
}
}
