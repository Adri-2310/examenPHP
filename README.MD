# 🍳 Marmiton-Exam - Application de Gestion de Recettes

**Version:** `1.0.0` - Phase: **Test Utilisateur** 🧪

---

## 📋 À propos du projet

**Marmiton-Exam** est une application web PHP permettant aux utilisateurs de :

- 👤 **S'authentifier** et gérer leur compte utilisateur
- 📝 **Créer, modifier et supprimer** ses propres recettes
- 🖼️ **Uploader des images** pour illustrer les recettes
- ❤️ **Sauvegarder des favoris** depuis l'API TheMealDB
- 👁️ **Consulter les détails** des recettes (locales et API)
- 🔍 **Rechercher** des recettes d'inspiration via l'API TheMealDB
- 🎨 **Changer de thème** (clair/sombre)

---

## 🎯 Objectifs de la Version 1.0

### ✅ Fonctionnalités Implémentées

#### Authentification & Utilisateurs
- ✅ Inscription avec validation email
- ✅ Connexion avec hashing sécurisé (password_hash)
- ✅ Déconnexion
- ✅ Rate limiting sur les tentatives de connexion
- ✅ Protection CSRF sur tous les formulaires

#### Gestion des Recettes Locales
- ✅ Créer une recette (titre, description, ingrédients, instructions, image)
- ✅ Lire/Consulter une recette
- ✅ Modifier une recette (propriétaire uniquement)
- ✅ Supprimer une recette (propriétaire uniquement)
- ✅ Upload d'images (validation MIME + taille)
- ✅ Gestion dynamique des ingrédients (JSON)
- ✅ Affichage responsive (desktop/mobile)

#### Favoris (API TheMealDB)
- ✅ Ajouter une recette API aux favoris
- ✅ Retirer un favori (AJAX)
- ✅ Consulter la liste des favoris
- ✅ **[NOUVEAU v1.0]** Afficher les détails d'une recette API dans l'app (au lieu de redirection externe)
- ✅ Cache en session (30 minutes)
- ✅ Gestion gracieuse des erreurs API

#### Interface de Recherche
- ✅ Recherche de recettes via TheMealDB
- ✅ Affichage dynamique (JavaScript)
- ✅ Ajout direct aux favoris depuis les résultats
- ✅ Section "Idées du jour" (recettes aléatoires)
- ✅ **[NOUVEAU v1.0]** Max 6 recettes aléatoires sur l'accueil

#### Design & UX
- ✅ Design responsive (Bootstrap 5)
- ✅ Navigation fluide
- ✅ Feedback utilisateur (toasts notifications)
- ✅ Thème sombre/clair (localStorage)
- ✅ Animations et transitions
- ✅ Icônes intégrées
- ✅ Menu burger pour mobile

---

## 🏗️ Architecture du Projet

### Structure des Dossiers

```
examenPHP/
├── public/                          # Point d'entrée + assets
│   ├── index.php                   # Front controller
│   ├── css/
│   │   ├── style.css              # Styles personnalisés
│   │   └── bootstrap.min.css       # Bootstrap
│   ├── js/
│   │   ├── main.js                # Point d'entrée JS
│   │   ├── modules/               # Modules réutilisables
│   │   │   ├── FavoriteToggler.js
│   │   │   ├── FormValidator.js
│   │   │   ├── IngredientManager.js
│   │   │   └── PasswordToggler.js
│   │   └── classes/
│   │       └── ThemeToggle.js
│   └── uploads/                   # Images uploadées

├── src/
│   ├── controllers/               # Logique métier
│   │   ├── MainController.php
│   │   ├── RecipesController.php
│   │   ├── FavoritesController.php
│   │   ├── ApiController.php
│   │   ├── UsersController.php
│   │   └── ContactController.php
│   ├── models/                    # Accès aux données
│   │   ├── RecipesModel.php
│   │   ├── FavoritesModel.php
│   │   └── UsersModel.php
│   └── core/                      # Framework MVC
│       ├── Main.php              # Routeur
│       ├── Controller.php        # Classe de base
│       ├── Model.php            # Classe de base
│       └── Db.php               # Connexion PDO

├── views/                         # Templates HTML
│   ├── base.php                  # Layout principal
│   ├── components/
│   │   ├── header.php
│   │   └── footer.php
│   ├── main/
│   │   └── index.php            # Accueil
│   ├── recipes/
│   │   ├── index.php            # Liste recettes locales
│   │   ├── lire.php             # Détail recette locale
│   │   ├── ajouter.php          # Ajout recette
│   │   └── edit.php             # Modification recette
│   ├── favorites/
│   │   └── index.php            # Liste favoris API
│   ├── api/
│   │   ├── index.php            # Recherche API
│   │   └── lire.php             # Détail recette API [NOUVEAU v1.0]
│   └── users/
│       ├── login.php
│       ├── register.php
│       └── profile.php

├── database/
│   └── examenPhp.sql            # Structure BDD

├── Autoloader.php                # PSR-4 autoloader
├── README.md                     # Ce fichier
└── RAPPORT_SOLUTION_API_LIRE.md  # Documentation technique
```

