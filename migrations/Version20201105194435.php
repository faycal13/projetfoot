<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201105194435 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participant_conversation (id INT AUTO_INCREMENT NOT NULL, footballer_id INT NOT NULL, conversation_id INT NOT NULL, INDEX IDX_17A662BB933A9BDB (footballer_id), INDEX IDX_17A662BB9AC0396 (conversation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participant_conversation ADD CONSTRAINT FK_17A662BB933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
        $this->addSql('ALTER TABLE participant_conversation ADD CONSTRAINT FK_17A662BB9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE private_message ADD participant_id INT NOT NULL');
        $this->addSql('ALTER TABLE private_message ADD CONSTRAINT FK_4744FC9B9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant_conversation (id)');
        $this->addSql('CREATE INDEX IDX_4744FC9B9D1C3019 ON private_message (participant_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_message DROP FOREIGN KEY FK_4744FC9B9D1C3019');
        $this->addSql('DROP TABLE participant_conversation');
        $this->addSql('DROP INDEX IDX_4744FC9B9D1C3019 ON private_message');
        $this->addSql('ALTER TABLE private_message DROP participant_id');
    }
}
