<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201013224012 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE footballer_carrer DROP FOREIGN KEY FK_34415DDFA73F0036');
        $this->addSql('DROP INDEX idx_34415ddfa73f0036 ON footballer_carrer');
        $this->addSql('CREATE INDEX IDX_34415DDF8BAC62AF ON footballer_carrer (city_id)');
        $this->addSql('ALTER TABLE footballer_carrer ADD CONSTRAINT FK_34415DDFA73F0036 FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE friends_list ADD accept TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE footballer_carrer DROP FOREIGN KEY FK_34415DDF8BAC62AF');
        $this->addSql('DROP INDEX idx_34415ddf8bac62af ON footballer_carrer');
        $this->addSql('CREATE INDEX IDX_34415DDFA73F0036 ON footballer_carrer (city_id)');
        $this->addSql('ALTER TABLE footballer_carrer ADD CONSTRAINT FK_34415DDF8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE friends_list DROP accept');
    }
}
