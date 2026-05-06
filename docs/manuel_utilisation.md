# Manuel d'utilisation — EcoRide

> Plateforme de covoiturage écologique — Version 1.0

---

## 1. Présentation

EcoRide est une plateforme de covoiturage qui privilégie les trajets écologiques (voitures électriques) et facilite le partage entre conducteurs et passagers. Le site est accessible à trois types d'utilisateurs : **visiteurs**, **utilisateurs** (chauffeurs et/ou passagers), **employés** et **administrateurs**.

## 2. Accès à l'application

- **En local** : http://localhost:8000 (serveur PHP)
- **En production** : https://ecoride.up.railway.app *(à compléter après déploiement)*

## 3. Comptes de démonstration

Mot de passe pour tous les comptes : **`Password123!`**

| Rôle | Email | Pseudo | Crédits |
|------|-------|--------|---------|
| Administrateur | `admin@ecoride.fr` | admin | 9999 |
| Employé | `employe@ecoride.fr` | employe1 | 100 |
| Chauffeur + passager | `sophie@example.com` | sophie_b | 50 |
| Chauffeur | `lucas@example.com` | lucas_m | 75 |
| Chauffeur + passager | `julie@example.com` | julie_r | 40 |
| Passager | `tom@example.com` | tom_d | 20 |
| Passager | `emma@example.com` | emma_l | 30 |
| Passager | `paul@example.com` | paul_g | 25 |

## 4. Parcours visiteur

### 4.1 Page d'accueil (US 1)
1. Ouvrir http://localhost:8000
2. La page présente l'entreprise, une barre de recherche et un footer (mail + mentions légales)

### 4.2 Recherche d'un covoiturage (US 3-4)
1. Sur la page d'accueil, saisir une ville de départ (`Paris`), d'arrivée (`Lyon`) et une date
2. Cliquer sur **Rechercher**
3. La liste des trajets correspondants s'affiche
4. Utiliser les filtres dans la sidebar pour affiner :
   - **Écologique uniquement** : ne montre que les voitures électriques
   - **Prix maximum** : en crédits par personne
   - **Durée maximum** : en minutes
   - **Note minimale** : filtre par note moyenne du chauffeur
5. Si aucun résultat, le site propose la **prochaine date disponible**

### 4.3 Détail d'un covoiturage (US 5)
1. Cliquer sur **Détails** d'un trajet
2. La page affiche : véhicule, marque, énergie, préférences chauffeur, avis validés
3. Bouton **Participer** si connecté avec assez de crédits

## 5. Parcours utilisateur (US 6, 7, 8, 9, 10, 11)

### 5.1 Création de compte (US 7)
1. Cliquer sur **Connexion** > **Créer un compte**
2. Renseigner un pseudo (3-50 car.), un email valide, un mot de passe sécurisé
   - **Min 8 caractères**
   - **1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial**
3. À la création, le compte reçoit **20 crédits offerts**

### 5.2 Espace utilisateur (US 8)
Une fois connecté, accéder à **Mon espace** :
- **Choisir son rôle** : chauffeur, passager, ou les deux (case à cocher)
- **Mes véhicules** (chauffeur uniquement) : ajouter une voiture (marque, modèle, immatriculation, énergie, places)
- **Mes préférences** : fumeur, animaux, et préférences personnalisées (clé/valeur libre stockées en MongoDB)

### 5.3 Proposer un voyage (US 9)
1. **Mon espace > Proposer un voyage**
2. Renseigner : départ, arrivée, dates, heures, véhicule, places, prix
3. Le prix doit être ≥ 3 crédits (2 prélevés par la plateforme)

### 5.4 Participer à un voyage (US 6)
1. Sur la page détail d'un trajet, cliquer **Participer**
2. **Double confirmation** demandée
3. Les crédits sont prélevés immédiatement, la place décrémentée

### 5.5 Historique (US 10)
- **Mon espace > Mon historique**
- Visualiser tous ses trajets (chauffeur et passager)
- **Annuler** un trajet (chauffeur : remboursement passagers + mail) ou (passager : remboursement de ses crédits)

### 5.6 Démarrer / arrêter un trajet (US 11)
- **Chauffeur** : depuis l'historique, bouton **Démarrer** puis **Arrivée à destination**
- **Passager** : après l'arrivée, bouton **Valider le trajet**
  - Indiquer si tout s'est bien passé (oui/non)
  - Si oui : noter (1-5 étoiles) + commentaire — l'avis passe en modération employé
  - Si non : commentaire requis — un employé contactera le chauffeur

## 6. Parcours employé (US 12)

Connexion avec `employe@ecoride.fr`. Accéder à **Espace employé** :

### 6.1 Modération des avis
- **Espace employé > Avis à modérer**
- Liste des avis en attente
- **Valider** ou **Refuser** chaque avis (le chauffeur ne voit que les avis validés)

### 6.2 Gestion des incidents
- **Espace employé > Incidents**
- Liste des trajets signalés problématiques
- Coordonnées chauffeur et passager affichées (mail cliquable)
- Description du problème pour traitement

## 7. Parcours administrateur (US 13)

Connexion avec `admin@ecoride.fr`. Accéder à **Admin** :

### 7.1 Tableau de bord
- 3 KPI : crédits gagnés, utilisateurs, covoiturages
- **Graphique** : nombre de covoiturages par jour (30 derniers jours)
- **Graphique** : crédits gagnés par jour (30 derniers jours)

### 7.2 Gestion des comptes
- **Admin > Gérer les comptes**
- Liste de tous les utilisateurs avec rôles
- **Suspendre** ou **Réactiver** un compte (utilisateur ou employé)
- **Créer un compte employé** depuis le formulaire dédié

> **Note** : la création du compte administrateur se fait directement en BDD (script SQL `02_seed.sql`), pas depuis l'application.

## 8. Sécurité

- Mots de passe hashés en **bcrypt** (jamais stockés en clair)
- **Protection CSRF** sur tous les formulaires POST
- **Sessions HTTPOnly** + `SameSite=Lax` + régénération à la connexion
- **Requêtes préparées PDO** (protection injection SQL)
- **Échappement HTML** (htmlspecialchars) sur tous les affichages utilisateur (anti-XSS)
- En-têtes de sécurité : `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`

## 9. Support

- Email : `contact@ecoride.fr`
- Page contact : `/contact`
- Mentions légales : `/mentions-legales`
