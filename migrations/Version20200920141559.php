<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200920141559 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE footballer_photo (id INT AUTO_INCREMENT NOT NULL, footballer_id INT NOT NULL, creation_date DATETIME NOT NULL, internal_link VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_D8D78F5E933A9BDB (footballer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE footballer_video (id INT AUTO_INCREMENT NOT NULL, footballer_id INT NOT NULL, creation_date DATETIME NOT NULL, external_link VARCHAR(255) DEFAULT NULL, internal_link VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_B0A7D16A933A9BDB (footballer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE footballer_photo ADD CONSTRAINT FK_D8D78F5E933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE footballer_video ADD CONSTRAINT FK_B0A7D16A933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE footballer_photo');
        $this->addSql('DROP TABLE footballer_video');
    }
}
