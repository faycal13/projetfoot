<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200923214145 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, last_login DATETIME DEFAULT NULL, phone VARCHAR(255) NOT NULL, sms_code INT DEFAULT NULL, email_code INT DEFAULT NULL, aRoles TEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, zip_code VARCHAR(5) NOT NULL, coordinates VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE footballer (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, description LONGTEXT DEFAULT NULL, profil_photo VARCHAR(255) DEFAULT NULL, goal LONGTEXT DEFAULT NULL, cover_photo VARCHAR(255) DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, height INT DEFAULT NULL, position VARCHAR(255) DEFAULT NULL, better_foot VARCHAR(20) DEFAULT NULL, UNIQUE INDEX UNIQ_DA9711BAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE footballer_photo (id INT AUTO_INCREMENT NOT NULL, footballer_id INT NOT NULL, creation_date DATETIME NOT NULL, internal_link VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_D8D78F5E933A9BDB (footballer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE footballer_video (id INT AUTO_INCREMENT NOT NULL, footballer_id INT NOT NULL, creation_date DATETIME NOT NULL, external_link VARCHAR(255) DEFAULT NULL, internal_link VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_B0A7D16A933A9BDB (footballer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, city_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, date_of_birth DATE NOT NULL, last_modify DATETIME NOT NULL, sexe VARCHAR(1) DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D6499B6B5FBA (account_id), INDEX IDX_8D93D6498BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE footballer ADD CONSTRAINT FK_DA9711BAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE footballer_photo ADD CONSTRAINT FK_D8D78F5E933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE footballer_video ADD CONSTRAINT FK_B0A7D16A933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499B6B5FBA');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498BAC62AF');
        $this->addSql('ALTER TABLE footballer_photo DROP FOREIGN KEY FK_D8D78F5E933A9BDB');
        $this->addSql('ALTER TABLE footballer_video DROP FOREIGN KEY FK_B0A7D16A933A9BDB');
        $this->addSql('ALTER TABLE footballer DROP FOREIGN KEY FK_DA9711BAA76ED395');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE footballer');
        $this->addSql('DROP TABLE footballer_photo');
        $this->addSql('DROP TABLE footballer_video');
        $this->addSql('DROP TABLE user');
    }
}
