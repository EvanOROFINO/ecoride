# Maquettes — EcoRide

Maquettes **haute fidélité** générées automatiquement à partir de l'application déployée
via Puppeteer (script `tools/screenshots.cjs`). Toutes les pages sont capturées en deux
formats : **desktop (1440×900)** et **mobile (414×896)**.

> Sources visuelles : captures de l'application réelle en production (sur AlwaysData)
> ou en local. Disponibles aussi sous forme de wireframes Figma équivalents (lien à venir).

## 1. Page d'accueil (US 1)

### Desktop
![Accueil — desktop](img/01-accueil-desktop.png)

### Mobile
![Accueil — mobile](img/01-accueil-mobile.png)

---

## 2. Recherche de covoiturages avec filtres (US 3, 4)

### Desktop
![Recherche covoiturages — desktop](img/02-recherche-covoiturages-desktop.png)

### Mobile
![Recherche covoiturages — mobile](img/02-recherche-covoiturages-mobile.png)

---

## 3. Détail d'un covoiturage (US 5, 6)

### Desktop
![Détail covoiturage — desktop](img/03-detail-covoiturage-desktop.png)

### Mobile
![Détail covoiturage — mobile](img/03-detail-covoiturage-mobile.png)

---

## 4. Page de connexion

### Desktop
![Connexion — desktop](img/04-connexion-desktop.png)

### Mobile
![Connexion — mobile](img/04-connexion-mobile.png)

---

## 5. Création de compte (US 7)

### Desktop
![Inscription — desktop](img/05-inscription-desktop.png)

### Mobile
![Inscription — mobile](img/05-inscription-mobile.png)

---

## 6. Tableau de bord administrateur (US 13)

### Desktop
![Admin dashboard — desktop](img/06-admin-dashboard-desktop.png)

### Mobile
![Admin dashboard — mobile](img/06-admin-dashboard-mobile.png)

---

## Reproduire ces captures

```bash
# Démarrer le serveur PHP local
php -S localhost:8002 -t public

# Dans un autre terminal
node tools/screenshots.cjs http://localhost:8002
```

Les captures sont régénérées dans `docs/maquettes/img/`.