---

## 🗄️ Base de Données

### Tables Principales

#### `users`
- `id` (PK) - Identifiant unique
- `email` - Email de l'utilisateur
- `password` - Hash du mot de passe (bcrypt)
- `username` - Pseudonyme
- `created_at` - Date création

#### `recipes` (Recettes Locales)
- `id` (PK)
- `user_id` (FK → users)
- `title` - Titre
- `description` - Description
- `ingredients` - JSON (format: `[{"name":"", "qty":""}]`)
- `instructions` - Instructions
- `image_url` - Chemin image
- `created_at`, `updated_at`

#### `favorites` (Recettes API)
- `id` (PK)
- `user_id` (FK → users)
- `id_api` - ID TheMealDB
- `titre` - Titre recette
- `image_url` - URL image
- `note` - Notes personnelles (optionnel)
- `created_at`

---

## 🚀 Installation & Configuration

### Prérequis
- PHP 7.4+
- MySQL 5.7+
- Composer (optionnel)
- Navigateur moderne (Chrome, Firefox, Safari, Edge)

### Étapes d'installation

1. **Cloner le projet**
   ```bash
   git clone <repo-url> examenPHP
   cd examenPHP
   ```

2. **Créer la base de données**
   ```bash
   mysql -u root -p < database/examenPhp.sql
   ```

3. **Configurer la connexion BD**
   - Éditer `src/core/Db.php`
   - Adapter les identifiants MySQL

4. **Lancer le serveur local**
   ```bash
   php -S localhost:8000
   # Accès: http://localhost:8000/public
   ```

5. **Tester**
   - S'inscrire: http://localhost:8000/public/?url=users/register
   - Se connecter: http://localhost:8000/public/?url=users/login
   - Accueil: http://localhost:8000/public/?url=main

---

## 🔐 Sécurité

### Mesures Implémentées

| Risque | Prévention |
|--------|-----------|
| **Injection SQL** | Requêtes préparées (PDO) |
| **XSS** | `htmlspecialchars()`, `strip_tags()` |
| **CSRF** | Token CSRF validé sur tous les POST |
| **Brute Force** | Rate limiting (3 tentatives/5 min) |
| **Mots de passe** | Hashing bcrypt (`password_hash`) |
| **Upload fichiers** | Validation MIME + extension |
| **Session** | Secure flags, HttpOnly, SameSite |

### Headers de Sécurité
- `X-Frame-Options: DENY`
- `X-Content-Type-Options: nosniff`
- `Strict-Transport-Security`
- `Content-Security-Policy`

---

## 📱 Fonctionnalités Clés v1.0

### [NOUVEAU] Affichage des Recettes API

**Avant v1.0:** Les favoris redirigent vers `https://www.themealdb.com/meal/{id}`

**Depuis v1.0:** Les favoris s'affichent dans l'application avec tous les détails:
- 📖 Page dédiée: `/api/lireRecette/{id_api}`
- 🖼️ Image de la recette (centrée et responsive)
- 📋 Liste complète des ingrédients
- 👨‍🍳 Instructions de préparation
- 🏷️ Catégorie et région
- 🔗 Liens vers source + TheMealDB
- ⚡ Cache en session (30 min)
- 🛡️ Gestion d'erreurs robuste

