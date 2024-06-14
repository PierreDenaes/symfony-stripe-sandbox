<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612075132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP INDEX UNIQ_906517441BE1FB52, ADD INDEX IDX_906517441BE1FB52 (basket_id)');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744A76ED395');
        $this->addSql('DROP INDEX IDX_90651744A76ED395 ON invoice');
        $this->addSql('ALTER TABLE invoice ADD amount NUMERIC(10, 2) NOT NULL, DROP user_id, CHANGE stripe_session_id stripe_session_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP INDEX IDX_906517441BE1FB52, ADD UNIQUE INDEX UNIQ_906517441BE1FB52 (basket_id)');
        $this->addSql('ALTER TABLE invoice ADD user_id INT NOT NULL, DROP amount, CHANGE stripe_session_id stripe_session_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_90651744A76ED395 ON invoice (user_id)');
    }
}
