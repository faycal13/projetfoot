<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201024214059 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chatroom_message ADD chatroom_people_id INT NOT NULL');
        $this->addSql('ALTER TABLE chatroom_message ADD CONSTRAINT FK_3B50C9F998FF31AE FOREIGN KEY (chatroom_people_id) REFERENCES chatroom_list (id)');
        $this->addSql('CREATE INDEX IDX_3B50C9F998FF31AE ON chatroom_message (chatroom_people_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chatroom_message DROP FOREIGN KEY FK_3B50C9F998FF31AE');
        $this->addSql('DROP INDEX IDX_3B50C9F998FF31AE ON chatroom_message');
        $this->addSql('ALTER TABLE chatroom_message DROP chatroom_people_id');
    }
}
