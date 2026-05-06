# Documentation gestion de projet — EcoRide

## 1. Méthodologie

### 1.1 Approche choisie : **Agile / Kanban**

Le projet EcoRide a été conduit selon une approche **Kanban** combinée à des principes Agile :
- Découpage du besoin en **User Stories** (US) numérotées
- Priorisation : du visiteur (US 1-7) vers les rôles privilégiés (US 8-13)
- **Itérations courtes** : une US codée et testée avant de passer à la suivante
- Branche Git par fonctionnalité, merge dans `develop` après tests, puis dans `main` pour livraison

### 1.2 Pourquoi pas Scrum ?

Le projet étant porté par un développeur unique, les cérémonies Scrum (sprint planning, daily, retro) n'apportent pas de valeur. Le **Kanban** offre la flexibilité nécessaire avec un suivi visuel simple.

## 2. Outil de gestion : Kanban

Le tableau est tenu dans un fichier `kanban.md` pour porter facilement vers Trello/Notion.

### 2.1 Colonnes
1. **Backlog** — toutes les fonctionnalités prévues, par ordre de priorité
2. **À faire** — fonctionnalités planifiées pour la semaine en cours
3. **En cours** — fonctionnalité actuellement développée (limite WIP : 1)
4. **Terminé sur develop** — testé et mergé sur `develop`
5. **Mergé sur main** — déployé en production

### 2.2 Règles de gestion
- **Limite WIP** : une seule US "En cours" à la fois
- Une US sur **develop** doit être testée localement avant de passer
- Une US sur **main** doit être validée par déploiement réussi sur Railway

## 3. Workflow Git

### 3.1 Stratégie de branches
```
main           ●────────●────────●────────● (production)
                \      /        /        /
develop          ●────●────────●────────● (intégration)
                  \  / \      / \      /
feature/us-1      ●●    \    /   \    /
feature/us-2          ●●●    /     \  /
feature/us-3                ●●●●    /
feature/us-4                       ●●●
```

### 3.2 Convention de nommage
- Branches : `feature/us-N-courte-description` (ex : `feature/us-3-recherche-covoiturages`)
- Commits : préfixés par `feat:`, `fix:`, `docs:`, `chore:`, `refactor:`, `test:`
- PR / merge : titre court + description détaillée des changements

### 3.3 Cycle de vie d'une fonctionnalité
1. `git checkout develop && git pull`
2. `git checkout -b feature/us-N-description`
3. Développer + tester en local
4. `git commit -m "feat: ..."`
5. `git push origin feature/us-N-description`
6. Merge dans `develop` (ou via Pull Request si revue)
7. Tests sur `develop`
8. Merge `develop` dans `main` après validation

## 4. Découpage des user stories

### 4.1 Priorisation MoSCoW

| Priorité | US | Description |
|----------|-----|-------------|
| MUST | 1, 2, 3, 5, 7 | Bases : voir, rechercher, créer un compte |
| MUST | 6, 9, 10 | Cœur métier : participer, proposer, gérer |
| SHOULD | 4, 8, 11 | Filtres, espace utilisateur, démarrage |
| SHOULD | 12, 13 | Modération + administration |
| COULD | (futur) | Messagerie, géoloc, paiement |

### 4.2 Estimation et suivi

| US | Difficulté | Heures estimées | Heures réelles |
|----|-----------|-----------------|----------------|
| 1  | ★         | 2 h            | 2 h            |
| 2  | ★         | 1 h            | 1 h            |
| 3  | ★★★       | 8 h            | 6 h            |
| 4  | ★★        | 4 h            | 3 h            |
| 5  | ★★        | 5 h            | 4 h            |
| 6  | ★★★       | 6 h            | 5 h            |
| 7  | ★★        | 3 h            | 3 h            |
| 8  | ★★★       | 7 h            | 6 h            |
| 9  | ★★        | 4 h            | 4 h            |
| 10 | ★★        | 4 h            | 3 h            |
| 11 | ★★★       | 6 h            | 5 h            |
| 12 | ★★        | 4 h            | 3 h            |
| 13 | ★★★       | 8 h            | 6 h            |
| **Total** | | **62 h** | **51 h** |

(Reste : maquettes, doc, déploiement = ~19h, total prévu 70h conforme à la consigne.)

## 5. Risques identifiés et mitigation

| Risque | Probabilité | Impact | Mitigation |
|--------|-------------|--------|------------|
| MongoDB non accessible en local | Haute | Moyen | Fallback gracieux dans les Models (try/catch) + Atlas pour la prod |
| Mots de passe non robustes | Moyenne | Élevé | Validation côté serveur + indication côté UI |
| Race conditions sur places dispo | Faible | Élevé | Transaction MySQL avec `SELECT ... FOR UPDATE` |
| Crédits négatifs | Faible | Élevé | Vérification atomique avant débit dans la transaction |
| XSS sur commentaires/avis | Moyenne | Élevé | Échappement systématique via `e()` |

## 6. Tests

### 6.1 Tests réalisés (manuels)

- ✅ Toutes les routes visiteur (HTTP 200)
- ✅ Login admin → redirection /admin
- ✅ Login utilisateur → /mon-espace
- ✅ Création de compte → 20 crédits offerts
- ✅ Recherche covoiturage avec filtres
- ✅ Page détail covoiturage (avec et sans MongoDB)
- ✅ Pages employé et admin (rôles requis)
- ✅ API JSON `/admin/api/stats` (graphiques)

### 6.2 Tests automatisés (à venir)
- PHPUnit pour les Models (couches d'accès aux données)
- Tests d'intégration des controllers

## 7. Livrables

| Livrable | Format | Statut |
|----------|--------|--------|
| Code source | Dépôt GitHub public | ✓ (à pousser) |
| Application déployée | URL Railway | À faire |
| README.md | Markdown | ✓ |
| Manuel d'utilisation | PDF | ✓ (en MD, à exporter) |
| Charte graphique | PDF | ✓ (en MD, à exporter) |
| Wireframes / Mockups | PDF | À faire (3 desktop + 3 mobile) |
| Documentation gestion projet | PDF | ✓ (ce fichier) |
| Documentation technique | PDF | ✓ |
| Scripts SQL | `.sql` | ✓ |
| Kanban | Trello/Notion + `kanban.md` | ✓ (MD), à porter |
