<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * DebugConfigCommand
 * 
 * Cette commande console permet de :
 * 1. Afficher les paramètres de configuration actuels de l'application (environnement, mode debug, email admin, etc.).
 * 2. Tester le fonctionnement du mailer en envoyant un email de test à l'adresse de l'administrateur.
 * 
 * Usage :
 *   php bin/console app:debug-config
 * 
 * Cette commande est utile pour vérifier rapidement que l'environnement et le système de messagerie sont correctement configurés.
 */
#[AsCommand(
    name: 'app:debug-config',
    description: 'Debug current configuration'
)]
class DebugConfigCommand extends Command
{
    public function __construct(
        private ParameterBagInterface $params,
        private MailerInterface $mailer
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('=== DEBUG CONFIGURATION ===');
        
        // Récupérer l'email admin depuis les paramètres
        $adminEmail = $this->params->get('admin_email') ?? $_ENV['ADMIN_EMAIL'] ?? 'lecapristobar86@gmail.com';
        
        // Variables d'environnement
        $output->writeln('APP_ENV: ' . $this->params->get('kernel.environment'));
        $output->writeln('APP_DEBUG: ' . ($this->params->get('kernel.debug') ? 'true' : 'false'));
        $output->writeln('ADMIN_EMAIL: ' . $adminEmail);
        
        // Test mailer
        $output->writeln('');
        $output->writeln('Testing mailer...');
        
        try {
            $email = (new Email())
                ->from($adminEmail)
                ->to($adminEmail)
                ->subject('Test config - ' . date('Y-m-d H:i:s'))
                ->text('Test email from debug command.');

            $this->mailer->send($email);
            $output->writeln('✅ <info>Mailer works!</info>');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $output->writeln('❌ <error>Mailer error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}