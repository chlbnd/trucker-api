<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200706050845 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(
                'CREATE TABLE tracking (
                    id INT AUTO_INCREMENT NOT NULL,
                    trucker_id INT NOT NULL,
                    from_lat VARCHAR(255) NOT NULL,
                    from_lon VARCHAR(255) NOT NULL,
                    to_lat VARCHAR(255) NOT NULL,
                    to_lon VARCHAR(255) NOT NULL,
                    check_in VARCHAR(255) NOT NULL,
                    check_out VARCHAR(255) DEFAULT NULL,
                    INDEX IDX_A87C621CF7441CDA (trucker_id),
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
            'ALTER TABLE tracking
            ADD CONSTRAINT FK_A87C621CF7441CDA
            FOREIGN KEY (trucker_id)
            REFERENCES trucker (id)'
        );
        $this->addSql(
            'ALTER TABLE trucker
            ADD CONSTRAINT FK_39031BED2FDA3C7
            FOREIGN KEY (truck_type_id)
            REFERENCES truck_type (id)'
        );

        $truckers = [
            ['id' => 1, 'name' => 'Caminhão 3/4'],
            ['id' => 2, 'name' => 'Caminhão Toco'],
            ['id' => 3, 'name' => 'Caminhão Truck'],
            ['id' => 4, 'name' => 'Carreta Simples'],
            ['id' => 5, 'name' => 'Carreta Eixo Estendido']
        ];

        foreach ($truckers as $trucker) {
            $this->addSql(
                'INSERT INTO truck_type(id, name)
                VALUES(:id, :name)', $trucker
            );
        }
    }

    public function down(Schema $schema) : void
    {
        $this->addSql(
            'ALTER TABLE trucker
            DROP FOREIGN KEY FK_39031BED2FDA3C7'
        );
        $this->addSql(
            'ALTER TABLE tracking
            DROP FOREIGN KEY FK_A87C621CF7441CDA'
        );
        $this->addSql('DROP TABLE tracking');
        $this->addSql('DROP TABLE truck_type');
        $this->addSql('DROP TABLE trucker');
    }
}
