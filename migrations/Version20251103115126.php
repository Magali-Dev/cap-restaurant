<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251103115126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD date_evenement DATETIME DEFAULT NULL, DROP date');
        $this->addSql('ALTER TABLE menu DROP prix, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE nom titre VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD date DATETIME NOT NULL, DROP date_evenement');
        $this->addSql('ALTER TABLE menu ADD prix VARCHAR(255) DEFAULT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE titre nom VARCHAR(255) NOT NULL');
    }
}
