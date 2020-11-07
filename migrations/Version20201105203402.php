<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201105203402 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9B9D1C3019');
        $this->addSql('DROP INDEX IDX_4744FC9B9D1C3019 ON private_message');
        $this->addSql('ALTER TABLE private_message DROP participant_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_message ADD participant_id INT NOT NULL');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9B9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant_conversation (id)');
        $this->addSql('CREATE INDEX IDX_4744FC9B9D1C3019 ON private_message (participant_id)');
    }
}
