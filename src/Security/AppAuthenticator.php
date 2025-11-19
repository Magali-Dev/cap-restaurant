<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Authenticator personnalisé - Gestion robuste de l'authentification des utilisateurs
 * 
 * Cet authenticator améliore la sécurité du processus de connexion avec :
 * - Validation robuste des données d'entrée (email, mot de passe, token CSRF)
 * - Protection contre l'énumération d'emails avec messages d'erreur identiques
 * - Vérification des redirections pour prévenir les attaques open redirect
 * - Nettoyage sécurisé des données de session après authentification
 * - Régénération d'ID de session pour prévenir la fixation de session
 * 
 * Implémente AuthenticationEntryPointInterface pour rediriger vers la page
 * de connexion quand un accès nécessite une authentification
 */
class AppAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    use TargetPathTrait;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST') && 
               $request->getPathInfo() === '/auth' &&
               $request->request->get('form_type') === 'login';
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username', '');
        $password = $request->request->get('_password', '');
        $csrfToken = $request->request->get('_csrf_token', '');

        // Nettoyage et validation robuste
        $email = is_string($email) ? trim($email) : '';
        $password = is_string($password) ? $password : '';
        $csrfToken = is_string($csrfToken) ? $csrfToken : '';

        // Validation stricte des champs requis
        if (empty($email)) {
            throw new CustomUserMessageAuthenticationException('L\'email est requis.');
        }

        if (empty($password)) {
            throw new CustomUserMessageAuthenticationException('Le mot de passe est requis.');
        }

        if (empty($csrfToken)) {
            throw new CustomUserMessageAuthenticationException('Token de sécurité manquant.');
        }

        // Validation du format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new CustomUserMessageAuthenticationException('Format d\'email invalide.');
        }

        // Protection contre les emails trop longs
        if (strlen($email) > 180) {
            throw new CustomUserMessageAuthenticationException('Email trop long.');
        }

        return new Passport(
            new UserBadge($email, function($userIdentifier) {
                // Recherche sécurisée de l'utilisateur
                $user = $this->entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $userIdentifier]);
                
                if (!$user) {
                    // Message identique pour éviter l'énumération d'emails
                    throw new CustomUserMessageAuthenticationException('Email ou mot de passe incorrect.');
                }

                return $user;
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Régénération de session pour prévenir le fixation
        $request->getSession()->migrate(true);

        // Nettoyage des données de login
        $request->getSession()->remove('_security.last_error');
        $request->getSession()->remove('_security.last_username');

        // Redirection vers la page cible ou accueil
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);
        if ($targetPath && $this->isSafeRedirect($targetPath)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Stockage sécurisé des erreurs
        $errorMessage = 'Email ou mot de passe incorrect.';
        
        if ($exception instanceof CustomUserMessageAuthenticationException) {
            $errorMessage = $exception->getMessage();
        }

        $request->getSession()->set('_security.last_error', $errorMessage);
        $request->getSession()->set('_security.last_username', 
            is_string($request->request->get('_username', '')) ? 
            $request->request->get('_username', '') : ''
        );

        return new RedirectResponse($this->urlGenerator->generate('app_auth'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_auth'));
    }

    /**
     * Vérifie que la redirection est vers un domaine autorisé
     */
    private function isSafeRedirect(string $targetPath): bool
    {
        $allowedHosts = [
            $this->urlGenerator->getContext()->getHost()
        ];
        
        $targetHost = parse_url($targetPath, PHP_URL_HOST);
        
        return !$targetHost || in_array($targetHost, $allowedHosts, true);
    }
}