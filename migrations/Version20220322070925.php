<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220322070925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nhiemvu DROP FOREIGN KEY FK_239D744C6DB94FA4');
        $this->addSql('DROP TABLE banggia');
        $this->addSql('DROP INDEX IDX_239D744C6DB94FA4 ON nhiemvu');
        $this->addSql('ALTER TABLE nhiemvu DROP banggia_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE banggia (id INT AUTO_INCREMENT NOT NULL, gia INT NOT NULL, mota VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, songay INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE nhiemvu ADD banggia_id INT NOT NULL');
        $this->addSql('ALTER TABLE nhiemvu ADD CONSTRAINT FK_239D744C6DB94FA4 FOREIGN KEY (banggia_id) REFERENCES banggia (id)');
        $this->addSql('CREATE INDEX IDX_239D744C6DB94FA4 ON nhiemvu (banggia_id)');
    }
}
