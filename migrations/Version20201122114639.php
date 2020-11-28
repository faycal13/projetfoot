<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201122114639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant_conversation DROP FOREIGN KEY FK_17A662BB933A9BDB');
        $this->addSql('DROP INDEX IDX_17A662BB933A9BDB ON participant_conversation');
        $this->addSql('ALTER TABLE participant_conversation CHANGE footballer_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE participant_conversation ADD CONSTRAINT FK_17A662BBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_17A662BBA76ED395 ON participant_conversation (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant_conversation DROP FOREIGN KEY FK_17A662BBA76ED395');
        $this->addSql('DROP INDEX IDX_17A662BBA76ED395 ON participant_conversation');
        $this->addSql('ALTER TABLE participant_conversation CHANGE user_id footballer_id INT NOT NULL');
        $this->addSql('ALTER TABLE participant_conversation ADD CONSTRAINT FK_17A662BB933A9BDB FOREIGN KEY (footballer_id) REFERENCES footballer (id)');
        $this->addSql('CREATE INDEX IDX_17A662BB933A9BDB ON participant_conversation (footballer_id)');
    }
}
