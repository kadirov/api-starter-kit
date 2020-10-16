<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201016055302 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add (App) entity. Uncomment if you want to use microservices';
    }

    public function up(Schema $schema) : void
    {
//        $this->addSql('CREATE TABLE app (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
//        $this->addSql('ALTER TABLE user ADD app_id INT NOT NULL');
//        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497987212D FOREIGN KEY (app_id) REFERENCES app (id)');
//        $this->addSql('CREATE INDEX IDX_8D93D6497987212D ON user (app_id)');
    }

    public function down(Schema $schema) : void
    {
//        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497987212D');
//        $this->addSql('DROP TABLE app');
//        $this->addSql('DROP INDEX IDX_8D93D6497987212D ON user');
//        $this->addSql('ALTER TABLE user DROP app_id');
    }
}
