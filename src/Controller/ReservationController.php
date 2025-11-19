<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Service\ReservationLimiterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de gestion des réservations en ligne pour le restaurant.
 *
 * Ce contrôleur permet :
 * 1. new() : créer une nouvelle réservation via le formulaire en ligne,
 *    avec vérification des créneaux disponibles, du nombre de personnes par réservation
 *    et du quota total par créneau.
 * 2. creneauxDisponibles() : retourner en JSON les créneaux disponibles et les places restantes
 *    pour une date donnée, utilisé pour le formulaire dynamique.
 * 3. limitation() : interface d'administration pour activer/désactiver les réservations en ligne,
 *    désactiver certains créneaux horaires ou certaines dates, via le service ReservationLimiterService.
 *
 * Le contrôleur utilise :
 * - ReservationRepository via EntityManager pour récupérer et gérer les réservations.
 * - ReservationLimiterService pour gérer les restrictions de créneaux et d'accès en ligne.
 */
class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'reservation_new')]
    public function new(Request $request, EntityManagerInterface $em, ReservationLimiterService $limiterService): Response
    {
        $reservationsDisabled = !$limiterService->isOnlineReservationEnabled();

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($reservationsDisabled) {
            $this->addFlash('danger', 'Les réservations en ligne sont temporairement désactivées.');
            return $this->render('reservation/new.html.twig', [
                'form' => $form->createView(),
                'limiter_service' => $limiterService,
                'reservations_disabled' => true,
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $date = $reservation->getDateReservation();
            $heure = $reservation->getHeureReservation();

            if (is_string($heure)) {
                $heure = \DateTime::createFromFormat('H:i', $heure);
                $reservation->setHeureReservation($heure);
            }

            // Vérification disponibilité du créneau
            if (!$limiterService->isDateTimeAllowed($date, $heure)) {
                $this->addFlash('danger', 'Ce créneau n\'est pas disponible.');
                return $this->redirectToRoute('reservation_new');
            }

            // Limite de personnes
            if ($reservation->getNombrePersonnes() > 14) {
                $this->addFlash('danger', 'Maximum 14 personnes par réservation.');
                return $this->redirectToRoute('reservation_new');
            }

            // Calcul quota par créneau
            $totalPlaces = $em->getRepository(Reservation::class)
                ->createQueryBuilder('r')
                ->select('SUM(r.nombrePersonnes)')
                ->where('r.dateReservation = :date')
                ->andWhere('r.heureReservation = :heure')
                ->setParameter('date', $date)
                ->setParameter('heure', $heure)
                ->getQuery()
                ->getSingleScalarResult() ?? 0;

            if ($totalPlaces + $reservation->getNombrePersonnes() > 40) {
                $placesRestantes = max(0, 40 - $totalPlaces);
                $this->addFlash('danger', "Il ne reste plus que $placesRestantes place(s) pour ce créneau.");
                return $this->redirectToRoute('reservation_new');
            }

            // Enregistrement
            $reservation->setCreeLe(new \DateTime());
            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', 'Votre réservation a été enregistrée.');
            return $this->redirectToRoute('reservation_new');
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
            'limiter_service' => $limiterService,
            'reservations_disabled' => false,
        ]);
    }

    #[Route('/reservation/creneaux-disponibles', name: 'reservation_creneaux_disponibles')]
    public function creneauxDisponibles(Request $request, EntityManagerInterface $em, ReservationLimiterService $limiterService): JsonResponse
    {
        $date = $request->query->get('date');
        if (!$date) {
            return new JsonResponse(['error' => 'Date manquante'], 400);
        }

        $dateObj = new \DateTime($date);

        $horaires = ['12:00','12:30','13:00','13:30','14:00','19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30'];
        $placesRestantes = [];

        foreach ($horaires as $horaire) {
            $heureObj = \DateTime::createFromFormat('H:i', $horaire);
            $isAllowed = $limiterService->isDateTimeAllowed($dateObj, $heureObj);

            $total = $em->getRepository(Reservation::class)
                ->createQueryBuilder('r')
                ->select('SUM(r.nombrePersonnes)')
                ->where('r.dateReservation = :date')
                ->andWhere('r.heureReservation = :heure')
                ->setParameter('date', $dateObj)
                ->setParameter('heure', $heureObj)
                ->getQuery()
                ->getSingleScalarResult() ?? 0;

            $placesRestantes[$horaire] = $isAllowed ? max(0, 40 - $total) : 0;
        }

        return new JsonResponse([
            'placesRestantesParCreneau' => $placesRestantes,
            'online_enabled' => $limiterService->isOnlineReservationEnabled()
        ]);
    }

    #[Route('/admin/reservation-limits', name: 'admin_reservation_limitation')]
    public function limitation(Request $request, ReservationLimiterService $limiterService): Response
    {
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');

            switch ($action) {
                case 'disable_all':
                    $limiterService->setOnlineReservation(false);
                    $this->addFlash('success', 'Réservations désactivées.');
                    break;

                case 'enable_all':
                    $limiterService->setOnlineReservation(true);
                    $this->addFlash('success', 'Réservations activées.');
                    break;

                case 'disable_hours':
                    $hours = $request->request->all('disabled_hours');
                    $limiterService->setDisabledHours(is_array($hours) ? $hours : []);
                    $this->addFlash('success', 'Créneaux horaires mis à jour.');
                    break;

                case 'disable_dates':
                    $datesInput = $request->request->get('disabled_dates', '');
                    $dates = $datesInput ? explode(',', $datesInput) : [];
                    $limiterService->setDisabledDates($dates);
                    $this->addFlash('success', 'Dates spécifiques mises à jour.');
                    break;
            }

            return $this->redirectToRoute('admin_reservation_limitation');
        }

        // GET request - afficher la page
        return $this->render('admin/reservation_limits.html.twig', [
            'status' => [
                'online_enabled' => $limiterService->isOnlineReservationEnabled(),
                'disabled_hours' => $limiterService->getDisabledHours(),
                'disabled_dates' => $limiterService->getDisabledDates(),
            ],
            'all_time_slots' => ['12:00','12:30','13:00','13:30','14:00','19:00','19:30','20:00','20:30','21:00','21:30','22:00','22:30']
        ]);
    }
}