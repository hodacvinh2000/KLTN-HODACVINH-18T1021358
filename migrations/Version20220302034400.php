<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220302034400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE anh (id INT AUTO_INCREMENT NOT NULL, tkgame_id INT NOT NULL, link VARCHAR(255) DEFAULT NULL, INDEX IDX_E43D746BF243CE1 (tkgame_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taikhoangame (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, desciption VARCHAR(2000) DEFAULT NULL, INDEX IDX_A732799EE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE anh ADD CONSTRAINT FK_E43D746BF243CE1 FOREIGN KEY (tkgame_id) REFERENCES taikhoangame (id)');
        $this->addSql('ALTER TABLE taikhoangame ADD CONSTRAINT FK_A732799EE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anh DROP FOREIGN KEY FK_E43D746BF243CE1');
        $this->addSql('DROP TABLE anh');
        $this->addSql('DROP TABLE taikhoangame');
    }
}
