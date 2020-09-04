<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200903130044 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add (user) createdAt, updatedAt and is deleted fields';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD is_deleted TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP created_at, DROP updated_at, DROP is_deleted');
    }
}
