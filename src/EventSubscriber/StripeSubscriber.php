<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
/**
 * EventSubscriber Stripe - Diffusion de la clé publique Stripe aux templates Twig
 * 
 * Ce subscriber écoute l'événement KernelEvents::CONTROLLER et injecte
 * automatiquement la clé publique Stripe dans toutes les variables globales Twig.
 * 
 * Permet d'utiliser 'stripe_public_key' dans n'importe quel template sans
 * avoir à la passer manuellement depuis chaque contrôleur.
 * 
 * Utilisé principalement pour le composant de paiement Stripe qui nécessite
 * la clé publique côté client pour initialiser les sessions de paiement.
 */
class StripeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private ParameterBagInterface $params
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        // Passe la clé Stripe à tous les templates
        $this->twig->addGlobal('stripe_public_key', $this->params->get('stripe_public_key'));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}