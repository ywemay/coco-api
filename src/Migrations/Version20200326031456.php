<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200326031456 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE physical_address (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, province VARCHAR(15) NOT NULL, city VARCHAR(30) NOT NULL, street VARCHAR(150) NOT NULL, address VARCHAR(150) NOT NULL, lt DOUBLE PRECISION DEFAULT NULL, lg DOUBLE PRECISION DEFAULT NULL, locked TINYINT(1) NOT NULL, INDEX IDX_19F6C7AC7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sale_order (id INT AUTO_INCREMENT NOT NULL, assigned_to_id INT DEFAULT NULL, container_load_report_id INT DEFAULT NULL, owner_id INT NOT NULL, address_id INT NOT NULL, date DATE NOT NULL, state SMALLINT NOT NULL, container_type VARCHAR(6) NOT NULL, start_date_time DATETIME NOT NULL, price INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_25F5CB1BF4BD7827 (assigned_to_id), UNIQUE INDEX UNIQ_25F5CB1BDCEEBF30 (container_load_report_id), INDEX IDX_25F5CB1B7E3C61F9 (owner_id), INDEX IDX_25F5CB1BF5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE container_load_report (id INT AUTO_INCREMENT NOT NULL, sale_order_id INT DEFAULT NULL, created_at DATETIME NOT NULL, amount_received INT NOT NULL, amount_tip INT NOT NULL, total_amount INT NOT NULL, company_profit INT NOT NULL, teamleader_tip INT NOT NULL, per_worker_amount INT NOT NULL, balance INT NOT NULL, UNIQUE INDEX UNIQ_D17FE03093EB8192 (sale_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE container_load_report_user (container_load_report_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_606684DFDCEEBF30 (container_load_report_id), INDEX IDX_606684DFA76ED395 (user_id), PRIMARY KEY(container_load_report_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, company VARCHAR(120) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, email VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D6497BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE physical_address ADD CONSTRAINT FK_19F6C7AC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sale_order ADD CONSTRAINT FK_25F5CB1BF4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sale_order ADD CONSTRAINT FK_25F5CB1BDCEEBF30 FOREIGN KEY (container_load_report_id) REFERENCES container_load_report (id)');
        $this->addSql('ALTER TABLE sale_order ADD CONSTRAINT FK_25F5CB1B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sale_order ADD CONSTRAINT FK_25F5CB1BF5B7AF75 FOREIGN KEY (address_id) REFERENCES physical_address (id)');
        $this->addSql('ALTER TABLE container_load_report ADD CONSTRAINT FK_D17FE03093EB8192 FOREIGN KEY (sale_order_id) REFERENCES sale_order (id)');
        $this->addSql('ALTER TABLE container_load_report_user ADD CONSTRAINT FK_606684DFDCEEBF30 FOREIGN KEY (container_load_report_id) REFERENCES container_load_report (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE container_load_report_user ADD CONSTRAINT FK_606684DFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sale_order DROP FOREIGN KEY FK_25F5CB1BF5B7AF75');
        $this->addSql('ALTER TABLE container_load_report DROP FOREIGN KEY FK_D17FE03093EB8192');
        $this->addSql('ALTER TABLE sale_order DROP FOREIGN KEY FK_25F5CB1BDCEEBF30');
        $this->addSql('ALTER TABLE container_load_report_user DROP FOREIGN KEY FK_606684DFDCEEBF30');
        $this->addSql('ALTER TABLE physical_address DROP FOREIGN KEY FK_19F6C7AC7E3C61F9');
        $this->addSql('ALTER TABLE sale_order DROP FOREIGN KEY FK_25F5CB1BF4BD7827');
        $this->addSql('ALTER TABLE sale_order DROP FOREIGN KEY FK_25F5CB1B7E3C61F9');
        $this->addSql('ALTER TABLE container_load_report_user DROP FOREIGN KEY FK_606684DFA76ED395');
        $this->addSql('DROP TABLE physical_address');
        $this->addSql('DROP TABLE sale_order');
        $this->addSql('DROP TABLE container_load_report');
        $this->addSql('DROP TABLE container_load_report_user');
        $this->addSql('DROP TABLE user');
    }
}
