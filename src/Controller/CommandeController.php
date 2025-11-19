<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeItem;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Contrôleur de gestion des commandes et paiements Stripe
 * 
 * Ce contrôleur gère le processus complet de commande :
 * - Création de sessions de paiement Stripe
 * - Gestion du panier utilisateur
 * - Traitement des retours de paiement (succès/échec)
 * - Webhooks Stripe pour les confirmations de paiement
 * - Historique des commandes utilisateur
 * 
 * Sécurisé - nécessite une authentification utilisateur pour la plupart des actions
 * Intègre Stripe pour les paiements en ligne sécurisés
 */
class CommandeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private StripeClient $stripeClient
    ) {}

    /**
     * Crée une session de paiement Stripe pour la commande en cours
     * Reçoit le panier JSON, crée la commande en base et initialise le paiement Stripe
     */
    #[Route('/commande/create-session', name: 'commande_create_session', methods: ['POST'])]
    public function createStripeSession(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non connecté'], 401);
        }

        $panierJson = $request->request->get('panier');
        $panier = json_decode($panierJson, true) ?: [];

        if (empty($panier)) {
            return $this->json(['error' => 'Panier vide'], 400);
        }

        // Créer la commande en statut "En attente"
        $commande = new Commande();
        $commande->setUser($user)
                 ->setStatus('En attente')
                 ->setCreatedAt(new \DateTime());

        $total = 0;
        $lineItems = [];

        foreach ($panier as $itemData) {
            $item = new CommandeItem();
            $item->setNom($itemData['nom'])
                 ->setQty($itemData['qty'])
                 ->setPrix($itemData['prix'])
                 ->setCommande($commande);

            // Gestion des suppléments
            $supplements = $itemData['supplements'] ?? [];
            $totalSup = 0;

            if (!empty($supplements)) {
                $item->setSupplements(json_encode($supplements));
                foreach ($supplements as $sup) {
                    $totalSup += $sup['prix'] * $sup['qty'];
                }
            }

            $this->em->persist($item);
            $commande->addItem($item);

            $itemTotal = ($itemData['prix'] * $itemData['qty']) + $totalSup;
            $total += $itemTotal;

            // Préparer les line items pour Stripe
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $itemData['nom'],
                        'description' => $this->generateSupplementDescription($supplements),
                    ],
                    'unit_amount' => (int) (($itemTotal / $itemData['qty']) * 100), // Prix unitaire en cents
                ],
                'quantity' => $itemData['qty'],
            ];
        }

        $commande->setTotal($total);
        $this->em->persist($commande);
        $this->em->flush();

        // Créer la session Stripe
        try {
            $session = $this->stripeClient->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $this->generateUrl('commande_success', [
                    'commandeId' => $commande->getId(),
                    'session_id' => '{CHECKOUT_SESSION_ID}'
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('commande_cancel', [
                    'commandeId' => $commande->getId()
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'customer_email' => $user->getEmail(),
                'metadata' => [
                    'commande_id' => $commande->getId(),
                    'user_id' => $user->getId()
                ],
            ]);

            return $this->json([
                'sessionId' => $session->id,
                'commandeId' => $commande->getId()
            ]);

        } catch (\Exception $e) {
            // En cas d'erreur Stripe, supprimer la commande
            $this->em->remove($commande);
            $this->em->flush();

            return $this->json(['error' => 'Erreur de paiement: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Page de succès après paiement
     * Vérifie le statut du paiement Stripe et met à jour la commande
     * Vide également le panier local de l'utilisateur
     */
    #[Route('/commande/success/{commandeId}', name: 'commande_success')]
    public function success(int $commandeId, Request $request): Response
    {
        $commande = $this->em->getRepository(Commande::class)->find($commandeId);
        
        if (!$commande || $commande->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        // Vérifier le paiement avec Stripe
        $sessionId = $request->query->get('session_id');
        
        try {
            $session = $this->stripeClient->checkout->sessions->retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                $commande->setStatus('Payée');
                $commande->setStripeSessionId($sessionId);
                $this->em->flush();

                // Vider le panier local
                if (isset($_SERVER['HTTP_COOKIE'])) {
                    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                    foreach($cookies as $cookie) {
                        $parts = explode('=', $cookie);
                        $name = trim($parts[0]);
                        if ($name === 'panier') {
                            setcookie($name, '', time()-1000);
                            setcookie($name, '', time()-1000, '/');
                        }
                    }
                }

                $this->addFlash('success', 'Paiement confirmé ! Votre commande est en préparation. 🍕');
            }
        } catch (\Exception $e) {
            $this->addFlash('warning', 'Paiement en cours de vérification...');
        }

        return $this->render('commande/success.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * Page d'annulation de paiement
     * Marque la commande comme annulée et redirige vers le panier
     */
    #[Route('/commande/cancel/{commandeId}', name: 'commande_cancel')]
    public function cancel(int $commandeId): Response
    {
        $commande = $this->em->getRepository(Commande::class)->find($commandeId);
        
        if ($commande && $commande->getUser() === $this->getUser()) {
            $commande->setStatus('Annulée');
            $this->em->flush();
        }

        $this->addFlash('warning', 'Paiement annulé. Vous pouvez réessayer quand vous voulez.');

        return $this->redirectToRoute('app_panier');
    }

    /**
     * Webhook Stripe pour les notifications de paiement
     * Version simplifiée pour développement, sécurisée pour la production
     * Reçoit les événements Stripe et met à jour le statut des commandes
     */
    #[Route('/webhook/stripe', name: 'stripe_webhook', methods: ['POST'])]
    public function handleStripeWebhook(Request $request): Response
    {
        // En développement, on accepte sans vérification
        if ($_ENV['APP_ENV'] === 'dev') {
            $payload = $request->getContent();
            $data = json_decode($payload, true);
            
            if (isset($data['type']) && $data['type'] === 'checkout.session.completed') {
                $session = $data['data']['object'];
                $commandeId = $session['metadata']['commande_id'] ?? null;
                
                if ($commandeId) {
                    $commande = $this->em->getRepository(Commande::class)->find($commandeId);
                    if ($commande) {
                        $commande->setStatus('Payée');
                        $commande->setStripeSessionId($session['id']);
                        $this->em->flush();
                        
                        error_log("Webhook DEV: Commande {$commandeId} marquée comme payée");
                    }
                }
            }
            
            return new Response('Webhook handled (dev mode)', 200);
        }
        
        // En production, vérification sécurisée
        $payload = $request->getContent();
        $sig_header = $request->headers->get('stripe-signature');
        $endpoint_secret = $this->getParameter('stripe_webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            
            $commande = $this->em->getRepository(Commande::class)->find(
                $session->metadata->commande_id
            );
            
            if ($commande) {
                $commande->setStatus('Payée');
                $commande->setStripeSessionId($session->id);
                $this->em->flush();
            }
        }

        return new Response('Webhook handled', 200);
    }

    /**
     * Génère une description des suppléments pour Stripe
     */
    private function generateSupplementDescription(array $supplements): string
    {
        if (empty($supplements)) {
            return 'Sans supplément';
        }

        return implode(', ', array_map(function($sup) {
            return $sup['nom'] . ' (x' . $sup['qty'] . ')';
        }, $supplements));
    }

    /**
     * Affiche le panier de l'utilisateur
     * Redirige vers l'authentification si l'utilisateur n'est pas connecté
     */
    #[Route('/panier', name: 'app_panier')]
    public function panier(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_auth');
        }

        return $this->render('commande/panier.html.twig', [
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }

    /**
     * Affiche l'historique des commandes de l'utilisateur
     * Les commandes sont triées par date de création décroissante
     * Décode les suppléments pour l'affichage
     */
    #[Route('/mes-commandes', name: 'app_commandes')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_auth');
        }

        $commandes = $this->em->getRepository(Commande::class)
                         ->findBy(['user' => $user], ['createdAt' => 'DESC']);

        // Décoder les suppléments pour chaque item
        foreach ($commandes as $commande) {
            foreach ($commande->getItems() as $item) {
                $item->decodedSupplements = $item->getSupplements() 
                    ? json_decode($item->getSupplements(), true) 
                    : [];
            }
        }

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}