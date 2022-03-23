<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519221838 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL, CHANGE prix prix DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE programmes DROP FOREIGN KEY FK_3631FC3F97C86FA4');
        $this->addSql('ALTER TABLE programmes ADD CONSTRAINT FK_3631FC3F97C86FA4 FOREIGN KEY (transporteur_id) REFERENCES transporteur (id)');
        $this->addSql('ALTER TABLE proposition CHANGE email email VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE utilisateur_id utilisateur_id INT NOT NULL, CHANGE prix prix DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE programmes DROP FOREIGN KEY FK_3631FC3F97C86FA4');
        $this->addSql('ALTER TABLE programmes ADD CONSTRAINT FK_3631FC3F97C86FA4 FOREIGN KEY (transporteur_id) REFERENCES transporteur (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE proposition CHANGE email email VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
