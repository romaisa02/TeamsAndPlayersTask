<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507140136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE players ADD team_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE players ADD CONSTRAINT FK_264E43A6B842D717 FOREIGN KEY (team_id_id) REFERENCES teams (id)');
        $this->addSql('CREATE INDEX IDX_264E43A6B842D717 ON players (team_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE players DROP FOREIGN KEY FK_264E43A6B842D717');
        $this->addSql('DROP INDEX IDX_264E43A6B842D717 ON players');
        $this->addSql('ALTER TABLE players DROP team_id_id');
    }
}
