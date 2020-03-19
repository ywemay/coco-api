<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200319145417 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D6497BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sale_order (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, date DATE NOT NULL, state SMALLINT NOT NULL, container_type VARCHAR(6) NOT NULL, start_date_time DATETIME NOT NULL, price INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_25F5CB1B979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_4FBF094F7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE container_load_order (id INT AUTO_INCREMENT NOT NULL, assigned_to_id INT DEFAULT NULL, sale_order_id INT DEFAULT NULL, created_at DATETIME NOT NULL, amount_due INT NOT NULL, state SMALLINT NOT NULL, INDEX IDX_7F936EC5F4BD7827 (assigned_to_id), UNIQUE INDEX UNIQ_7F936EC593EB8192 (sale_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_order ADD CONSTRAINT FK_25F5CB1B979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE container_load_order ADD CONSTRAINT FK_7F936EC5F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE container_load_order ADD CONSTRAINT FK_7F936EC593EB8192 FOREIGN KEY (sale_order_id) REFERENCES sale_order (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F7E3C61F9');
        $this->addSql('ALTER TABLE container_load_order DROP FOREIGN KEY FK_7F936EC5F4BD7827');
        $this->addSql('ALTER TABLE container_load_order DROP FOREIGN KEY FK_7F936EC593EB8192');
        $this->addSql('ALTER TABLE sale_order DROP FOREIGN KEY FK_25F5CB1B979B1AD6');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE sale_order');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE container_load_order');
    }
}
