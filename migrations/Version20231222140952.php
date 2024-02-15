<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231222140952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card_six_qp (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, value INT, points INT NOT NULL, UNIQUE INDEX UNIQ_752340F1D3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chosen_card_six_qp (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, player_id INT NOT NULL, game_id INT NOT NULL, card_id INT NOT NULL, state TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_F3099B4DD3F165E7 (help_id), UNIQUE INDEX UNIQ_F3099B4D99E6F5DF (player_id), INDEX IDX_F3099B4DE48FD905 (game_id), INDEX IDX_F3099B4D4ACC9A20 (card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discard_six_qp (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, player_id INT NOT NULL, game_id INT NOT NULL, total_points INT NOT NULL, UNIQUE INDEX UNIQ_80DC0B5AD3F165E7 (help_id), UNIQUE INDEX UNIQ_80DC0B5A99E6F5DF (player_id), INDEX IDX_80DC0B5AE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discard_six_qp_card_six_qp (discard_six_qp_id INT NOT NULL, card_six_qp_id INT NOT NULL, INDEX IDX_FD8BA8AE6BFB29E2 (discard_six_qp_id), INDEX IDX_FD8BA8AE8059E2D5 (card_six_qp_id), PRIMARY KEY(discard_six_qp_id, card_six_qp_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_six_qp (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_7A29BE4DD3F165E7 (help_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help (id INT AUTO_INCREMENT NOT NULL, game_name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_six_qp (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, username VARCHAR(255) NOT NULL, INDEX IDX_981C855BE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_six_qp_card_six_qp (player_six_qp_id INT NOT NULL, card_six_qp_id INT NOT NULL, INDEX IDX_2C1E1490545D34B8 (player_six_qp_id), INDEX IDX_2C1E14908059E2D5 (card_six_qp_id), PRIMARY KEY(player_six_qp_id, card_six_qp_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE row_six_qp (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, game_id INT NOT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_573AC057D3F165E7 (help_id), INDEX IDX_573AC057E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE row_six_qp_card_six_qp (row_six_qp_id INT NOT NULL, card_six_qp_id INT NOT NULL, INDEX IDX_AB3653B2244F3AE (row_six_qp_id), INDEX IDX_AB3653B28059E2D5 (card_six_qp_id), PRIMARY KEY(row_six_qp_id, card_six_qp_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE card_six_qp ADD CONSTRAINT FK_752340F1D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE chosen_card_six_qp ADD CONSTRAINT FK_F3099B4DD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE chosen_card_six_qp ADD CONSTRAINT FK_F3099B4D99E6F5DF FOREIGN KEY (player_id) REFERENCES player_six_qp (id)');
        $this->addSql('ALTER TABLE chosen_card_six_qp ADD CONSTRAINT FK_F3099B4DE48FD905 FOREIGN KEY (game_id) REFERENCES game_six_qp (id)');
        $this->addSql('ALTER TABLE chosen_card_six_qp ADD CONSTRAINT FK_F3099B4D4ACC9A20 FOREIGN KEY (card_id) REFERENCES card_six_qp (id)');
        $this->addSql('ALTER TABLE discard_six_qp ADD CONSTRAINT FK_80DC0B5AD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE discard_six_qp ADD CONSTRAINT FK_80DC0B5A99E6F5DF FOREIGN KEY (player_id) REFERENCES player_six_qp (id)');
        $this->addSql('ALTER TABLE discard_six_qp ADD CONSTRAINT FK_80DC0B5AE48FD905 FOREIGN KEY (game_id) REFERENCES game_six_qp (id)');
        $this->addSql('ALTER TABLE discard_six_qp_card_six_qp ADD CONSTRAINT FK_FD8BA8AE6BFB29E2 FOREIGN KEY (discard_six_qp_id) REFERENCES discard_six_qp (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discard_six_qp_card_six_qp ADD CONSTRAINT FK_FD8BA8AE8059E2D5 FOREIGN KEY (card_six_qp_id) REFERENCES card_six_qp (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_six_qp ADD CONSTRAINT FK_7A29BE4DD3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE player_six_qp ADD CONSTRAINT FK_981C855BE48FD905 FOREIGN KEY (game_id) REFERENCES game_six_qp (id)');
        $this->addSql('ALTER TABLE player_six_qp_card_six_qp ADD CONSTRAINT FK_2C1E1490545D34B8 FOREIGN KEY (player_six_qp_id) REFERENCES player_six_qp (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_six_qp_card_six_qp ADD CONSTRAINT FK_2C1E14908059E2D5 FOREIGN KEY (card_six_qp_id) REFERENCES card_six_qp (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE row_six_qp ADD CONSTRAINT FK_573AC057D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE row_six_qp ADD CONSTRAINT FK_573AC057E48FD905 FOREIGN KEY (game_id) REFERENCES game_six_qp (id)');
        $this->addSql('ALTER TABLE row_six_qp_card_six_qp ADD CONSTRAINT FK_AB3653B2244F3AE FOREIGN KEY (row_six_qp_id) REFERENCES row_six_qp (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE row_six_qp_card_six_qp ADD CONSTRAINT FK_AB3653B28059E2D5 FOREIGN KEY (card_six_qp_id) REFERENCES card_six_qp (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_six_qp DROP FOREIGN KEY FK_752340F1D3F165E7');
        $this->addSql('ALTER TABLE chosen_card_six_qp DROP FOREIGN KEY FK_F3099B4DD3F165E7');
        $this->addSql('ALTER TABLE chosen_card_six_qp DROP FOREIGN KEY FK_F3099B4D99E6F5DF');
        $this->addSql('ALTER TABLE chosen_card_six_qp DROP FOREIGN KEY FK_F3099B4DE48FD905');
        $this->addSql('ALTER TABLE chosen_card_six_qp DROP FOREIGN KEY FK_F3099B4D4ACC9A20');
        $this->addSql('ALTER TABLE discard_six_qp DROP FOREIGN KEY FK_80DC0B5AD3F165E7');
        $this->addSql('ALTER TABLE discard_six_qp DROP FOREIGN KEY FK_80DC0B5A99E6F5DF');
        $this->addSql('ALTER TABLE discard_six_qp DROP FOREIGN KEY FK_80DC0B5AE48FD905');
        $this->addSql('ALTER TABLE discard_six_qp_card_six_qp DROP FOREIGN KEY FK_FD8BA8AE6BFB29E2');
        $this->addSql('ALTER TABLE discard_six_qp_card_six_qp DROP FOREIGN KEY FK_FD8BA8AE8059E2D5');
        $this->addSql('ALTER TABLE game_six_qp DROP FOREIGN KEY FK_7A29BE4DD3F165E7');
        $this->addSql('ALTER TABLE player_six_qp DROP FOREIGN KEY FK_981C855BE48FD905');
        $this->addSql('ALTER TABLE player_six_qp_card_six_qp DROP FOREIGN KEY FK_2C1E1490545D34B8');
        $this->addSql('ALTER TABLE player_six_qp_card_six_qp DROP FOREIGN KEY FK_2C1E14908059E2D5');
        $this->addSql('ALTER TABLE row_six_qp DROP FOREIGN KEY FK_573AC057D3F165E7');
        $this->addSql('ALTER TABLE row_six_qp DROP FOREIGN KEY FK_573AC057E48FD905');
        $this->addSql('ALTER TABLE row_six_qp_card_six_qp DROP FOREIGN KEY FK_AB3653B2244F3AE');
        $this->addSql('ALTER TABLE row_six_qp_card_six_qp DROP FOREIGN KEY FK_AB3653B28059E2D5');
        $this->addSql('DROP TABLE card_six_qp');
        $this->addSql('DROP TABLE chosen_card_six_qp');
        $this->addSql('DROP TABLE discard_six_qp');
        $this->addSql('DROP TABLE discard_six_qp_card_six_qp');
        $this->addSql('DROP TABLE game_six_qp');
        $this->addSql('DROP TABLE help');
        $this->addSql('DROP TABLE player_six_qp');
        $this->addSql('DROP TABLE player_six_qp_card_six_qp');
        $this->addSql('DROP TABLE row_six_qp');
        $this->addSql('DROP TABLE row_six_qp_card_six_qp');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
