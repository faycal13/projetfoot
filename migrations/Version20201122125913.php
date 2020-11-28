<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201122125913 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9BF624B39D');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9BF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9BF624B39D');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9BF624B39D FOREIGN KEY (sender_id) REFERENCES footballer (id)');
    }
}
