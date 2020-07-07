<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200707034159 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(
            'CREATE TABLE address (
                id INT AUTO_INCREMENT NOT NULL,
                street_number VARCHAR(255) NOT NULL,
                street_name VARCHAR(255) NOT NULL,
                neighborhood VARCHAR(255) NOT NULL,
                city VARCHAR(255) NOT NULL,
                state VARCHAR(255) NOT NULL,
                zip_code VARCHAR(255) NOT NULL,
                latitude VARCHAR(255) NOT NULL,
                longitude VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE tracking (
                id INT AUTO_INCREMENT NOT NULL,
                trucker_id INT NOT NULL,
                from_address_id INT NOT NULL,
                to_address_id INT DEFAULT NULL,
                check_in VARCHAR(255) NOT NULL,
                check_out VARCHAR(255) DEFAULT NULL,
                INDEX IDX_A87C621CF7441CDA (trucker_id),
                INDEX IDX_A87C621CDE136972 (from_address_id),
                INDEX IDX_A87C621CD2844D08 (to_address_id),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE truck_type (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE trucker (
                id INT AUTO_INCREMENT NOT NULL,
                truck_type_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                birthdate VARCHAR(255) NOT NULL,
                gender VARCHAR(2) DEFAULT NULL,
                is_owner TINYINT(1) NOT NULL,
                cnh_type VARCHAR(3) NOT NULL,
                is_loaded TINYINT(1) NOT NULL,
                INDEX IDX_39031BED2FDA3C7 (truck_type_id),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE user (
                id INT AUTO_INCREMENT NOT NULL,
                email VARCHAR(180) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL,
                UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
                PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4
            COLLATE `utf8mb4_unicode_ci`
            ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE tracking
            ADD CONSTRAINT FK_A87C621CF7441CDA
            FOREIGN KEY (trucker_id)
            REFERENCES trucker (id)');
        $this->addSql(
            'ALTER TABLE tracking
            ADD CONSTRAINT FK_A87C621CDE136972
            FOREIGN KEY (from_address_id)
            REFERENCES address (id)');
        $this->addSql(
            'ALTER TABLE tracking
            ADD CONSTRAINT FK_A87C621CD2844D08
            FOREIGN KEY (to_address_id)
            REFERENCES address (id)');
        $this->addSql(
            'ALTER TABLE trucker
            ADD CONSTRAINT FK_39031BED2FDA3C7
            FOREIGN KEY (truck_type_id)
            REFERENCES truck_type (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql(
            'ALTER TABLE tracking
            DROP FOREIGN KEY FK_A87C621CDE136972'
        );
        $this->addSql(
            'ALTER TABLE tracking
            DROP FOREIGN KEY FK_A87C621CD2844D08'
        );
        $this->addSql(
            'ALTER TABLE trucker
            DROP FOREIGN KEY FK_39031BED2FDA3C7'
        );
        $this->addSql(
            'ALTER TABLE tracking
            DROP FOREIGN KEY FK_A87C621CF7441CDA'
        );
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE tracking');
        $this->addSql('DROP TABLE truck_type');
        $this->addSql('DROP TABLE trucker');
        $this->addSql('DROP TABLE user');
    }
}
