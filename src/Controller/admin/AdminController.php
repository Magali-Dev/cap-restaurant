<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Entity\Reservation;
use App\Service\EmailService;
use App\Service\ReservationLimiterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur d'administration pour la gestion des utilisateurs, réservations et paramètres
 * 
 * Ce contrôleur fournit une interface d'administration complète permettant :
 * - La visualisation des statistiques (dashboard)
 * - La gestion des utilisateurs et de leurs rôles
 * - La gestion des réservations (consultation, modification de statut, suppression)
 * - La configuration des limitations de réservations (dates et heures désactivées)
 * 
 * Sécurisé par le rôle ROLE_ADMIN - accessible uniquement aux administrateurs
 * Toutes les actions sensibles sont protégées par des tokens CSRF
 */
#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // Dashboard 
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'users_count' => $em->getRepository(User::class)->count([]),
            'admins_count' => $em->getRepository(User::class)
                ->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->where('u.roles LIKE :role')
                ->setParameter('role', '%ROLE_ADMIN%')
                ->getQuery()
                ->getSingleScalarResult(),
            'reservations_count' => $em->getRepository(Reservation::class)->count([]),
            'reservations_today' => $em->getRepository(Reservation::class)
                ->createQueryBuilder('r')
                ->select('COUNT(r.id)')
                ->where('r.dateReservation = :today')
                ->setParameter('today', new \DateTime())
                ->getQuery()
                ->getSingleScalarResult(),
        ]);
    }

    //  Users 
    #[Route('/users', name: 'admin_users')]
    public function users(EntityManagerInterface $em): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $em->getRepository(User::class)->findAll(),
        ]);
    }

    #[Route('/user/{id}/make-admin', name: 'admin_user_make_admin', methods: ['POST'])]
    public function makeAdmin(User $user, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('make-admin-'.$user->getId(), $request->request->get('_token'))) {
            $user->setRoles(['ROLE_ADMIN']);
            $em->flush();
            $this->addFlash('success', 'Utilisateur promu administrateur.');
        }

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/user/{id}/remove-admin', name: 'admin_user_remove_admin', methods: ['POST'])]
    public function removeAdmin(User $user, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('remove-admin-'.$user->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('admin_users');
        }

        if ($user->getId() === $this->getUser()->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas retirer vos propres droits.');
        } else {
            $user->setRoles(['ROLE_USER']);
            $em->flush();
            $this->addFlash('success', 'Droits administrateur retirés.');
        }

        return $this->redirectToRoute('admin_users');
    }

    // Reservations 
    #[Route('/reservations', name: 'admin_reservations')]
    public function reservations(EntityManagerInterface $em): Response
    {
        return $this->render('admin/reservations.html.twig', [
            'reservations' => $em->getRepository(Reservation::class)
                ->findBy([], ['dateReservation'=>'ASC', 'heureReservation'=>'ASC']),
        ]);
    }

    #[Route('/reservation/{id}/status', name: 'admin_reservation_status', methods: ['POST'])]
    public function updateReservationStatus(
        Reservation $reservation,
        EntityManagerInterface $em,
        Request $request,
        EmailService $emailService
    ): Response {
        $newStatus = $request->request->get('status');

        if ($this->isCsrfTokenValid('status-reservation-'.$reservation->getId(), $request->request->get('_token'))) {
            $oldStatus = $reservation->getStatut();
            $reservation->setStatut($newStatus);
            $em->flush();

            if ($oldStatus !== $newStatus) {
                $this->sendReservationStatusEmail($reservation, $newStatus, $emailService);
            }

            $this->addFlash('success', 'Statut de la réservation mis à jour.');
        }

        return $this->redirectToRoute('admin_reservations');
    }

    private function sendReservationStatusEmail(Reservation $reservation, string $newStatus, EmailService $emailService): void
    {
        $clientName = trim(($reservation->getPrenom() ?? '') . ' ' . ($reservation->getNom() ?? ''));
        $details = [
            'id' => $reservation->getId(),
            'date' => $reservation->getDateReservation(),
            'time' => $reservation->getHeureReservation(),
            'people' => $reservation->getNombrePersonnes(),
        ];

        $sent = false;
        if ($newStatus === 'Confirmée') {
            $sent = $emailService->sendReservationConfirmation($reservation->getEmail(), $clientName, $details);
        } elseif ($newStatus === 'Annulée') {
            $sent = $emailService->sendReservationCancellation($reservation->getEmail(), $clientName, $details);
        }

        $this->addFlash($sent ? 'success' : 'warning', $sent
            ? 'Email envoyé au client.'
            : 'Statut mis à jour mais échec de l\'envoi d\'email.'
        );
    }

    #[Route('/reservation/{id}/delete', name: 'admin_reservation_delete', methods: ['POST'])]
    public function deleteReservation(Reservation $reservation, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete-reservation-'.$reservation->getId(), $request->request->get('_token'))) {
            $em->remove($reservation);
            $em->flush();
            $this->addFlash('success', 'Réservation supprimée.');
        }

        return $this->redirectToRoute('admin_reservations');
    }

    //  Limitation des réservations 
    #[Route('/reservation-limits', name: 'admin_reservation_limits')]
    public function reservationLimits(Request $request, ReservationLimiterService $limiter): Response
    {
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $this->handleReservationLimitsAction($action, $request, $limiter);
            return $this->redirectToRoute('admin_reservation_limits');
        }

        return $this->render('admin/reservation_limits.html.twig', [
            'status' => $limiter->getLimitationStatus(),
            'all_time_slots' => $limiter->generateTimeSlots(),
        ]);
    }

    private function handleReservationLimitsAction(string $action, Request $request, ReservationLimiterService $limiter): void
    {
        switch ($action) {
            case 'disable_all':
                $limiter->disableOnlineReservation();
                $this->addFlash('success', '✅ Réservations en ligne désactivées.');
                break;
            case 'enable_all':
                $limiter->enableOnlineReservation();
                $this->addFlash('success', '✅ Réservations en ligne réactivées.');
                break;
            case 'disable_hours':
                $hours = $request->request->get('disabled_hours', []);
                $limiter->setDisabledHours(is_array($hours) ? $hours : [$hours]);
                $this->addFlash('success', '✅ Heures désactivées mises à jour.');
                break;
            case 'disable_dates':
                $datesInput = $request->request->get('disabled_dates', '');
                $dates = is_array($datesInput) ? $datesInput : explode(',', $datesInput);
                $dates = array_filter(array_map('trim', $dates));
                $limiter->setDisabledDates($dates);
                $this->addFlash('success', '✅ Dates désactivées mises à jour.');
                break;
        }
    }
}