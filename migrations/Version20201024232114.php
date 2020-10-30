<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201024232114 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chatroom_message DROP FOREIGN KEY FK_3B50C9F998FF31AE');
        $this->addSql('ALTER TABLE chatroom_message ADD CONSTRAINT FK_3B50C9F998FF31AE FOREIGN KEY (chatroom_people_id) REFERENCES chatroom_list (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chatroom_message DROP FOREIGN KEY FK_3B50C9F998FF31AE');
        $this->addSql('ALTER TABLE chatroom_message ADD CONSTRAINT FK_3B50C9F998FF31AE FOREIGN KEY (chatroom_people_id) REFERENCES chatroom_list (id)');
    }
}
