# 🌿 EcoRide

> Plateforme de covoiturage écologique — TP Développeur Web et Web Mobile (Studi).

🌐 **Application en ligne : https://orofino.alwaysdata.net**

EcoRide met en relation conducteurs et passagers pour des trajets partagés, en valorisant les voyages écologiques (voitures électriques) via un badge dédié et un système de filtres.

## ✨ Fonctionnalités (13 user stories)

| US | Description | Rôle |
|----|-------------|------|
| 1 | Page d'accueil avec barre de recherche | Visiteur |
| 2 | Menu de navigation | Visiteur |
| 3 | Recherche de covoiturages par ville/date | Visiteur |
| 4 | Filtres (écologique, prix, durée, note) | Visiteur |
| 5 | Vue détaillée d'un covoiturage | Visiteur / Utilisateur |
| 6 | Participer à un covoiturage (avec crédits) | Utilisateur |
| 7 | Création de compte + connexion sécurisée | Visiteur |
| 8 | Espace utilisateur (rôles, véhicules, préférences) | Utilisateur |
| 9 | Saisir un voyage en tant que chauffeur | Chauffeur |
| 10 | Historique + annulation | Utilisateur |
| 11 | Démarrer / arrêter un trajet + validation passager + notation | Utilisateur |
| 12 | Espace employé (modération avis + incidents) | Employé |
| 13 | Tableau de bord administrateur (graphiques, gestion comptes) | Administrateur |

## 🛠 Stack technique

- **Front** : HTML5, CSS3 (charte graphique custom), JavaScript ES6
- **Icônes** : Bootstrap Icons (CDN)
- **Graphiques** : Chart.js (CDN)
- **Back** : PHP 8.2, PDO, autoload PSR-4
- **BDD relationnelle** : MySQL 8 / MariaDB 10
- **BDD NoSQL** : MongoDB (avis, préférences, configuration)
- **Authentification** : sessions PHP + bcrypt + CSRF
- **Déploiement** : Railway.app (Nixpacks)

## 📋 Prérequis

- PHP 8.2 (avec extensions `pdo_mysql`, `mongodb`, `zip`)
- MySQL 8 ou MariaDB 10
- Composer 2.x
- Git
- Un compte MongoDB Atlas (gratuit) **OU** MongoDB Community Server local

## 🚀 Installation locale

### 1. Cloner le dépôt
```bash
git clone https://github.com/EvanOROFINO/ecoride.git
cd ecoride
```

> **Dépôt public** : https://github.com/EvanOROFINO/ecoride

### 2. Configurer l'environnement
```bash
cp .env.example .env
```
Éditer le fichier `.env` :
- `DB_*` : identifiants MySQL
- `MONGO_URI` : votre cluster Atlas ou `mongodb://localhost:27017`
- `APP_SECRET` : 32 caractères aléatoires

### 3. Installer les dépendances
```bash
composer install
```

### 4. Créer et peupler la base MySQL

**Option A — phpMyAdmin (XAMPP)** : importer `sql/01_schema.sql` puis `sql/02_seed.sql`

**Option B — ligne de commande** :
```bash
mysql -u root -p < sql/01_schema.sql
mysql -u root -p < sql/02_seed.sql
```

### 5. Initialiser MongoDB
```bash
php sql/init_mongo.php
```

### 6. Lancer le serveur de développement
```bash
php -S localhost:8000 -t public
```

Ouvrir http://localhost:8000

## 👥 Comptes de démonstration

Mot de passe pour tous les comptes : **`Password123!`**

| Rôle | Email |
|------|-------|
| Administrateur | `admin@ecoride.fr` |
| Employé | `employe@ecoride.fr` |
| Chauffeur + passager | `sophie@example.com`, `julie@example.com` |
| Chauffeur | `lucas@example.com` |
| Passager | `tom@example.com`, `emma@example.com`, `paul@example.com` |

Voir `docs/manuel_utilisation.md` pour les parcours détaillés.

## 🔐 Sécurité

- Mots de passe hachés en **bcrypt** (`password_hash` / `password_verify`)
- **Validation de robustesse** des mots de passe (min 8 car., maj/min/chiffre/spécial)
- **Protection CSRF** sur tous les formulaires POST (jeton de session vérifié par `hash_equals`)
- **Sessions strictes** : `HttpOnly`, `SameSite=Lax`, `use_strict_mode`, régénération à la connexion
- **Requêtes préparées PDO** (anti-injection SQL)
- **Échappement HTML** systématique sur les sorties (anti-XSS)
- En-têtes de sécurité : `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`
- Contrôle d'accès par rôle (`Auth::requireRole(...)`)

## 🌳 Workflow Git

- `main` : branche de production (déployée sur Railway)
- `develop` : branche d'intégration (toutes les US validées localement)
- `feature/<nom>` : une branche par US, mergée dans `develop` après tests

```
main           ●────────●────────●────────● (production)
                \      /        /        /
develop          ●────●────────●────────● (intégration)
                  \  / \      / \      /
feature/us-N      ●●    \    /   \    /
```

## 📁 Structure du projet

```
ecoride/
├── config/                  # Configuration centrale
├── public/                  # Document root (index.php, .htaccess, css, js)
├── src/
│   ├── Core/                # Database, Mongo, Router, Controller, Auth, Security
│   ├── Controllers/         # 6 controllers par domaine fonctionnel
│   ├── Models/              # 8 modèles (User, Covoiturage, Avis, ...)
│   └── Helpers/functions.php
├── views/                   # Templates PHP par domaine
├── sql/                     # 01_schema.sql, 02_seed.sql, init_mongo.php
├── docs/                    # Charte, MCD, manuel, doc technique, kanban, maquettes
├── routes.php               # Définition des routes
└── composer.json
```

## 🚢 Déploiement Railway

1. Pousser le dépôt sur GitHub (public)
2. Sur [Railway](https://railway.app) : `New Project > Deploy from GitHub`
3. Ajouter un plugin **MySQL** (Railway génère les variables `DB_*`)
4. Renseigner les variables d'environnement : `APP_*`, `MONGO_URI`, `MONGO_DB`
5. Le build est géré par `nixpacks.toml` (PHP 8.2 + extensions)
6. Au premier déploiement, exécuter `sql/01_schema.sql` et `sql/02_seed.sql` via la console Railway
7. Ouvrir l'URL Railway pour accéder à l'application

## 📚 Documentation

| Document | Emplacement |
|----------|-------------|
| Manuel d'utilisation | `docs/manuel_utilisation.md` |
| Charte graphique | `docs/charte/charte_graphique.md` |
| Modèle conceptuel de données | `docs/technique/mcd.md` |
| Schéma MongoDB | `docs/technique/schema_mongodb.md` |
| Documentation technique | `docs/technique/documentation_technique.md` |
| Gestion de projet | `docs/gestion_projet.md` |
| Kanban | `docs/kanban.md` |
| Maquettes (wireframes) | `docs/maquettes/maquettes.html` |

## 👤 Auteur

Evan Orofino — Promotion DWWM 2026
