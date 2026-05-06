# EcoRide

Plateforme de covoiturage écologique. Projet réalisé dans le cadre du Titre Professionnel **Développeur Web et Web Mobile** (Studi).

## Stack technique

- **Front** : HTML5, CSS3 (Bootstrap 5), JavaScript (ES6)
- **Back** : PHP 8.2, PDO
- **Base de données relationnelle** : MySQL 8
- **Base de données NoSQL** : MongoDB Atlas
- **Déploiement** : Railway

## Prérequis

- PHP >= 8.2
- MySQL >= 8.0 (ou MariaDB >= 10.6)
- Composer (pour le driver MongoDB et PHPMailer)
- Un compte MongoDB Atlas (gratuit)

## Installation locale

### 1. Cloner le dépôt

```bash
git clone https://github.com/<votre-user>/ecoride.git
cd ecoride
```

### 2. Configurer l'environnement

```bash
cp .env.example .env
```

Éditer le fichier `.env` avec vos identifiants MySQL et MongoDB.

### 3. Installer les dépendances

```bash
composer install
```

### 4. Créer et peupler la base MySQL

Via phpMyAdmin (XAMPP), importer dans l'ordre :

1. `sql/01_schema.sql` — création des tables
2. `sql/02_seed.sql` — données de test

Ou en ligne de commande :

```bash
mysql -u root -p < sql/01_schema.sql
mysql -u root -p < sql/02_seed.sql
```

### 5. Initialiser MongoDB

```bash
php sql/init_mongo.php
```

### 6. Lancer le serveur

Avec XAMPP : copier le dossier dans `C:\xampp\htdocs\ecoride` et démarrer Apache.

Ou avec le serveur PHP intégré :

```bash
php -S localhost:8000 -t public
```

Ouvrir http://localhost:8000

## Comptes de test

Voir `docs/manuel_utilisation.pdf` pour les identifiants des différents rôles (visiteur, utilisateur, employé, administrateur).

## Workflow Git

- `main` : branche de production (déployée)
- `develop` : branche d'intégration
- `feature/<nom>` : une branche par fonctionnalité, mergée dans `develop` après tests

## Auteur

Evan Orofino — Promotion DWWM 2026
