# Schéma MongoDB — EcoRide

MongoDB stocke les données **non critiques** ou à **schéma flexible** : avis, préférences, configuration et logs. Les données transactionnelles critiques (utilisateurs, covoiturages, crédits) restent en MySQL.

## Collections

### `avis`

Avis et notes laissés par les passagers sur les chauffeurs après un trajet. La modération étant asynchrone (US 11 et 12), un statut `en_attente` permet le passage en file d'attente avant validation par un employé.

```js
{
  _id: ObjectId,
  auteur_id: Number,        // référence utilisateur SQL
  auteur_pseudo: String,    // dénormalisation pour éviter une jointure
  chauffeur_id: Number,
  covoiturage_id: Number,
  note: Number,             // 1 à 5
  commentaire: String,
  statut: String,           // "en_attente" | "valide" | "refuse"
  date_creation: ISODate,
  date_validation: ISODate, // optionnel
  validateur_id: Number     // optionnel : id employé qui a modéré
}
```

Index : `{ chauffeur_id: 1, statut: 1 }`, `{ date_creation: -1 }`

### `preferences`

Préférences libres déclarées par les chauffeurs. Le format clé/valeur permet à chaque chauffeur d'ajouter ses propres préférences sans modifier le schéma.

```js
{
  _id: ObjectId,
  utilisateur_id: Number,
  preferences: {
    fumeur: "non",
    animaux: "oui",
    musique: "oui",
    discussion: "modérée",
    pause_pipi: "toutes les 2h"  // exemple de préférence personnalisée
  },
  maj: ISODate
}
```

Index : `{ utilisateur_id: 1 }`

### `configuration`

Paramètres globaux de l'application. Lus à chaque requête, écrits rarement.

```js
{
  _id: ObjectId,
  cle: String,           // unique
  valeur: Mixed,         // peut être number, string, boolean, object
  description: String
}
```

Index : `{ cle: 1 }` unique

### `logs`

Journal d'événements applicatifs (connexions, actions sensibles, erreurs).

```js
{
  _id: ObjectId,
  date: ISODate,
  niveau: String,        // "info" | "warning" | "error"
  message: String,
  context: Object        // contexte arbitraire
}
```

Index : `{ date: -1 }`, `{ niveau: 1 }`

## Justification du choix MongoDB

| Critère | Argument |
|---------|----------|
| **Flexibilité du schéma** | Les préférences chauffeurs sont **extensibles à l'infini** par l'utilisateur — un schéma SQL fixe serait inadapté |
| **Performance lecture** | Les avis sont massivement lus (page détail covoiturage), peu écrits — MongoDB est optimisé pour ce profil |
| **Pas de jointure forte** | Les avis et préférences sont autonomes : pas de besoin de transactions ACID multi-tables |
| **Audit non bloquant** | Les logs s'écrivent en fire-and-forget, sans impact sur la transaction métier |

## Ce qu'on garde en MySQL et pourquoi

| Donnée | Pourquoi MySQL |
|--------|----------------|
| Utilisateurs, rôles | Intégrité référentielle critique |
| Covoiturages, participations | Transactions ACID nécessaires (transfert de crédits + décrément place) |
| Voitures, marques | Référentiel partagé, contraintes FK |
| Crédits plateforme | Trace financière — comptabilité auditable |
