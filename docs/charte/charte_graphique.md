# Charte graphique — EcoRide

## 1. Concept

L'identité visuelle d'EcoRide s'inspire de l'écologie et du voyage. Les couleurs évoquent la nature (vert pour le végétal, bleu pour le ciel et l'eau, beige pour la terre). Le ton est moderne, accessible et rassurant.

## 2. Palette de couleurs

| Rôle | Nom | HEX | RGB | Usage |
|------|-----|-----|-----|-------|
| Primaire | Vert forêt | `#2E7D32` | 46, 125, 50 | Boutons principaux, liens actifs, header |
| Primaire clair | Vert tendre | `#66BB6A` | 102, 187, 106 | Hover, accents, badges écologiques |
| Secondaire | Bleu ciel | `#0288D1` | 2, 136, 209 | Liens, info, icônes |
| Accent | Ocre / Sable | `#F9A825` | 249, 168, 37 | CTA secondaires, étoiles de notation |
| Fond clair | Blanc cassé | `#F5F7F4` | 245, 247, 244 | Fond principal des pages |
| Fond carte | Blanc pur | `#FFFFFF` | 255, 255, 255 | Fond des cartes/encarts |
| Texte principal | Anthracite | `#212121` | 33, 33, 33 | Corps de texte |
| Texte secondaire | Gris moyen | `#616161` | 97, 97, 97 | Labels, métadonnées |
| Erreur | Rouge brique | `#C62828` | 198, 40, 40 | Messages d'erreur |
| Succès | Vert validé | `#388E3C` | 56, 142, 60 | Messages de succès |

## 3. Typographie

- **Titres (h1-h3)** : `Poppins`, sans-serif, font-weight 600
- **Sous-titres (h4-h6)** : `Poppins`, sans-serif, font-weight 500
- **Corps de texte** : `Inter`, sans-serif, font-weight 400, taille 16px, line-height 1.6
- **Boutons** : `Poppins`, sans-serif, font-weight 600, taille 14px, lettres en majuscules

Polices chargées via Google Fonts :

```html
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
```

## 4. Espacement et grilles

- Système de grille Bootstrap 5 (12 colonnes)
- Espacement de base : multiples de 8px (8, 16, 24, 32, 48, 64)
- Border-radius standard : 8px (cartes), 4px (champs de formulaire), 24px (boutons pilule)

## 5. Composants

### Boutons

- **Primaire** : fond `#2E7D32`, texte blanc, hover `#1B5E20`
- **Secondaire** : fond transparent, bordure `#2E7D32`, texte `#2E7D32`, hover fond `#E8F5E9`
- **Danger** : fond `#C62828`, texte blanc

### Cartes covoiturage

- Fond blanc, ombre légère `0 2px 8px rgba(0,0,0,0.08)`
- Border-radius 8px, padding 16px
- Badge "Écologique" : fond `#66BB6A`, texte blanc, icône feuille

### Formulaires

- Champs : bordure `#BDBDBD`, focus bordure `#2E7D32`, padding 12px 16px
- Labels : couleur `#616161`, taille 14px, font-weight 500

## 6. Iconographie

- Bibliothèque : **Bootstrap Icons** (cohérence avec Bootstrap 5)
- Icônes thématiques : feuille (écologique), voiture, étoile (notation), euro (prix), horloge (durée)

## 7. Logo

Logo textuel : "EcoRide" en `Poppins` 700, avec une icône feuille à gauche du nom.
- Couleur principale : `#2E7D32`
- Variante sur fond sombre : blanc avec icône `#66BB6A`

## 8. Accessibilité

- Contraste minimal AA (WCAG 2.1) sur tous les textes
- Taille de texte minimale : 14px
- Focus visible sur tous les éléments interactifs
- `alt` obligatoire sur toutes les images informatives
