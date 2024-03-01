<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240301131634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_glm DROP FOREIGN KEY FK_1862B93869545666');
        $this->addSql('CREATE TABLE tile_activation_bonus_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, resource_id INT DEFAULT NULL, amount INT NOT NULL, UNIQUE INDEX UNIQ_19F727B3D3F165E7 (help_id), INDEX IDX_19F727B389329D25 (resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_activation_cost_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, resource_id INT DEFAULT NULL, price INT NOT NULL, UNIQUE INDEX UNIQ_8E96C745D3F165E7 (help_id), INDEX IDX_8E96C74589329D25 (resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_buy_bonus_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, resource_id INT DEFAULT NULL, amount INT NOT NULL, UNIQUE INDEX UNIQ_570C7C21D3F165E7 (help_id), INDEX IDX_570C7C2189329D25 (resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_buy_cost_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, resource_id INT DEFAULT NULL, price INT NOT NULL, UNIQUE INDEX UNIQ_714E01A7D3F165E7 (help_id), INDEX IDX_714E01A789329D25 (resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_glm_tile_buy_cost_glm (tile_glm_id INT NOT NULL, tile_buy_cost_glm_id INT NOT NULL, INDEX IDX_D62FAA13E6306C44 (tile_glm_id), INDEX IDX_D62FAA133209D372 (tile_buy_cost_glm_id), PRIMARY KEY(tile_glm_id, tile_buy_cost_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_glm_tile_buy_bonus_glm (tile_glm_id INT NOT NULL, tile_buy_bonus_glm_id INT NOT NULL, INDEX IDX_9BA76A1FE6306C44 (tile_glm_id), INDEX IDX_9BA76A1FD5F29361 (tile_buy_bonus_glm_id), PRIMARY KEY(tile_glm_id, tile_buy_bonus_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_glm_tile_activation_cost_glm (tile_glm_id INT NOT NULL, tile_activation_cost_glm_id INT NOT NULL, INDEX IDX_5D99329AE6306C44 (tile_glm_id), INDEX IDX_5D99329A8DC7FD6D (tile_activation_cost_glm_id), PRIMARY KEY(tile_glm_id, tile_activation_cost_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tile_glm_tile_activation_bonus_glm (tile_glm_id INT NOT NULL, tile_activation_bonus_glm_id INT NOT NULL, INDEX IDX_F48E703E6306C44 (tile_glm_id), INDEX IDX_F48E703584550BA (tile_activation_bonus_glm_id), PRIMARY KEY(tile_glm_id, tile_activation_bonus_glm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tile_activation_bonus_glm ADD CONSTRAINT FK_19F727B3D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_activation_bonus_glm ADD CONSTRAINT FK_19F727B389329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE tile_activation_cost_glm ADD CONSTRAINT FK_8E96C745D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_activation_cost_glm ADD CONSTRAINT FK_8E96C74589329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE tile_buy_bonus_glm ADD CONSTRAINT FK_570C7C21D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_buy_bonus_glm ADD CONSTRAINT FK_570C7C2189329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE tile_buy_cost_glm ADD CONSTRAINT FK_714E01A7D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_buy_cost_glm ADD CONSTRAINT FK_714E01A789329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE tile_glm_tile_buy_cost_glm ADD CONSTRAINT FK_D62FAA13E6306C44 FOREIGN KEY (tile_glm_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE tile_glm_tile_buy_cost_glm ADD CONSTRAINT FK_D62FAA133209D372 FOREIGN KEY (tile_buy_cost_glm_id) REFERENCES tile_buy_cost_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_glm_tile_buy_bonus_glm ADD CONSTRAINT FK_9BA76A1FE6306C44 FOREIGN KEY (tile_glm_id) REFERENCES tile_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_glm_tile_buy_bonus_glm ADD CONSTRAINT FK_9BA76A1FD5F29361 FOREIGN KEY (tile_buy_bonus_glm_id) REFERENCES tile_buy_bonus_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_glm_tile_activation_cost_glm ADD CONSTRAINT FK_5D99329AE6306C44 FOREIGN KEY (tile_glm_id) REFERENCES tile_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_glm_tile_activation_cost_glm ADD CONSTRAINT FK_5D99329A8DC7FD6D FOREIGN KEY (tile_activation_cost_glm_id) REFERENCES tile_activation_cost_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_glm_tile_activation_bonus_glm ADD CONSTRAINT FK_F48E703E6306C44 FOREIGN KEY (tile_glm_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE tile_glm_tile_activation_bonus_glm ADD CONSTRAINT FK_F48E703584550BA FOREIGN KEY (tile_activation_bonus_glm_id) REFERENCES tile_activation_bonus_glm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tile_bonus_glm DROP FOREIGN KEY FK_8E2D82E68744CF10');
        $this->addSql('ALTER TABLE tile_bonus_glm DROP FOREIGN KEY FK_8E2D82E6E6306C44');
        $this->addSql('ALTER TABLE tile_bonus_glm DROP FOREIGN KEY FK_8E2D82E689329D25');
        $this->addSql('ALTER TABLE tile_bonus_glm DROP FOREIGN KEY FK_8E2D82E6D3F165E7');
        $this->addSql('ALTER TABLE tile_cost_glm DROP FOREIGN KEY FK_86EA1A4E8744CF10');
        $this->addSql('ALTER TABLE tile_cost_glm DROP FOREIGN KEY FK_86EA1A4EE6306C44');
        $this->addSql('ALTER TABLE tile_cost_glm DROP FOREIGN KEY FK_86EA1A4E89329D25');
        $this->addSql('ALTER TABLE tile_cost_glm DROP FOREIGN KEY FK_86EA1A4ED3F165E7');
        $this->addSql('DROP TABLE tile_bonus_glm');
        $this->addSql('DROP TABLE tile_cost_glm');
        //$this->addSql('ALTER TABLE card_glm DROP FOREIGN KEY FK_1862B93869545666');
        $this->addSql('ALTER TABLE card_glm ADD CONSTRAINT FK_1862B93869545666 FOREIGN KEY (bonus_id) REFERENCES tile_buy_bonus_glm (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_glm DROP FOREIGN KEY FK_1862B93869545666');
        $this->addSql('CREATE TABLE tile_bonus_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, resource_id INT DEFAULT NULL, tile_glm_id INT DEFAULT NULL, tile_bonus_glm_id INT DEFAULT NULL, amount INT NOT NULL, INDEX IDX_8E2D82E6E6306C44 (tile_glm_id), INDEX IDX_8E2D82E68744CF10 (tile_bonus_glm_id), UNIQUE INDEX UNIQ_8E2D82E6D3F165E7 (help_id), INDEX IDX_8E2D82E689329D25 (resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tile_cost_glm (id INT AUTO_INCREMENT NOT NULL, help_id INT DEFAULT NULL, tile_glm_id INT NOT NULL, tile_bonus_glm_id INT DEFAULT NULL, resource_id INT DEFAULT NULL, price INT NOT NULL, INDEX IDX_86EA1A4E89329D25 (resource_id), UNIQUE INDEX UNIQ_86EA1A4ED3F165E7 (help_id), INDEX IDX_86EA1A4EE6306C44 (tile_glm_id), INDEX IDX_86EA1A4E8744CF10 (tile_bonus_glm_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tile_bonus_glm ADD CONSTRAINT FK_8E2D82E68744CF10 FOREIGN KEY (tile_bonus_glm_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE tile_bonus_glm ADD CONSTRAINT FK_8E2D82E6E6306C44 FOREIGN KEY (tile_glm_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE tile_bonus_glm ADD CONSTRAINT FK_8E2D82E689329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE tile_bonus_glm ADD CONSTRAINT FK_8E2D82E6D3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_cost_glm ADD CONSTRAINT FK_86EA1A4E8744CF10 FOREIGN KEY (tile_bonus_glm_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE tile_cost_glm ADD CONSTRAINT FK_86EA1A4EE6306C44 FOREIGN KEY (tile_glm_id) REFERENCES tile_glm (id)');
        $this->addSql('ALTER TABLE tile_cost_glm ADD CONSTRAINT FK_86EA1A4E89329D25 FOREIGN KEY (resource_id) REFERENCES resource_glm (id)');
        $this->addSql('ALTER TABLE tile_cost_glm ADD CONSTRAINT FK_86EA1A4ED3F165E7 FOREIGN KEY (help_id) REFERENCES help (id)');
        $this->addSql('ALTER TABLE tile_activation_bonus_glm DROP FOREIGN KEY FK_19F727B3D3F165E7');
        $this->addSql('ALTER TABLE tile_activation_bonus_glm DROP FOREIGN KEY FK_19F727B389329D25');
        $this->addSql('ALTER TABLE tile_activation_cost_glm DROP FOREIGN KEY FK_8E96C745D3F165E7');
        $this->addSql('ALTER TABLE tile_activation_cost_glm DROP FOREIGN KEY FK_8E96C74589329D25');
        $this->addSql('ALTER TABLE tile_buy_bonus_glm DROP FOREIGN KEY FK_570C7C21D3F165E7');
        $this->addSql('ALTER TABLE tile_buy_bonus_glm DROP FOREIGN KEY FK_570C7C2189329D25');
        $this->addSql('ALTER TABLE tile_buy_cost_glm DROP FOREIGN KEY FK_714E01A7D3F165E7');
        $this->addSql('ALTER TABLE tile_buy_cost_glm DROP FOREIGN KEY FK_714E01A789329D25');
        $this->addSql('ALTER TABLE tile_glm_tile_buy_cost_glm DROP FOREIGN KEY FK_D62FAA13E6306C44');
        $this->addSql('ALTER TABLE tile_glm_tile_buy_cost_glm DROP FOREIGN KEY FK_D62FAA133209D372');
        $this->addSql('ALTER TABLE tile_glm_tile_buy_bonus_glm DROP FOREIGN KEY FK_9BA76A1FE6306C44');
        $this->addSql('ALTER TABLE tile_glm_tile_buy_bonus_glm DROP FOREIGN KEY FK_9BA76A1FD5F29361');
        $this->addSql('ALTER TABLE tile_glm_tile_activation_cost_glm DROP FOREIGN KEY FK_5D99329AE6306C44');
        $this->addSql('ALTER TABLE tile_glm_tile_activation_cost_glm DROP FOREIGN KEY FK_5D99329A8DC7FD6D');
        $this->addSql('ALTER TABLE tile_glm_tile_activation_bonus_glm DROP FOREIGN KEY FK_F48E703E6306C44');
        $this->addSql('ALTER TABLE tile_glm_tile_activation_bonus_glm DROP FOREIGN KEY FK_F48E703584550BA');
        $this->addSql('DROP TABLE tile_activation_bonus_glm');
        $this->addSql('DROP TABLE tile_activation_cost_glm');
        $this->addSql('DROP TABLE tile_buy_bonus_glm');
        $this->addSql('DROP TABLE tile_buy_cost_glm');
        $this->addSql('DROP TABLE tile_glm_tile_buy_cost_glm');
        $this->addSql('DROP TABLE tile_glm_tile_buy_bonus_glm');
        $this->addSql('DROP TABLE tile_glm_tile_activation_cost_glm');
        $this->addSql('DROP TABLE tile_glm_tile_activation_bonus_glm');
        //$this->addSql('ALTER TABLE card_glm DROP FOREIGN KEY FK_1862B93869545666');
        $this->addSql('ALTER TABLE card_glm ADD CONSTRAINT FK_1862B93869545666 FOREIGN KEY (bonus_id) REFERENCES tile_bonus_glm (id)');
    }
}
