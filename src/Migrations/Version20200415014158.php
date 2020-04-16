<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200415014158 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bds (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, pitch VARCHAR(255) NOT NULL, date DATE NOT NULL, author VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE star ADD bds_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE star ADD CONSTRAINT FK_C9DB5A1478C6CD2F FOREIGN KEY (bds_id) REFERENCES bds (id)');
        $this->addSql('CREATE INDEX IDX_C9DB5A1478C6CD2F ON star (bds_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE star DROP FOREIGN KEY FK_C9DB5A1478C6CD2F');
        $this->addSql('DROP TABLE bds');
        $this->addSql('DROP INDEX IDX_C9DB5A1478C6CD2F ON star');
        $this->addSql('ALTER TABLE star DROP bds_id');
    }
}
