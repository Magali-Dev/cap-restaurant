<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251028085331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la colonne message dans la table reservation';
    }

    public function up(Schema $schema): void
    {
        // Ajoute uniquement la colonne message
        $this->addSql('ALTER TABLE reservation ADD message LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Supprime uniquement la colonne message
        $this->addSql('ALTER TABLE reservation DROP message');
    }
}
