<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222142753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE order_sequence INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_sequence INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ORDERS (ID INT NOT NULL, NAME VARCHAR(255) NOT NULL, CREATE_DATE DATE NOT NULL, USER_ID INT NOT NULL, DESCRIPTION VARCHAR(255) DEFAULT NULL, VALID_TO DATE DEFAULT NULL, STATUS INT NOT NULL, PRIMARY KEY(ID))');
        $this->addSql('CREATE INDEX IDX_15D73A68A0666B6F ON ORDERS (USER_ID)');
        $this->addSql('CREATE TABLE USERS (ID INT NOT NULL, NAME VARCHAR(255) NOT NULL, LOGIN VARCHAR(255) NOT NULL, EMAIL VARCHAR(255) NOT NULL, PASSWORD VARCHAR(255) DEFAULT NULL, REGISTRATION_DATE TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ROLES JSON NOT NULL, END_DATE DATE DEFAULT NULL, STATUS INT NOT NULL, PRIMARY KEY(ID))');
        $this->addSql('ALTER TABLE ORDERS ADD CONSTRAINT FK_15D73A68A0666B6F FOREIGN KEY (USER_ID) REFERENCES USERS (ID) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE order_sequence CASCADE');
        $this->addSql('DROP SEQUENCE user_sequence CASCADE');
        $this->addSql('ALTER TABLE ORDERS DROP CONSTRAINT FK_15D73A68A0666B6F');
        $this->addSql('DROP TABLE ORDERS');
        $this->addSql('DROP TABLE USERS');
    }
}
