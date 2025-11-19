<?php

namespace App\Controller\admin;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * AdminEventsController
 * 
 * Ce contrôleur gère l'administration des événements de l'application.
 * Toutes les routes sont protégées par le rôle ROLE_ADMIN.
 * 
 * Fonctionnalités principales :
 * 
 * - Affichage des événements existants (index)
 * - Ajout d'un nouvel événement avec :
 *     - titre, description, date
 *     - upload optionnel d'un fichier/image
 * - Suppression d'un événement, avec suppression du fichier associé si présent
 * 
 * Utilisation :
 *   - Accéder à /admin/events pour la liste des événements
 *   - Ajouter un événement via POST sur /admin/events/ajouter
 *   - Supprimer un événement via POST sur /admin/events/{id}/supprimer
 */
#[Route('/admin/events')]
#[IsGranted('ROLE_ADMIN')]
class AdminEventsController extends AbstractController
{
    private string $uploadDir = 'uploads/events';

    #[Route('/', name: 'admin_events')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy([], ['dateEvenement' => 'DESC']);

        return $this->render('admin/events.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/ajouter', name: 'admin_events_add', methods: ['POST'])]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        $titre = $request->request->get('titre');
        $description = $request->request->get('description');
        $date = $request->request->get('date');
        $fichier = $request->files->get('fichier');

        if (!$titre || !$date) {
            $this->addFlash('warning', 'Veuillez renseigner le titre et la date de l’événement.');
            return $this->redirectToRoute('admin_events');
        }

        $event = new Event();
        $event->setTitre($titre);
        $event->setDescription($description);
        $event->setDateEvenement(new \DateTime($date));

        // Gestion du fichier
        if ($fichier) {
            $uploadPath = $this->getParameter('kernel.project_dir') . '/public/' . $this->uploadDir;

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $nomFichier = uniqid() . '.' . $fichier->guessExtension();

            try {
                $fichier->move($uploadPath, $nomFichier);
                $event->setNomFichier($nomFichier);
            } catch (FileException $e) {
                $this->addFlash('danger', 'Erreur lors de l’upload du fichier.');
            }
        }

        $em->persist($event);
        $em->flush();

        $this->addFlash('success', 'Événement ajouté avec succès !');

        return $this->redirectToRoute('admin_events');
    }

    #[Route('/{id}/supprimer', name: 'admin_events_delete', methods: ['POST'])]
    public function supprimer(Event $event, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete-event-' . $event->getId(), $request->request->get('_token'))) {

            // Supprimer l'image associée si elle existe
            if ($event->getNomFichier()) {
                $chemin = $this->getParameter('kernel.project_dir') . '/public/' . $this->uploadDir . '/' . $event->getNomFichier();
                if (file_exists($chemin)) {
                    unlink($chemin);
                }
            }

            $em->remove($event);
            $em->flush();

            $this->addFlash('success', 'Événement supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_events');
    }
}
