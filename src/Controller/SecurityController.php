<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
/**
 * Ce contrôleur gère les fonctionnalités d'authentification et d'inscription des utilisateurs.
 * 
 * Il permet :
 *  - de gérer la connexion et la déconnexion des utilisateurs (via le système de sécurité Symfony) ;
 *  - d’inscrire de nouveaux comptes utilisateurs avec vérification d’unicité d’email et hachage sécurisé du mot de passe ;
 *  - de vérifier dynamiquement si un email est déjà enregistré (via une route AJAX).
 * 
 * Ce contrôleur repose sur :
 *  - l'entité User pour la persistance des comptes ;
 *  - le formulaire RegistrationFormType pour la gestion du formulaire d'inscription ;
 *  - le service UserPasswordHasherInterface pour le hachage des mots de passe ;
 *  - et le composant Security pour la gestion des sessions utilisateur.
 */
class SecurityController extends AbstractController
{
    #[Route('/auth', name: 'app_auth')]
    public function auth(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        Security $security
    ): Response
    {
        // Si l'utilisateur est déjà connecté
        if ($security->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);
        $registrationForm->handleRequest($request);

        // Traitement INSCRIPTION seulement (avec form_type)
        if ($registrationForm->isSubmitted() && $request->request->get('form_type') === 'register') {
            if ($registrationForm->isValid()) {
                try {
                    $plainPassword = $registrationForm->get('motDePasse')->getData();
                    
                    // Vérifier si l'email existe déjà
                    $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
                    if ($existingUser) {
                        $this->addFlash('danger', '❌ Cet email est déjà utilisé.');
                    } else {
                        // Hasher le mot de passe
                        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                        $user->setMotDePasse($hashedPassword);
                        $user->setRoles(['ROLE_USER']);

                        $em->persist($user);
                        $em->flush();

                        $this->addFlash('success', '✅ Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');
                        return $this->redirectToRoute('app_auth');
                    }
                    
                } catch (\Exception $e) {
                    $this->addFlash('danger', '❌ Une erreur est survenue lors de la création du compte.');
                }
            } else {
                // Le formulaire n'est pas valide - les erreurs seront affichées automatiquement dans le template
                $this->addFlash('danger', '❌ Veuillez corriger les erreurs dans le formulaire.');
            }
        }

        // Gestion CONNEXION (automatique par Symfony)
        $loginError = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login_register.html.twig', [
            'registrationForm' => $registrationForm->createView(),
            'login_error' => $loginError,
            'last_username' => $lastUsername ?? '',
        ]);
    }

    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $email = $request->query->get('email');
        
        if (!$email) {
            return $this->json(['exists' => false]);
        }
        
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        
        return $this->json([
            'exists' => $user !== null
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank');
    }
}