**Fichiers modifiés:**
- `src/controllers/ApiController.php` - Nouvelle méthode `lireRecette()`
- `views/api/lire.php` - Nouvelle vue (créée)
- `views/favorites/index.php` - Lien interne (au lieu de themealdb.com)

### [AMÉLIORÉ] Accueil

**Amélioration:**
- Section "Mes Coups de Cœur": Max 6 recettes aléatoires
- Ordre différent à chaque rechargement
- Meilleure lisibilité

---

## 🧪 Phase Test Utilisateur

### Statut Actuel
- ✅ **Développement:** Terminé
- 🧪 **Tests:** EN COURS (Phase 1)
- 📋 **Feedback utilisateurs:** À collecter
- 🔄 **Optimisations:** À faire selon retours

### Checklist Tests (Réalisée)

- [x] Navigation de base
- [x] Affichage images/ingrédients/instructions
- [x] Badges catégorie/région
- [x] Bouton retour
- [x] Cache (performance)
- [x] Métadonnées et liens
- [x] Responsive mobile
- [x] Gestion erreurs API
- [x] Scénario utilisateur complet

### Prochaines Étapes (v1.1)

- [ ] Retours des utilisateurs
- [ ] Optimisations selon feedback
- [ ] Documentation utilisateur
- [ ] Amélioration commentaires JavaScript (JSDoc)
- [ ] Tests de charge
- [ ] SEO (sitemap, meta tags)

---

## 📊 Documentation Technique

Détails techniques complets disponibles dans:
- **`RAPPORT_SOLUTION_API_LIRE.md`** - Architecture v1.0 (affichage recettes API)

### État des Commentaires du Code

| Type | Status | Détail |
|------|--------|--------|
| PHP Controllers | ✅ 100% | Excellents, français |
| PHP Models | ✅ 100% | Excellents, français |
| PHP Vues | ✅ 100% | Bien documentées |
| JavaScript | ⚠️ 71% | À améliorer (JSDoc) |
| **TOTAL** | **71%** | Couverture globale |

---

## 🛠️ Technologies Utilisées

### Backend
- **PHP** 7.4+ - Langage serveur
- **MySQL** - Base de données
- **PDO** - ORM minimaliste
- **PSR-4** - Autoloading

### Frontend
- **HTML5** - Structure
- **CSS3** - Styles (personnalisés + Bootstrap 5)
- **JavaScript (ES6+)** - Interactivité
- **Bootstrap 5** - Framework CSS
- **Toastify** - Notifications

### APIs Externes
- **TheMealDB** - Source de recettes (https://www.themealdb.com/api.php)

### Outils
- **Git** - Versioning
- **Composer** (optionnel)

---

## 📈 Statistiques Projet

- **Fichiers PHP:** 14+ (Controllers, Models, Views)
- **Fichiers JavaScript:** 4+ modules
- **Templates Vue:** 12+
- **Lignes de code:** ~3500+
- **Couverture commentaires:** 71% du code
- **Base de données:** 3 tables + relations

---

## 📞 Support & Contact

Pour des questions ou retours:
- 📧 Email: [À définir]
- 💬 Issues: [Créer une issue]
- 🐛 Bug reports: [Procédure à définir]

---

## 📄 Licence

À définir (MIT, GPL, etc.)

---

## 🎓 Crédits

- **Développeur:** Warse
- **Date projet:** 2026
- **Version:** 1.0.0

---

## ✅ CHANGELOG v1.0.0

### Features
- ✨ Affichage complet des recettes API (au lieu de redirection)
- ✨ Cache en session 30 minutes
- ✨ Récupération dynamique des ingrédients de l'API
- ✨ Gestion d'erreurs robuste (API down, timeout, JSON invalide)
- ✨ Max 6 recettes aléatoires sur l'accueil

### Fixes
- 🐛 Lien "Voir la recette" depuis favoris (interne au lieu d'externe)
- 🐛 Suppression inutile de youtube_url du contrôleur

### Améliorations
- ⚡ Performance améliorée avec cache session
- 🎨 Layout optimisé pour les recettes API
- 📱 Responsive mobile amélioré
- 🛡️ Sécurité XSS sur toutes les données API

---

**Merci d'utiliser Marmiton-Exam! 🍳**

*Status: En test utilisateur - Version stable 1.0.0*
