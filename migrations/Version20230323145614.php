<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230323145614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE production_time (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, project_id INT NOT NULL, production_time INT NOT NULL, INDEX IDX_B484D3518C03F15C (employee_id), INDEX IDX_B484D351166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE production_time ADD CONSTRAINT FK_B484D3518C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE production_time ADD CONSTRAINT FK_B484D351166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production_time DROP FOREIGN KEY FK_B484D3518C03F15C');
        $this->addSql('ALTER TABLE production_time DROP FOREIGN KEY FK_B484D351166D1F9C');
        $this->addSql('DROP TABLE production_time');
    }
}
