<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104104134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE supplement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplement_pizza (supplement_id INT NOT NULL, pizza_id INT NOT NULL, INDEX IDX_68023E617793FA21 (supplement_id), INDEX IDX_68023E61D41D1D42 (pizza_id), PRIMARY KEY(supplement_id, pizza_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE supplement_pizza ADD CONSTRAINT FK_68023E617793FA21 FOREIGN KEY (supplement_id) REFERENCES supplement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE supplement_pizza ADD CONSTRAINT FK_68023E61D41D1D42 FOREIGN KEY (pizza_id) REFERENCES pizza (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE supplement_pizza DROP FOREIGN KEY FK_68023E617793FA21');
        $this->addSql('ALTER TABLE supplement_pizza DROP FOREIGN KEY FK_68023E61D41D1D42');
        $this->addSql('DROP TABLE supplement');
        $this->addSql('DROP TABLE supplement_pizza');
    }
}
