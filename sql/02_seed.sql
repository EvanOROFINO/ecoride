-- =====================================================================
-- EcoRide - Jeu de données de test
-- À exécuter APRÈS 01_schema.sql
-- =====================================================================

USE ecoride;

-- ---------------------------------------------------------------------
-- Rôles applicatifs
-- ---------------------------------------------------------------------
INSERT INTO role (role_id, libelle) VALUES
    (1, 'utilisateur'),
    (2, 'chauffeur'),
    (3, 'passager'),
    (4, 'employe'),
    (5, 'administrateur');

-- ---------------------------------------------------------------------
-- Marques de véhicules (référentiel)
-- ---------------------------------------------------------------------
INSERT INTO marque (libelle) VALUES
    ('Renault'), ('Peugeot'), ('Citroën'), ('Tesla'), ('Toyota'),
    ('Volkswagen'), ('BMW'), ('Mercedes'), ('Hyundai'), ('Dacia');

-- ---------------------------------------------------------------------
-- Utilisateurs de test
-- IMPORTANT : tous les mots de passe sont "Password123!" hachés en bcrypt
-- Hash généré avec : password_hash('Password123!', PASSWORD_DEFAULT)
-- Note : password_verify() compare correctement même si le hash est unique par appel
-- ---------------------------------------------------------------------
INSERT INTO utilisateur (utilisateur_id, pseudo, email, password_hash, nom, prenom, telephone, date_naissance, photo, credit, statut) VALUES
    (1, 'admin',     'admin@ecoride.fr',     '$2y$10$FoRsdBMBPm1txeIQYG1Vi.xccpp1FWOre6Aua1xH8ZfwSLnJimFWW', 'Martin',  'Alice',   '0612345678', '1985-04-12', 'default-avatar.png', 9999, 'actif'),
    (2, 'employe1',  'employe@ecoride.fr',   '$2y$10$FoRsdBMBPm1txeIQYG1Vi.xccpp1FWOre6Aua1xH8ZfwSLnJimFWW', 'Dubois',  'Bernard', '0612345679', '1990-06-23', 'default-avatar.png', 100,  'actif'),
    (3, 'sophie_b',  'sophie@example.com',   '$2y$10$FoRsdBMBPm1txeIQYG1Vi.xccpp1FWOre6Aua1xH8ZfwSLnJimFWW', 'Bernard', 'Sophie',  '0698765432', '1992-09-15', 'default-avatar.png', 50,   'actif'),
    (4, 'lucas_m',   'lucas@example.com',    '$2y$10$FoRsdBMBPm1txeIQYG1Vi.xccpp1FWOre6Aua1xH8ZfwSLnJimFWW', 'Moreau',  'Lucas',   '0698765433', '1988-12-04', 'default-avatar.png', 75,   'actif'),
    (5, 'emma_l',    'emma@example.com',     '$2y$10$FoRsdBMBPm1txeIQYG1Vi.xccpp1FWOre6Aua1xH8ZfwSLnJimFWW', 'Leroy',   'Emma',    '0698765434', '1995-02-18', 'default-avatar.png', 30,   'actif'),
    (6, 'tom_d',     'tom@example.com',      '$2y$10$FoRsdBMBPm1txeIQYG1Vi.xccpp1FWOre6Aua1xH8ZfwSLnJimFWW', 'Dupont',  'Tom',     '0698765435', '1991-07-30', 'default-avatar.png', 20,   'actif'),
    (7, 'julie_r',   'julie@example.com',    '$2y$10$FoRsdBMBPm1txeIQYG1Vi.xccpp1FWOre6Aua1xH8ZfwSLnJimFWW', 'Roux',    'Julie',   '0698765436', '1993-11-08', 'default-avatar.png', 40,   'actif'),
    (8, 'paul_g',    'paul@example.com',     '$2y$10$FoRsdBMBPm1txeIQYG1Vi.xccpp1FWOre6Aua1xH8ZfwSLnJimFWW', 'Garnier', 'Paul',    '0698765437', '1987-03-22', 'default-avatar.png', 25,   'actif');

-- ---------------------------------------------------------------------
-- Attribution des rôles
-- ---------------------------------------------------------------------
INSERT INTO utilisateur_role (utilisateur_id, role_id) VALUES
    (1, 5),                  -- Alice = administrateur
    (2, 4),                  -- Bernard = employé
    (3, 1), (3, 2), (3, 3),  -- Sophie = utilisateur + chauffeur + passager
    (4, 1), (4, 2),          -- Lucas = utilisateur + chauffeur
    (5, 1), (5, 3),          -- Emma = utilisateur + passager
    (6, 1), (6, 3),          -- Tom = passager
    (7, 1), (7, 2), (7, 3),  -- Julie = chauffeur + passager
    (8, 1), (8, 3);          -- Paul = passager

