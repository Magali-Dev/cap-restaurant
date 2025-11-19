<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
/**
 * Service d'envoi d'emails - Gestion des notifications par email
 * 
 * Ce service gère l'envoi de tous les emails de l'application :
 * - Confirmations de réservation aux clients et au gérant
 * - Notifications d'annulation de réservation
 * - Emails transactionnels avec templates HTML personnalisés
 * 
 * Utilise le composant Mailer de Symfony avec gestion robuste des erreurs
 * et templates responsifs pour une expérience utilisateur optimale
 */
class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private string $adminEmail
    ) {}

    public function sendReservationConfirmation(string $clientEmail, string $clientName, array $reservationDetails): bool
    {
        try {
            // Email au client
            $clientEmailObj = (new Email())
                ->from(new Address($this->adminEmail, 'Le Cap Ristobar'))
                ->to($clientEmail)
                ->subject('🎉 Confirmation de votre réservation - Le Cap Ristobar')
                ->html($this->getClientConfirmationTemplate($clientName, $reservationDetails));

            $this->mailer->send($clientEmailObj);

            // Email au gérant
            $managerEmail = (new Email())
                ->from(new Address($this->adminEmail, 'Système de Réservation'))
                ->to($this->adminEmail)
                ->subject('✅ Réservation confirmée #' . $reservationDetails['id'])
                ->html($this->getManagerConfirmationTemplate($clientName, $reservationDetails));

            $this->mailer->send($managerEmail);

            return true;
        } catch (\Exception $e) {
            error_log("❌ [EMAIL ERROR] Confirmation failed: " . $e->getMessage());
            return false;
        }
    }

    public function sendReservationCancellation(string $clientEmail, string $clientName, array $reservationDetails): bool
    {
        try {
            // Email au client
            $clientEmailObj = (new Email())
                ->from(new Address($this->adminEmail, 'Le Cap Ristobar'))
                ->to($clientEmail)
                ->subject('❌ Annulation de votre réservation - Le Cap Ristobar')
                ->html($this->getClientCancellationTemplate($clientName, $reservationDetails));

            $this->mailer->send($clientEmailObj);

            // Email au gérant
            $managerEmail = (new Email())
                ->from(new Address($this->adminEmail, 'Système de Réservation'))
                ->to($this->adminEmail)
                ->subject('❌ Réservation annulée #' . $reservationDetails['id'])
                ->html($this->getManagerCancellationTemplate($clientName, $reservationDetails));

            $this->mailer->send($managerEmail);

            return true;
        } catch (\Exception $e) {
            error_log("❌ [EMAIL ERROR] Cancellation failed: " . $e->getMessage());
            return false;
        }
    }

    private function getClientConfirmationTemplate(string $clientName, array $reservationDetails): string
    {
        $date = $reservationDetails['date']->format('d/m/Y');
        $time = $reservationDetails['time']->format('H:i');
        $people = $reservationDetails['people'];
        $reservationId = $reservationDetails['id'];

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0e2c35; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8fafc; padding: 30px; border-radius: 0 0 10px 10px; }
                .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
                .success-badge { background: #10b981; color: white; padding: 10px 20px; border-radius: 20px; display: inline-block; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Le Cap Ristobar</h1>
                    <p>Bar • Restaurant • Pizzeria • Événements</p>
                </div>
                <div class="content">
                    <div class="success-badge">
                        ✅ RÉSERVATION CONFIRMÉE
                    </div>
                    
                    <h2>Bonjour {$clientName},</h2>
                    
                    <p>Votre réservation a été <strong>confirmée</strong> avec succès !</p>
                    
                    <div class="details">
                        <h3>📅 Détails de votre réservation :</h3>
                        <p><strong>Numéro de réservation :</strong> #{$reservationId}</p>
                        <p><strong>Date :</strong> {$date}</p>
                        <p><strong>Heure :</strong> {$time}</p>
                        <p><strong>Nombre de personnes :</strong> {$people}</p>
                    </div>
                    
                    <p>Nous sommes impatients de vous accueillir au <strong>Le Cap Ristobar</strong> !</p>
                    
                    <p><strong>Informations importantes :</strong></p>
                    <ul>
                        <li>Merci d'arriver 5 minutes avant l'heure réservée</li>
                        <li>En cas de retard, merci de nous prévenir au 05 49 53 28 76</li>
                        <li>En cas d'annulation, merci de nous contacter au moins 24 heures à l'avance</li>
                    </ul>
                    
                    <p><strong>Adresse :</strong><br>
                    Le Cap Ristobar<br>
                    38 Rue de Magenta<br>
                    86000 Poitiers</p>
                    
                    <div class="footer">
                        <p>À très bientôt !<br>
                        L'équipe du Cap Ristobar</p>
                        <p>📞 05 49 53 28 76 | ✉️ lecapristobar86@gmail.com</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function getClientCancellationTemplate(string $clientName, array $reservationDetails): string
    {
        $date = $reservationDetails['date']->format('d/m/Y');
        $time = $reservationDetails['time']->format('H:i');
        $reservationId = $reservationDetails['id'];

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0e2c35; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8fafc; padding: 30px; border-radius: 0 0 10px 10px; }
                .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
                .cancellation-badge { background: #ef4444; color: white; padding: 10px 20px; border-radius: 20px; display: inline-block; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Le Cap Ristobar</h1>
                    <p>Bar • Restaurant • Pizzeria • Événements</p>
                </div>
                <div class="content">
                    <div class="cancellation-badge">
                        ❌ RÉSERVATION ANNULÉE
                    </div>
                    
                    <h2>Bonjour {$clientName},</h2>
                    
                    <p>Votre réservation a été <strong>annulée</strong>.</p>
                    
                    <div class="details">
                        <h3>📅 Détails de la réservation annulée :</h3>
                        <p><strong>Numéro de réservation :</strong> #{$reservationId}</p>
                        <p><strong>Date :</strong> {$date}</p>
                        <p><strong>Heure :</strong> {$time}</p>
                    </div>
                    
                    <p>Nous regrettons de ne pas pouvoir vous accueillir cette fois-ci.</p>
                    
                    <p>Nous espérons vous voir bientôt au <strong>Le Cap Ristobar</strong> pour une prochaine occasion !</p>
                    
                    <p>Pour toute question ou nouvelle réservation, n'hésitez pas à nous contacter :</p>
                    
                    <div class="footer">
                        <p>À bientôt peut-être !<br>
                        L'équipe du Cap Ristobar</p>
                        <p>📞 05 49 53 28 76 | ✉️ lecapristobar86@gmail.com</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function getManagerConfirmationTemplate(string $clientName, array $reservationDetails): string
    {
        $date = $reservationDetails['date']->format('d/m/Y');
        $time = $reservationDetails['time']->format('H:i');
        $people = $reservationDetails['people'];
        $reservationId = $reservationDetails['id'];

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #10b981; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8fafc; padding: 30px; border-radius: 0 0 10px 10px; }
                .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981; }
                .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📋 NOUVELLE RÉSERVATION CONFIRMÉE</h1>
                    <p>Le Cap Ristobar - Notification Gérant</p>
                </div>
                <div class="content">
                    <h2>Une réservation vient d'être confirmée</h2>
                    
                    <div class="details">
                        <h3>🧾 Détails de la réservation :</h3>
                        <p><strong>Numéro :</strong> #{$reservationId}</p>
                        <p><strong>Client :</strong> {$clientName}</p>
                        <p><strong>Date :</strong> {$date}</p>
                        <p><strong>Heure :</strong> {$time}</p>
                        <p><strong>Nombre de personnes :</strong> {$people}</p>
                        <p><strong>Statut :</strong> <span style="color: #10b981; font-weight: bold;">✅ Confirmée</span></p>
                    </div>
                    
                    <p><strong>Action requise :</strong> Préparer la table et noter l'équipe de service.</p>
                    
                    <div class="footer">
                        <p>Notification automatique - Système de Réservation<br>
                        Le Cap Ristobar</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function getManagerCancellationTemplate(string $clientName, array $reservationDetails): string
    {
        $date = $reservationDetails['date']->format('d/m/Y');
        $time = $reservationDetails['time']->format('H:i');
        $people = $reservationDetails['people'];
        $reservationId = $reservationDetails['id'];

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #ef4444; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8fafc; padding: 30px; border-radius: 0 0 10px 10px; }
                .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ef4444; }
                .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>❌ RÉSERVATION ANNULÉE</h1>
                    <p>Le Cap Ristobar - Notification Gérant</p>
                </div>
                <div class="content">
                    <h2>Une réservation vient d'être annulée</h2>
                    
                    <div class="details">
                        <h3>🧾 Détails de la réservation annulée :</h3>
                        <p><strong>Numéro :</strong> #{$reservationId}</p>
                        <p><strong>Client :</strong> {$clientName}</p>
                        <p><strong>Date :</strong> {$date}</p>
                        <p><strong>Heure :</strong> {$time}</p>
                        <p><strong>Nombre de personnes :</strong> {$people}</p>
                        <p><strong>Statut :</strong> <span style="color: #ef4444; font-weight: bold;">❌ Annulée</span></p>
                    </div>
                    
                    <p><strong>Action requise :</strong> Libérer la table et informer l'équipe de service.</p>
                    
                    <div class="footer">
                        <p>Notification automatique - Système de Réservation<br>
                        Le Cap Ristobar</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}