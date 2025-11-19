<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ContactController
 * 
 * Ce contrôleur gère le formulaire de contact du site.
 * 
 * Fonctionnalités principales :
 * - Récupération des données du formulaire (nom, email, téléphone, objet, message)
 * - Validation basique des champs
 * - Envoi d'un email au gérant avec les informations du contact
 * - Envoi d'un email de confirmation au client
 * - Gestion des erreurs et notifications via Flash Messages
 * 
 * Accès :
 * - Disponible publiquement via la route "/contact"
 */
class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $nom = $request->request->get('nom');
            $email = $request->request->get('email');
            $telephone = $request->request->get('telephone');
            $objet = $request->request->get('objet');
            $message = $request->request->get('message');

            // Valider les données (validation basique)
            if (empty($nom) || empty($email) || empty($telephone) || empty($objet) || empty($message)) {
                $this->addFlash('error', 'Tous les champs sont obligatoires.');
                return $this->redirectToRoute('app_home');
            }

            try {
                // Créer l'email pour le gérant
                $emailMessage = (new Email())
                    ->from($email)
                    ->to('lecapristobar86@gmail.com') 
                    ->replyTo($email)
                    ->subject("📧 Nouveau message - {$objet} - Le Cap Ristobar")
                    ->html($this->createEmailTemplate($nom, $email, $telephone, $objet, $message));

                $mailer->send($emailMessage);

                // Email de confirmation au client
                $confirmationEmail = (new Email())
                    ->from('lecapristobar86@gmail.com')
                    ->to($email)
                    ->subject('✅ Confirmation de votre message - Le Cap Ristobar')
                    ->html($this->createConfirmationTemplate($nom, $objet));

                $mailer->send($confirmationEmail);

                $this->addFlash('success', 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer.');
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute('app_home');
    }

    private function createEmailTemplate(string $nom, string $email, string $telephone, string $objet, string $message): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0e2c35; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8fafc; padding: 30px; border-radius: 0 0 10px 10px; }
                .details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📧 NOUVEAU MESSAGE - LE CAP RISTOBAR</h1>
                    <p>Formulaire de contact</p>
                </div>
                <div class='content'>
                    <h2>Vous avez reçu un nouveau message</h2>
                    
                    <div class='details'>
                        <h3>👤 Informations du contact :</h3>
                        <p><strong>Nom :</strong> {$nom}</p>
                        <p><strong>Email :</strong> {$email}</p>
                        <p><strong>Téléphone :</strong> {$telephone}</p>
                        <p><strong>Objet :</strong> {$objet}</p>
                    </div>
                    
                    <div class='details'>
                        <h3>📝 Message :</h3>
                        <p>{$message}</p>
                    </div>
                    
                    <p><strong>⚠️ Action requise :</strong> Répondre à ce message dans les 24h.</p>
                    
                    <div class='footer'>
                        <p>Notification automatique - Système de Contact<br>
                        Le Cap Ristobar</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function createConfirmationTemplate(string $nom, string $objet): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0e2c35; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8fafc; padding: 30px; border-radius: 0 0 10px 10px; }
                .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Le Cap Ristobar</h1>
                    <p>Bar • Restaurant • Pizzeria • Événements</p>
                </div>
                <div class='content'>
                    <h2>Bonjour {$nom},</h2>
                    
                    <p>Nous avons bien reçu votre message concernant <strong>{$objet}</strong>.</p>
                    
                    <p>Nous vous remercions pour votre intérêt et nous vous répondrons dans les plus brefs délais.</p>
                    
                    <p><strong>Notre équipe s'engage à vous répondre sous 24 heures.</strong></p>
                    
                    <p>En attendant, n'hésitez pas à nous suivre sur nos réseaux sociaux pour découvrir nos actualités.</p>
                    
                    <div class='footer'>
                        <p>À très bientôt !<br>
                        L'équipe du Cap Ristobar</p>
                        <p>📞 05 49 53 28 76| ✉️ lecapristobar86@gmail.com</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