-- ---------------------------------------------------------------------
-- Voitures (chauffeurs : Sophie=3, Lucas=4, Julie=7)
-- ---------------------------------------------------------------------
INSERT INTO voiture (voiture_id, utilisateur_id, marque_id, modele, immatriculation, energie, couleur, date_premiere_immatriculation, nb_places) VALUES
    (1, 3, 4, 'Model 3',    'AB-123-CD', 'electrique', 'Blanc',  '2022-05-10', 4),  -- Sophie - Tesla électrique
    (2, 3, 1, 'Clio',       'EF-456-GH', 'essence',    'Rouge',  '2019-03-15', 4),  -- Sophie - Renault
    (3, 4, 2, '208',        'IJ-789-KL', 'diesel',     'Gris',   '2020-08-22', 4),  -- Lucas - Peugeot
    (4, 7, 4, 'Model Y',    'MN-012-OP', 'electrique', 'Noir',   '2023-01-05', 5),  -- Julie - Tesla électrique
    (5, 7, 5, 'Yaris',      'QR-345-ST', 'hybride',    'Bleu',   '2021-11-30', 4);  -- Julie - Toyota hybride

-- ---------------------------------------------------------------------
-- Covoiturages
-- ---------------------------------------------------------------------
INSERT INTO covoiturage (covoiturage_id, chauffeur_id, voiture_id, date_depart, heure_depart, date_arrivee, heure_arrivee, lieu_depart, lieu_arrivee, statut, nb_place, prix_personne) VALUES
    (1, 3, 1, CURDATE() + INTERVAL 2 DAY,  '08:00:00', CURDATE() + INTERVAL 2 DAY,  '12:00:00', 'Paris',     'Lyon',      'prevu',    3, 35.00),
    (2, 3, 2, CURDATE() + INTERVAL 3 DAY,  '14:00:00', CURDATE() + INTERVAL 3 DAY,  '16:30:00', 'Paris',     'Reims',     'prevu',    2, 18.00),
    (3, 4, 3, CURDATE() + INTERVAL 2 DAY,  '09:30:00', CURDATE() + INTERVAL 2 DAY,  '13:30:00', 'Paris',     'Lyon',      'prevu',    4, 30.00),
    (4, 4, 3, CURDATE() + INTERVAL 5 DAY,  '07:00:00', CURDATE() + INTERVAL 5 DAY,  '11:00:00', 'Lyon',      'Marseille', 'prevu',    3, 25.00),
    (5, 7, 4, CURDATE() + INTERVAL 1 DAY,  '10:00:00', CURDATE() + INTERVAL 1 DAY,  '14:00:00', 'Paris',     'Bordeaux',  'prevu',    4, 40.00),
    (6, 7, 5, CURDATE() + INTERVAL 7 DAY,  '15:00:00', CURDATE() + INTERVAL 7 DAY,  '17:30:00', 'Bordeaux',  'Toulouse',  'prevu',    3, 22.00),
    (7, 3, 1, CURDATE() - INTERVAL 5 DAY,  '08:00:00', CURDATE() - INTERVAL 5 DAY,  '12:00:00', 'Paris',     'Lyon',      'termine',  0, 35.00),
    (8, 4, 3, CURDATE() - INTERVAL 10 DAY, '09:00:00', CURDATE() - INTERVAL 10 DAY, '13:00:00', 'Lyon',      'Marseille', 'termine',  0, 28.00);

-- ---------------------------------------------------------------------
-- Participations (passagers inscrits aux covoiturages)
-- ---------------------------------------------------------------------
INSERT INTO participation (covoiturage_id, passager_id, statut_validation) VALUES
    (1, 5, 'en_attente'),
    (1, 6, 'en_attente'),
    (3, 5, 'en_attente'),
    (5, 8, 'en_attente'),
    (5, 6, 'en_attente'),
    (7, 5, 'valide_ok'),
    (7, 8, 'valide_ok'),
    (8, 6, 'valide_ok');

-- ---------------------------------------------------------------------
-- Avis (sur les covoiturages terminés)
-- ---------------------------------------------------------------------
INSERT INTO avis (auteur_id, chauffeur_id, covoiturage_id, commentaire, note, statut) VALUES
    (5, 3, 7, 'Trajet très agréable, conductrice ponctuelle et sympa !', 5, 'valide'),
    (8, 3, 7, 'Très bien dans l''ensemble, je recommande.',                4, 'valide'),
    (6, 4, 8, 'Bon trajet, voiture confortable.',                          4, 'en_attente');

-- ---------------------------------------------------------------------
-- Préférences chauffeurs
-- ---------------------------------------------------------------------
INSERT INTO preference (utilisateur_id, cle, valeur) VALUES
    (3, 'fumeur',     'non'),
    (3, 'animaux',    'oui'),
    (3, 'musique',    'oui'),
    (4, 'fumeur',     'non'),
    (4, 'animaux',    'non'),
    (4, 'discussion', 'modérée'),
    (7, 'fumeur',     'non'),
    (7, 'animaux',    'oui'),
    (7, 'climatisation', 'oui');

-- ---------------------------------------------------------------------
-- Crédits gagnés par la plateforme (2 crédits par trajet terminé)
-- ---------------------------------------------------------------------
INSERT INTO credit_plateforme (covoiturage_id, montant, date_transaction) VALUES
    (7, 2, NOW() - INTERVAL 5 DAY),
    (8, 2, NOW() - INTERVAL 10 DAY);
