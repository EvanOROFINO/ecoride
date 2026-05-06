-- =====================================================================
-- EcoRide - Script de création de la base de données relationnelle
-- SGBD : MySQL 8.0+ / MariaDB 10.6+
-- Encodage : utf8mb4 (supporte les emojis et caractères internationaux)
-- =====================================================================

DROP DATABASE IF EXISTS ecoride;
CREATE DATABASE ecoride
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE ecoride;

-- ---------------------------------------------------------------------
-- Table : role
-- Rôles applicatifs (utilisateur, chauffeur, passager, employe, admin)
-- ---------------------------------------------------------------------
CREATE TABLE role (
    role_id     INT AUTO_INCREMENT PRIMARY KEY,
    libelle     VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : utilisateur
-- Comptes utilisateurs de la plateforme
-- ---------------------------------------------------------------------
CREATE TABLE utilisateur (
    utilisateur_id     INT AUTO_INCREMENT PRIMARY KEY,
    pseudo             VARCHAR(50) NOT NULL UNIQUE,
    email              VARCHAR(150) NOT NULL UNIQUE,
    password_hash      VARCHAR(255) NOT NULL,
    nom                VARCHAR(50),
    prenom             VARCHAR(50),
    telephone          VARCHAR(20),
    date_naissance     DATE,
    photo              VARCHAR(255) DEFAULT 'default-avatar.png',
    credit             INT NOT NULL DEFAULT 20,
    statut             ENUM('actif','suspendu') NOT NULL DEFAULT 'actif',
    date_creation      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_pseudo (pseudo)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : utilisateur_role (table associative N-N)
-- Un utilisateur peut avoir plusieurs rôles, et inversement
-- ---------------------------------------------------------------------
CREATE TABLE utilisateur_role (
    utilisateur_id     INT NOT NULL,
    role_id            INT NOT NULL,
    PRIMARY KEY (utilisateur_id, role_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (role_id)        REFERENCES role(role_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : marque
-- Référentiel des marques automobiles
-- ---------------------------------------------------------------------
CREATE TABLE marque (
    marque_id      INT AUTO_INCREMENT PRIMARY KEY,
    libelle        VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : voiture
-- Véhicules déclarés par les chauffeurs
-- ---------------------------------------------------------------------
CREATE TABLE voiture (
    voiture_id                       INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id                   INT NOT NULL,
    marque_id                        INT NOT NULL,
    modele                           VARCHAR(50) NOT NULL,
    immatriculation                  VARCHAR(15) NOT NULL UNIQUE,
    energie                          ENUM('essence','diesel','electrique','hybride','gpl') NOT NULL,
    couleur                          VARCHAR(30),
    date_premiere_immatriculation    DATE,
    nb_places                        TINYINT NOT NULL DEFAULT 4,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (marque_id)      REFERENCES marque(marque_id),
    INDEX idx_energie (energie)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : covoiturage
-- Trajets proposés par les chauffeurs
-- ---------------------------------------------------------------------
CREATE TABLE covoiturage (
    covoiturage_id     INT AUTO_INCREMENT PRIMARY KEY,
    chauffeur_id       INT NOT NULL,
    voiture_id         INT NOT NULL,
    date_depart        DATE NOT NULL,
    heure_depart       TIME NOT NULL,
    date_arrivee       DATE NOT NULL,
    heure_arrivee      TIME NOT NULL,
    lieu_depart        VARCHAR(100) NOT NULL,
    lieu_arrivee       VARCHAR(100) NOT NULL,
    statut             ENUM('prevu','en_cours','termine','annule') NOT NULL DEFAULT 'prevu',
    nb_place           TINYINT NOT NULL,
    prix_personne      DECIMAL(6,2) NOT NULL,
    FOREIGN KEY (chauffeur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (voiture_id)   REFERENCES voiture(voiture_id),
    INDEX idx_recherche (lieu_depart, lieu_arrivee, date_depart),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : participation
-- Inscriptions des passagers à un covoiturage
-- ---------------------------------------------------------------------
CREATE TABLE participation (
    participation_id     INT AUTO_INCREMENT PRIMARY KEY,
    covoiturage_id       INT NOT NULL,
    passager_id          INT NOT NULL,
    date_inscription     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut_validation    ENUM('en_attente','valide_ok','valide_probleme','annule') NOT NULL DEFAULT 'en_attente',
    commentaire_probleme TEXT NULL,
    UNIQUE KEY uk_participation (covoiturage_id, passager_id),
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturage(covoiturage_id) ON DELETE CASCADE,
    FOREIGN KEY (passager_id)    REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : avis
-- Avis laissés par les passagers sur les chauffeurs
-- (modération obligatoire avant affichage)
-- ---------------------------------------------------------------------
CREATE TABLE avis (
    avis_id            INT AUTO_INCREMENT PRIMARY KEY,
    auteur_id          INT NOT NULL,
    chauffeur_id       INT NOT NULL,
    covoiturage_id     INT NOT NULL,
    commentaire        TEXT,
    note               TINYINT NOT NULL CHECK (note BETWEEN 1 AND 5),
    statut             ENUM('en_attente','valide','refuse') NOT NULL DEFAULT 'en_attente',
    date_creation      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id)      REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (chauffeur_id)   REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturage(covoiturage_id) ON DELETE CASCADE,
    INDEX idx_chauffeur_statut (chauffeur_id, statut)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : preference
-- Préférences clé/valeur des chauffeurs (extensibles)
-- ---------------------------------------------------------------------
CREATE TABLE preference (
    preference_id      INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id     INT NOT NULL,
    cle                VARCHAR(50) NOT NULL,
    valeur             VARCHAR(255) NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    INDEX idx_user_cle (utilisateur_id, cle)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table : credit_plateforme
-- Trace les commissions perçues par la plateforme (2 crédits par trajet)
-- Permet le graphique admin US 13
-- ---------------------------------------------------------------------
CREATE TABLE credit_plateforme (
    transaction_id     INT AUTO_INCREMENT PRIMARY KEY,
    covoiturage_id     INT NOT NULL,
    montant            INT NOT NULL,
    date_transaction   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturage(covoiturage_id) ON DELETE CASCADE,
    INDEX idx_date (date_transaction)
) ENGINE=InnoDB;
