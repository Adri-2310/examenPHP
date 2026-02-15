# 📋 Checklist - Améliorations UI/UX du Site de Recettes

## 🎨 Organisation CSS

### ✅ Structure actuelle
- [x] Fichier `style.css` initialisé (vide actuellement)
- [x] CSS inline dans `base.php` (thème dark)
- [x] Styles inline dans plusieurs vues PHP

### 📁 Nouvelle Architecture CSS Proposée
```
public/css/
├── style.css           # Import principal de tous les styles
├── theme-light.css     # Variables et styles pour le thème clair
├── theme-dark.css      # Variables et styles pour le thème sombre
├── components.css      # Composants réutilisables (cards, buttons, forms)
├── layout.css          # Structure générale (header, footer, grid)
└── utilities.css       # Classes utilitaires personnalisées
```

---

## 🎯 Checklist d'Améliorations par Catégorie

### 1️⃣ **Structure et Organisation**
- [ ] Extraire tout le CSS inline de `base.php` vers des fichiers CSS
- [ ] Extraire les styles inline des vues vers `components.css`
- [ ] Créer `theme-light.css` avec les variables CSS pour le thème clair
- [ ] Créer `theme-dark.css` avec les variables CSS pour le thème sombre
- [ ] Centraliser tous les imports CSS dans `style.css`
- [ ] Supprimer les balises `<style>` inline du HTML

### 2️⃣ **Thèmes et Couleurs**
- [ ] Définir une palette de couleurs cohérente
  - [ ] Couleur primaire (exemple: bleu/vert pour cuisine)
  - [ ] Couleur secondaire (complémentaire)
  - [ ] Couleur d'accent (pour favoris, actions importantes)
  - [ ] Couleurs de statut (succès/danger/warning/info)
