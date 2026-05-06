# 📁 Structure du dossier EcoRide

> **Tout est dans `C:\Users\evano\Desktop\EcoRide\`** — voici comment c'est organisé.

## 🎁 Pour le jury (le plus important)

```
EcoRide/
└── LIVRABLES_JURY/                       👈 Tous les PDFs à fournir au jury
    ├── LISEZMOI.txt                         Récap + identifiants + liens
    ├── 01_Manuel_utilisation.pdf
    ├── 02_Charte_graphique.pdf
    ├── 03_Maquettes_wireframes.pdf
    ├── 04_Documentation_technique.pdf
    ├── 05_MCD_modele_donnees.pdf
    ├── 06_Schema_MongoDB.pdf
    ├── 07_Gestion_de_projet.pdf
    └── 08_Kanban.pdf
```

## 💻 Code source de l'application

```
EcoRide/
├── README.md                             📘 Présentation + instructions install
├── .env.example                          🔧 Modèle de variables d'environnement
├── .env                                  ⚠️ Ta config (NON commité)
├── .gitignore
├── composer.json / composer.lock         📦 Dépendances PHP (vendor/)
├── package.json / package-lock.json      📦 Dépendance Node (marked, pour PDF)
├── Procfile                              🚢 Config déploiement Railway/Heroku
├── railway.toml                          🚢 Config Railway
├── nixpacks.toml                         🚢 Config build Nixpacks
├── routes.php                            🛣️ Définition des routes HTTP
│
├── config/
│   └── config.php                        ⚙️ Configuration centrale (BDD, Mongo)
│
├── public/                               🌐 Document root (servi par le serveur)
│   ├── index.php                            Front controller (point d'entrée)
│   ├── .htaccess                            URL rewriting + headers sécurité
│   ├── css/app.css                          Styles personnalisés (charte)
│   ├── js/app.js                            JavaScript (menu mobile, modals)
│   ├── images/
│   └── uploads/
│
├── src/                                  💡 Code applicatif (PSR-4 → App\)
│   ├── Core/                                Classes de base
│   │   ├── Database.php                     Singleton PDO MySQL
│   │   ├── Mongo.php                        Singleton MongoDB
│   │   ├── Router.php                       Routeur HTTP
│   │   ├── Controller.php                   Classe parent des controllers
│   │   ├── Auth.php                         Authentification + rôles
│   │   └── Security.php                     CSRF, bcrypt, validation mdp
│   ├── Controllers/                         6 controllers
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── CovoiturageController.php
│   │   ├── UserController.php
│   │   ├── EmployeController.php
│   │   ├── AdminController.php
│   │   └── ContactController.php
│   ├── Models/                              8 models
│   │   ├── User.php
│   │   ├── Covoiturage.php
│   │   ├── Voiture.php
│   │   ├── Marque.php
│   │   ├── Participation.php
│   │   ├── Avis.php                         (MongoDB)
│   │   ├── Preference.php                   (MongoDB)
│   │   └── Stats.php                        (agrégations admin)
│   └── Helpers/
│       └── functions.php                    e(), csrf_field(), format_date_fr()
│
├── views/                                🖼️ Templates PHP
│   ├── layouts/main.php                     Layout principal
│   ├── partials/                            navbar, footer
│   ├── home/                                accueil, contact, mentions légales
│   ├── auth/                                login, register
│   ├── covoiturage/                         index (liste + filtres), show (détail)
│   ├── user/                                dashboard, vehicules, preferences,
│   │                                        voyage-nouveau, historique
│   ├── employe/                             dashboard, avis, incidents
│   ├── admin/                               dashboard (graphiques), employes
│   └── errors/                              404, 403, 500
│
├── sql/                                  🗃️ Scripts BDD
│   ├── 01_schema.sql                        Création des tables MySQL
│   ├── 02_seed.sql                          Données de test
│   └── init_mongo.php                       Init des collections MongoDB
│
└── tools/
    └── md-to-html.cjs                       Script Node pour convertir MD → HTML
```

## 📚 Sources des documents

```
EcoRide/
└── docs/                                 📝 Sources Markdown des PDFs
    ├── manuel_utilisation.md
    ├── gestion_projet.md
    ├── kanban.md
    ├── charte/
    │   └── charte_graphique.md
    ├── maquettes/
    │   └── maquettes.html               (HTML directement, pas de MD)
    ├── technique/
    │   ├── mcd.md
    │   ├── schema_mongodb.md
    │   └── documentation_technique.md
    └── pdf/                              💾 PDFs générés (copiés dans LIVRABLES_JURY/)
        └── *.pdf
```

## 🔧 Dépendances (auto-générées)

```
EcoRide/
├── vendor/                               (Composer — non commité)
└── node_modules/                         (npm marked — non commité)
```

---

## 📊 Récapitulatif

| Type | Emplacement | À toucher ? |
|------|-------------|-------------|
| **PDFs pour le jury** | `LIVRABLES_JURY/` | ✅ À envoyer tel quel |
| **Code de l'app** | `src/`, `views/`, `public/` | ❌ Tout est prêt |
| **Sources docs** | `docs/` | ❌ Déjà converties |
| **Configuration** | `config/`, `.env`, `routes.php` | ❌ Déjà configurées |
| **Déploiement** | `Procfile`, `railway.toml`, `nixpacks.toml` | ⏳ Quand tu déploieras |
