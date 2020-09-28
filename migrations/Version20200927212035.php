<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200927212035 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE block_friends_list (id INT AUTO_INCREMENT NOT NULL, footballer_id INT NOT NULL, target_id INT NOT NULL, creation_date DATETIME NOT NULL, INDEX IDX_D8C8D3E3933A9BDB (footballer_id), INDEX IDX_D8C8D3E3158E0B66 (target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chatroom_list (id INT AUTO_INCREMENT NOT NULL, footballer_id INT NOT NULL, statut VARCHAR(255) NOT NULL, creation_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_3D2E802C933A9BDB (footballer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chatroom_message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, message LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, INDEX IDX_3B50C9F9F624B39D (sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, footballer_list VARCHAR(255) NOT NULL, datetime DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE footballer_carrer (id INT AUTO_INCREMENT NOT NULL, ville_id INT NOT NULL, footballer_id INT NOT NULL, club VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, INDEX IDX_34415DDFA73F0036 (ville_id), INDEX IDX_34415DDF933A9BDB (footballer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE footballer_skills (id INT AUTO_INCREMENT NOT NULL, footballer_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_4A4292DE933A9BDB (footballer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE friends_list (id INT AUTO_INCREMENT NOT NULL, footballer_id INT NOT NULL, friend_id INT NOT NULL, creation_date DATETIME NOT NULL, INDEX IDX_C913D5EA933A9BDB (footballer_id), INDEX IDX_C913D5EA6A5458E8 (friend_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE private_message (id INT AUTO_INCREMENT NOT NULL, conversation_id INT NOT NULL, sender_id INT NOT NULL, message LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, INDEX IDX_4744FC9B9AC0396 (conversation_id), INDEX IDX_4744FC9BF624B39D (sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE block_friends_list ADD CONSTRAINT FK_D8C8D3E3933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE block_friends_list ADD CONSTRAINT FK_D8C8D3E3158E0B66 FOREIGN KEY (target_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE chatroom_list ADD CONSTRAINT FK_3D2E802C933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE chatroom_message ADD CONSTRAINT FK_3B50C9F9F624B39D FOREIGN KEY (sender_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE footballer_carrer ADD CONSTRAINT FK_34415DDFA73F0036 FOREIGN KEY (ville_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE footballer_carrer ADD CONSTRAINT FK_34415DDF933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE footballer_skills ADD CONSTRAINT FK_4A4292DE933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE friends_list ADD CONSTRAINT FK_C913D5EA933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE friends_list ADD CONSTRAINT FK_C913D5EA6A5458E8 FOREIGN KEY (friend_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9B9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9BF624B39D FOREIGN KEY (sender_id) REFERENCES footballer (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9B9AC0396');
        $this->addSql('DROP TABLE block_friends_list');
        $this->addSql('DROP TABLE chatroom_list');
        $this->addSql('DROP TABLE chatroom_message');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE footballer_carrer');
        $this->addSql('DROP TABLE footballer_skills');
        $this->addSql('DROP TABLE friends_list');
        $this->addSql('DROP TABLE private_message');
    }
}