- [ ] Harmoniser les couleurs du thème dark actuel
  - [ ] Améliorer le contraste pour l'accessibilité
  - [ ] Adoucir le noir pur (#1a1a1a) si nécessaire
- [ ] Créer des variables CSS pour toutes les couleurs
- [ ] Ajouter une transition fluide lors du changement de thème

### 3️⃣ **Typographie**
- [ ] Choisir une police Google Fonts moderne
  - [ ] Police principale pour le texte (ex: Inter, Roboto, Open Sans)
  - [ ] Police secondaire pour les titres (ex: Playfair Display, Poppins)
- [ ] Définir une échelle typographique cohérente (h1-h6, p, small)
- [ ] Améliorer la lisibilité des textes longs (line-height, letter-spacing)
- [ ] Ajouter des poids de police variés (light, regular, medium, bold)
- [ ] Augmenter la taille des titres principaux
- [ ] Harmoniser les espacements autour des textes

### 4️⃣ **Navigation**
- [ ] Améliorer le style de la navbar
  - [ ] Ajouter un effet hover sur les liens
  - [ ] Créer un effet d'indicateur pour la page active
  - [ ] Améliorer l'espacement entre les items
- [ ] Rendre la navbar sticky (collée en haut lors du scroll)
- [ ] Ajouter une ombre ou bordure subtile à la navbar
- [ ] Améliorer le bouton de toggle du thème
  - [ ] Animation lors du changement d'icône
  - [ ] Meilleur style visuel (arrondi, couleur)
- [ ] Créer une version responsive pour mobile
  - [ ] Menu hamburger fonctionnel
  - [ ] Navigation adaptée aux petits écrans

### 5️⃣ **Cards de Recettes**
- [ ] Uniformiser la hauteur des images (déjà fait: 180px/200px)
- [ ] Ajouter une ombre portée subtile sur les cards
- [ ] Créer un effet hover sur les cards
  - [ ] Légère élévation (transform: translateY)
  - [ ] Augmentation de l'ombre
  - [ ] Transition fluide
- [ ] Améliorer le style des badges de catégories
- [ ] Ajouter un overlay gradient sur les images
- [ ] Améliorer l'espacement interne des cards
- [ ] Ajouter des coins arrondis plus prononcés
- [ ] Créer un effet de chargement (skeleton) pendant le fetch API

### 6️⃣ **Formulaires**
- [ ] Styliser les inputs
  - [ ] Borders plus subtiles
  - [ ] Focus state distinctif
  - [ ] Padding harmonieux
- [ ] Améliorer les messages d'erreur
  - [ ] Extraire les styles inline vers CSS
  - [ ] Animation d'apparition
  - [ ] Icône d'erreur
- [ ] Styliser les boutons
  - [ ] Ajouter des effets hover
  - [ ] États disabled clairs
  - [ ] Transitions fluides
- [ ] Ajouter des labels avec icônes
- [ ] Créer un style pour les champs valides (vert)
- [ ] Améliorer le style toggle du mot de passe

### 7️⃣ **Page de Détails (lire.php)**
- [ ] Améliorer la présentation de l'image principale
  - [ ] Coins arrondis
  - [ ] Ombre portée
  - [ ] Responsive
- [ ] Styliser la section des ingrédients
  - [ ] Liste avec puces personnalisées (✓ ou •)
  - [ ] Espacement harmonieux
  - [ ] Background subtle
- [ ] Améliorer la section instructions
  - [ ] Extraire le style inline `white-space: pre-wrap`
  - [ ] Numérotation des étapes si possible
  - [ ] Espacement entre les paragraphes
- [ ] Ajouter des séparateurs visuels entre les sections
- [ ] Améliorer le bouton favori
  - [ ] Animation lors du clic
  - [ ] Couleur distincte quand actif

### 8️⃣ **Boutons et Actions**
- [ ] Créer des variantes de boutons cohérentes
  - [ ] Primaire, secondaire, danger, succès
  - [ ] Tailles (small, medium, large)
- [ ] Ajouter des états hover/active/focus
- [ ] Ajouter des icônes aux boutons importants
- [ ] Créer des boutons avec loading state
- [ ] Harmoniser les couleurs avec Bootstrap

### 9️⃣ **Footer**
- [ ] Améliorer le design du footer
  - [ ] Ajouter des liens utiles (Mentions légales, À propos, etc.)
  - [ ] Ajouter des icônes de réseaux sociaux
  - [ ] Meilleure séparation avec le contenu
- [ ] Adapter aux deux thèmes (light/dark)
- [ ] Ajouter un espacement supérieur conséquent

### 🔟 **Animations et Transitions**
- [ ] Ajouter des animations d'apparition (fade-in)
  - [ ] Pour les cards au chargement
  - [ ] Pour les modales
  - [ ] Pour les notifications
- [ ] Créer des transitions fluides
  - [ ] Changement de thème (0.3s ease)
  - [ ] Hover sur les éléments interactifs
  - [ ] Ouverture/fermeture des menus
- [ ] Ajouter un effet de loading pour les requêtes API
- [ ] Animation sur le bouton favori (cœur qui pulse)

### 1️⃣1️⃣ **Responsive Design**
- [ ] Tester et ajuster pour mobile (< 768px)
  - [ ] Grid de recettes (1 colonne)
  - [ ] Navbar responsive
  - [ ] Formulaires adaptés
- [ ] Tester et ajuster pour tablette (768px - 1024px)
  - [ ] Grid de recettes (2 colonnes)
  - [ ] Espacements ajustés
- [ ] Tester sur grand écran (> 1200px)
  - [ ] Largeur maximale du contenu
  - [ ] Grid de recettes (3-4 colonnes)

### 1️⃣2️⃣ **Accessibilité**
- [ ] Vérifier le contraste des couleurs (WCAG AA)
- [ ] Ajouter des focus visibles pour la navigation au clavier
- [ ] Tester avec un lecteur d'écran
- [ ] Ajouter des attributs ARIA appropriés
- [ ] Assurer une taille de texte lisible (min 16px)

### 1️⃣3️⃣ **Micro-interactions**
- [ ] Ajouter un feedback visuel sur tous les clics
- [ ] Animation sur les notifications Toast
- [ ] Effet ripple sur les boutons
- [ ] Loader pendant les appels API
- [ ] Animation des favoris (cœur qui se remplit)

### 1️⃣4️⃣ **Images et Médias**
- [ ] Optimiser les images (compression)
- [ ] Ajouter des images placeholder cohérentes
- [ ] Créer un système de lazy loading
- [ ] Ajouter des filtres CSS sur les images au hover
- [ ] Gérer les images manquantes gracieusement

### 1️⃣5️⃣ **Espacement et Layout**
- [ ] Définir un système d'espacement cohérent (4px, 8px, 16px, 24px, 32px)
- [ ] Utiliser les variables CSS pour les espacements
- [ ] Améliorer les marges entre les sections
- [ ] Créer une grille harmonieuse
- [ ] Ajouter du "breathing room" (white space)

---

## 🚀 Plan d'Action Prioritaire

### Phase 1 : Foundation (À faire en premier)
1. Créer `theme-light.css` et `theme-dark.css`
2. Extraire tout le CSS inline vers les fichiers appropriés
3. Définir les variables CSS (couleurs, espacements, polices)
4. Importer Google Fonts

### Phase 2 : Composants (Ensuite)
5. Styliser les cards de recettes
6. Améliorer les formulaires
7. Refaire le style de la navigation
8. Améliorer les boutons

### Phase 3 : Polish (Finition)
9. Ajouter les animations et transitions
10. Tester et ajuster le responsive
11. Vérifier l'accessibilité
12. Micro-interactions

### Phase 4 : Optimisation
13. Optimiser les images
14. Tester les performances
15. Derniers ajustements visuels

---

## 📊 Métriques de Succès

- [ ] Temps de chargement < 2 secondes
- [ ] Score Lighthouse Performance > 90
- [ ] Score Lighthouse Accessibility > 90
- [ ] Design cohérent sur tous les navigateurs
- [ ] 100% responsive (mobile, tablette, desktop)
- [ ] Tous les styles CSS externes (0 style inline)

---

## 💡 Inspirations et Références

### Sites de Recettes Modernes
- **Marmiton** : Navigation intuitive
- **750g** : Cards de recettes élégantes
- **Tasty** : Animations et micro-interactions
- **AllRecipes** : Organisation claire

### Palettes de Couleurs Suggérées
- **Warm Cooking** : #FF6B35 (orange), #F7931E (jaune), #004E89 (bleu)
- **Fresh & Green** : #2D6A4F (vert), #52B788 (vert clair), #D8F3DC (mint)
- **Classic Elegant** : #2C3E50 (bleu foncé), #E74C3C (rouge), #ECF0F1 (gris clair)

---

## 📝 Notes

- Bootstrap 5.3.0 est déjà intégré : utiliser ses utilities
- Toastify-js est configuré : bonne base pour les notifications
- Le système de thème dark/light existe : l'améliorer
- Privilégier les variables CSS pour faciliter la maintenance

---

**Date de création** : 15 février 2026  
**Dernière mise à jour** : 15 février 2026  
**Statut** : 🟡 En cours
