<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260324100202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE proj_contenu_panier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quantite INTEGER NOT NULL, user_id INTEGER NOT NULL, produit_id INTEGER NOT NULL, CONSTRAINT FK_9D15E74AA76ED395 FOREIGN KEY (user_id) REFERENCES proj_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9D15E74AF347EFB FOREIGN KEY (produit_id) REFERENCES proj_produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9D15E74AA76ED395 ON proj_contenu_panier (user_id)');
        $this->addSql('CREATE INDEX IDX_9D15E74AF347EFB ON proj_contenu_panier (produit_id)');
        $this->addSql('CREATE TABLE proj_pays (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, code VARCHAR(2) NOT NULL)');
        $this->addSql('CREATE TABLE proj_produit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, quantite_stock INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE proj_produit_pays (id_produit INTEGER NOT NULL, id_pays INTEGER NOT NULL, PRIMARY KEY (id_produit, id_pays), CONSTRAINT FK_F5E91F12F7384557 FOREIGN KEY (id_produit) REFERENCES proj_produit (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F5E91F12BFBF20AC FOREIGN KEY (id_pays) REFERENCES proj_pays (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F5E91F12F7384557 ON proj_produit_pays (id_produit)');
        $this->addSql('CREATE INDEX IDX_F5E91F12BFBF20AC ON proj_produit_pays (id_pays)');
        $this->addSql('CREATE TABLE proj_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, is_admin BOOLEAN NOT NULL, is_super_admin BOOLEAN NOT NULL, pays_id INTEGER DEFAULT NULL, CONSTRAINT FK_3ADA00E9A6E44244 FOREIGN KEY (pays_id) REFERENCES proj_pays (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_3ADA00E9A6E44244 ON proj_user (pays_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_LOGIN ON proj_user (login)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE proj_contenu_panier');
        $this->addSql('DROP TABLE proj_pays');
        $this->addSql('DROP TABLE proj_produit');
        $this->addSql('DROP TABLE proj_produit_pays');
        $this->addSql('DROP TABLE proj_user');
    }
}
