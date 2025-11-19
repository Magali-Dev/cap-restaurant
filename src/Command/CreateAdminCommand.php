<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * CreateAdminCommand
 * 
 * Cette commande console permet de créer un utilisateur administrateur
 * pour l'application Le Cap Ristobar. 
 * Elle demande l'email, le nom, le prénom et le mot de passe de l'utilisateur.
 * Si l'utilisateur existe déjà, elle propose de le promouvoir en administrateur.
 * 
 * Usage :
 *   php bin/console app:create-admin
 */

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un utilisateur administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Création d\'un administrateur pour Le Cap Ristobar');

        $helper = $this->getHelper('question');

        // Email
        $emailQuestion = new Question('Entrez l\'email de l\'administrateur : ');
        $emailQuestion->setValidator(function ($value) {
            if (empty($value)) {
                throw new \Exception('L\'email ne peut pas être vide');
            }
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email invalide');
            }
            return $value;
        });
        $email = $helper->ask($input, $output, $emailQuestion);

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->warning('Un utilisateur avec cet email existe déjà.');
            
            if (!$io->confirm('Voulez-vous le promouvoir administrateur ?', false)) {
                return Command::SUCCESS;
            }
            
            $existingUser->setRoles(['ROLE_ADMIN']);
            $this->entityManager->flush();
            $io->success('Utilisateur promu administrateur avec succès !');
            return Command::SUCCESS;
        }

        // Nom
        $nomQuestion = new Question('Entrez le nom : ');
        $nomQuestion->setValidator(function ($value) {
            if (empty($value)) {
                throw new \Exception('Le nom ne peut pas être vide');
            }
            return $value;
        });
        $nom = $helper->ask($input, $output, $nomQuestion);

        // Prénom
        $prenomQuestion = new Question('Entrez le prénom : ');
        $prenom = $helper->ask($input, $output, $prenomQuestion);

        // Mot de passe
        $passwordQuestion = new Question('Entrez le mot de passe : ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setValidator(function ($value) {
            if (empty($value)) {
                throw new \Exception('Le mot de passe ne peut pas être vide');
            }
            if (strlen($value) < 6) {
                throw new \Exception('Le mot de passe doit contenir au moins 6 caractères');
            }
            return $value;
        });
        $password = $helper->ask($input, $output, $passwordQuestion);

        // Créer l'utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setRoles(['ROLE_ADMIN']);

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setMotDePasse($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Administrateur créé avec succès !');
        $io->note('Email: ' . $email);
        $io->note('Vous pouvez maintenant vous connecter à l\'administration.');

        return Command::SUCCESS;
    }
}