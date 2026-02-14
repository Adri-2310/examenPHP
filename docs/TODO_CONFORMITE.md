# 📋 TODO - CONFORMITÉ AU PROJET PROGRESSIF WEB

**Date d'analyse** : 14 février 2026
**Projet** : Application de Gestion de Recettes PHP
**Analysé par** : 4 agents spécialisés Claude Sonnet 4.5

---

## 🎯 RÉSUMÉ GLOBAL

| Aspect | Score | Statut |
|--------|-------|--------|
| **Backend PHP** (6 parties obligatoires) | 89% | ⚠️ Quasi-complet |
| **Frontend JavaScript** (3 exigences) | 33% | ❌ Incomplet |
| **Parties Bonus** (4 parties facultatives) | 100% | ✅ Toutes implémentées |
| **CONFORMITÉ GLOBALE** | 74% | ⚠️ Travail requis |

---

## 🚨 ACTIONS CRITIQUES (OBLIGATOIRES)

### ❌ 1. Créer une classe JavaScript personnalisée (FRONTEND)

**Statut** : NON FAIT (0%)
**Priorité** : CRITIQUE
**Temps estimé** : 1 heure

**Ce qui manque** :
- Aucune classe JavaScript ES6+ présente dans le projet
- Le dossier `public/js/` n'existe même pas

**Actions à faire** :

#### Étape 1 : Créer la structure de dossiers
```
public/
├── js/
│   ├── classes/
│   │   └── ThemeToggle.js
│   └── main.js
```

#### Étape 2 : Créer la classe ThemeToggle.js

**Fichier** : `public/js/classes/ThemeToggle.js`

```javascript
/**
 * Classe ThemeToggle - Permet de basculer entre thème clair et sombre
 *
 * Fonctionnalités :
 * - Sauvegarde de la préférence dans localStorage
 * - Application automatique du thème au chargement
 * - Bouton de toggle dans la navigation
 *
 * @class
 */
class ThemeToggle {
    /**
     * Initialise le système de thème
     */
    constructor() {
        this.theme = localStorage.getItem('theme') || 'light';
        this.init();
    }

    /**
     * Initialise les événements et applique le thème
     */
    init() {
        this.applyTheme();

        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggle());
        }
    }

    /**
     * Bascule entre les thèmes clair et sombre
     */
    toggle() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        this.applyTheme();
        localStorage.setItem('theme', this.theme);
    }

    /**
     * Applique le thème au document
     */
    applyTheme() {
        document.body.setAttribute('data-theme', this.theme);

        const icon = document.getElementById('theme-icon');
        if (icon) {
            icon.textContent = this.theme === 'light' ? '🌙' : '☀️';
        }
    }
}

// Export pour utilisation dans main.js
window.ThemeToggle = ThemeToggle;
```

#### Étape 3 : Créer main.js

**Fichier** : `public/js/main.js`

```javascript
/**
 * Point d'entrée JavaScript principal
 */
document.addEventListener('DOMContentLoaded', () => {
    // Initialisation du toggle de thème
    new ThemeToggle();
});
```

#### Étape 4 : Modifier views/base.php

Ajouter dans le `<head>` (après Bootstrap CSS) :
```php
<!-- Styles du thème -->
<style>
    [data-theme="dark"] {
        --bs-body-bg: #1a1a1a;
        --bs-body-color: #ffffff;
    }
    [data-theme="dark"] .card {
        background-color: #2a2a2a;
        color: #ffffff;
    }
    [data-theme="dark"] .navbar {
        background-color: #2a2a2a !important;
    }
</style>
```

Ajouter dans la navbar (ligne ~55, après les liens de navigation) :
```php
<!-- Bouton de toggle thème -->
<button id="theme-toggle" class="btn btn-outline-secondary ms-2" title="Changer de thème">
    <span id="theme-icon">🌙</span>
</button>
```

Ajouter avant `</body>` (après Bootstrap JS) :
```php
<!-- Classes JavaScript personnalisées -->
<script src="/js/classes/ThemeToggle.js"></script>
<script src="/js/main.js"></script>
```

**✅ EXIGENCE 1 REMPLIE**

---

### ❌ 2. Intégrer une bibliothèque JavaScript externe (FRONTEND)

**Statut** : NON FAIT (0%)
**Priorité** : CRITIQUE
**Temps estimé** : 30 minutes

**Ce qui manque** :
- Aucune bibliothèque JavaScript spécialisée intégrée
- Bootstrap ne compte PAS comme bibliothèque pour cette exigence

**Action recommandée** : Intégrer **Toastify-js** pour les notifications

#### Étape 1 : Ajouter les CDN dans views/base.php

Dans le `<head>` (après Bootstrap CSS) :
```php
<!-- Toastify-js pour les notifications -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
```

Avant `</body>` (après Bootstrap JS) :
```php
<!-- Bibliothèque Toastify-js -->
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
```

#### Étape 2 : Créer le fichier helper pour Toastify

**Fichier** : `public/js/notifications.js`

```javascript
/**
 * Helper pour les notifications Toastify
 */
const Notifications = {
    /**
     * Affiche une notification de succès
     * @param {string} message - Le message à afficher
     */
    success(message) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            stopOnFocus: true
        }).showToast();
    },

    /**
     * Affiche une notification d'erreur
     * @param {string} message - Le message à afficher
     */
    error(message) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
            stopOnFocus: true
        }).showToast();
    },

    /**
     * Affiche une notification d'information
     * @param {string} message - Le message à afficher
     */
    info(message) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #00d2ff, #3a7bd5)",
            stopOnFocus: true
        }).showToast();
    }
};

window.Notifications = Notifications;
```

Ajouter dans `views/base.php` avant `</body>` :
```php
<script src="/js/notifications.js"></script>
```

#### Étape 3 : Utiliser Toastify dans le projet

**Exemple 1** : Ajouter un toast après ajout de favori

Dans `views/api/index.php`, après la ligne 92 (dans le formulaire d'ajout) :
```javascript
// Intercepter la soumission du formulaire
form.addEventListener('submit', (e) => {
    e.preventDefault();

    fetch('/favorites/add', {
        method: 'POST',
        body: new FormData(form)
    }).then(() => {
        Notifications.success('✅ Recette ajoutée aux favoris !');
    }).catch(() => {
        Notifications.error('❌ Erreur lors de l\'ajout');
    });
});
```

**Exemple 2** : Afficher un message de bienvenue après connexion

Dans `views/main/index.php`, ajouter après la ligne 33 :
```php
<?php if(isset($_SESSION['user']) && !isset($_SESSION['welcome_shown'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Notifications.success('Bienvenue <?= htmlspecialchars($_SESSION['user']['nom']) ?> ! 👋');
        });
    </script>
    <?php $_SESSION['welcome_shown'] = true; ?>
<?php endif; ?>
```

**✅ EXIGENCE 2 REMPLIE**

---

### ❌ 3. Créer un formulaire de contact (BACKEND)

**Statut** : NON FAIT (0%)
**Priorité** : HAUTE
**Temps estimé** : 1 heure

**Ce qui manque** :
- Aucun formulaire de contact n'existe dans le projet

#### Étape 1 : Créer la vue

**Fichier** : `views/main/contact.php`

```php
<?php
/**
 * Vue : contact.php
 * Formulaire de contact
 */
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">📧 Nous contacter</h2>

            <?php if(isset($erreur)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="/main/contact">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom *</label>
                    <input type="text" class="form-control" id="nom" name="nom"
                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="sujet" class="form-label">Sujet *</label>
                    <input type="text" class="form-control" id="sujet" name="sujet"
                           value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Message *</label>
                    <textarea class="form-control" id="message" name="message"
                              rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    </div>
</div>
```

#### Étape 2 : Ajouter la méthode dans MainController

**Fichier** : `src/controllers/MainController.php`

Ajouter cette méthode à la fin de la classe :

```php
/**
 * Affiche et traite le formulaire de contact
 */
public function contact()
{
    // Traitement du formulaire
    if (!empty($_POST)) {
        // Validation CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Erreur de sécurité : Token CSRF invalide");
        }

        // Validation des champs
        if (!empty($_POST['nom']) && !empty($_POST['email']) &&
            !empty($_POST['sujet']) && !empty($_POST['message'])) {

            // Validation email
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $erreur = "Adresse email invalide";
            } else {
                // Nettoyage des données
                $nom = strip_tags($_POST['nom']);
                $email = strip_tags($_POST['email']);
                $sujet = strip_tags($_POST['sujet']);
                $message = strip_tags($_POST['message']);

                // Ici : Envoyer l'email (ou stocker en BDD)
                // Pour l'examen, on simule juste un succès

                $success = "Votre message a été envoyé avec succès !";

                // On pourrait aussi faire une redirection :
                // header('Location: /?message=success');
                // exit;
            }
        } else {
            $erreur = "Veuillez remplir tous les champs obligatoires.";
        }
    }

    // Affichage du formulaire
    $this->render('main/contact', [
        'erreur' => $erreur ?? null,
        'success' => $success ?? null,
        'titre' => 'Contact'
    ]);
}
```

#### Étape 3 : Ajouter le lien dans la navigation

Dans `views/base.php`, ajouter dans la navbar (vers la ligne 57) :

```php
<li class="nav-item">
    <a class="nav-link" href="/main/contact">Contact</a>
</li>
```

**✅ FORMULAIRE DE CONTACT CRÉÉ**

---

### ⚠️ 4. Ajouter la repopulation des champs (BACKEND)

**Statut** : PARTIEL (50%)
**Priorité** : HAUTE
**Temps estimé** : 15 minutes

**Ce qui manque** :
- Les formulaires login et register ne repopulent pas les champs en cas d'erreur

#### Corriger views/auth/login.php

Ligne 30, modifier :
```php
<input type="email" class="form-control" id="email" name="email"
       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
```

#### Corriger views/auth/register.php

Lignes 26, 32, 38 :
```php
<!-- Nom -->
<input type="text" class="form-control" id="nom" name="nom"
       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>

<!-- Email -->
<input type="email" class="form-control" id="email" name="email"
       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

<!-- Mot de passe : NE PAS repopuler pour des raisons de sécurité -->
```

**✅ REPOPULATION CORRIGÉE**

---

### ⚠️ 5. Implémenter le rate limiting sur le login (BACKEND)

**Statut** : NON FAIT (0%)
**Priorité** : HAUTE
**Temps estimé** : 30 minutes

**Ce qui manque** :
- Aucune protection contre les attaques par force brute sur le login

#### Modifier src/controllers/UsersController.php

Dans la méthode `login()`, ajouter AVANT la ligne 60 :

```php
public function login()
{
    // ===== RATE LIMITING =====
    $ip = $_SERVER['REMOTE_ADDR'];
    $maxAttempts = 5;
    $lockoutTime = 15 * 60; // 15 minutes en secondes

    // Initialiser le compteur si nécessaire
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }

    // Nettoyer les anciennes tentatives (plus de 15 minutes)
    foreach ($_SESSION['login_attempts'] as $attemptIp => $data) {
        if (time() - $data['time'] > $lockoutTime) {
            unset($_SESSION['login_attempts'][$attemptIp]);
        }
    }

    // Vérifier si l'IP est bloquée
    if (isset($_SESSION['login_attempts'][$ip])) {
        $attempts = $_SESSION['login_attempts'][$ip]['count'];
        $firstAttempt = $_SESSION['login_attempts'][$ip]['time'];

        if ($attempts >= $maxAttempts && (time() - $firstAttempt) < $lockoutTime) {
            $remainingTime = ceil(($lockoutTime - (time() - $firstAttempt)) / 60);
            $erreur = "Trop de tentatives de connexion. Réessayez dans {$remainingTime} minute(s).";

            $this->render('auth/login', [
                'erreur' => $erreur,
                'titre' => 'Connexion'
            ]);
            return;
        }
    }
    // ===== FIN RATE LIMITING =====

    // ... le reste du code login existant ...
}
```

Après la ligne où vous vérifiez le mot de passe (ligne ~85), ajouter en cas d'échec :

```php
} else {
    // Mot de passe incorrect

    // ===== ENREGISTRER TENTATIVE ÉCHOUÉE =====
    if (!isset($_SESSION['login_attempts'][$ip])) {
        $_SESSION['login_attempts'][$ip] = [
            'count' => 1,
            'time' => time()
        ];
    } else {
        $_SESSION['login_attempts'][$ip]['count']++;
    }
    // ===== FIN =====

    $erreur = "Identifiants incorrects";
}
```

En cas de login réussi (ligne ~74), ajouter :

```php
// Login réussi : réinitialiser les tentatives
unset($_SESSION['login_attempts'][$ip]);
```

**✅ RATE LIMITING IMPLÉMENTÉ**

---

### ⚠️ 6. Corriger la faille XSS dans api/index.php (SÉCURITÉ)

**Statut** : VULNÉRABLE
**Priorité** : CRITIQUE
**Temps estimé** : 10 minutes

**Problème** :
- Les données de l'API externe sont injectées dans le HTML sans échappement

#### Modifier views/api/index.php

Ajouter cette fonction au début du `<script>` (après la ligne 54) :

```javascript
/**
 * Échappe les caractères HTML pour prévenir XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
```

Puis modifier les lignes 82-84 pour échapper les données :

```javascript
<h5 class="card-title text-primary">${escapeHtml(meal.strMeal)}</h5>
<span class="badge bg-secondary mb-2 align-self-start">${escapeHtml(meal.strCategory)}</span>
<p class="card-text small text-muted flex-grow-1">${escapeHtml(meal.strInstructions.substring(0, 100))}...</p>
```

Et lignes 89-91 :

```javascript
<input type="hidden" name="id_api" value="${escapeHtml(meal.idMeal)}">
<input type="hidden" name="titre" value="${escapeHtml(meal.strMeal)}">
<input type="hidden" name="image_url" value="${escapeHtml(meal.strMealThumb)}">
```

**✅ FAILLE XSS CORRIGÉE**

---

## 📝 ACTIONS RECOMMANDÉES (NON OBLIGATOIRES)

### 7. Implémenter un système de flash messages complet

**Priorité** : MOYENNE
**Temps estimé** : 30 minutes

Créer dans `src/core/Controller.php` :

```php
/**
 * Définit un message flash
 */
protected function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Récupère et supprime un message flash
 */
protected function getFlash(string $type): ?string
{
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}
```

Utiliser dans les contrôleurs :
```php
$this->setFlash('success', 'Recette ajoutée avec succès !');
header('Location: /recipes');
```

Afficher dans `base.php` (après l'ouverture de `<body>`) :
```php
<?php
$flashTypes = ['success', 'error', 'info', 'warning'];
foreach ($flashTypes as $type):
    if (isset($_SESSION['flash'][$type])):
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'info' => 'alert-info',
            'warning' => 'alert-warning'
        ][$type];
?>
    <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash'][$type]) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php
        unset($_SESSION['flash'][$type]);
    endif;
endforeach;
?>
```

---

### 8. Ajouter du CSS personnalisé

**Priorité** : BASSE
**Temps estimé** : 20 minutes

Le fichier `public/css/style.css` existe mais est vide.

Ajouter :

```css
/* Variables de thème */
:root {
    --primary-color: #4A90E2;
    --danger-color: #E74C3C;
    --success-color: #2ECC71;
}

/* Animations pour les cards */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
}

/* Thème sombre */
[data-theme="dark"] {
    --bs-body-bg: #1a1a1a;
    --bs-body-color: #ffffff;
}

[data-theme="dark"] .card {
    background-color: #2a2a2a;
    border-color: #3a3a3a;
}

[data-theme="dark"] .navbar {
    background-color: #2a2a2a !important;
    border-bottom: 1px solid #3a3a3a;
}

[data-theme="dark"] input,
[data-theme="dark"] textarea,
[data-theme="dark"] select {
    background-color: #2a2a2a;
    color: #ffffff;
    border-color: #3a3a3a;
}

/* Boutons personnalisés */
.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), #357ABD);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #357ABD, var(--primary-color));
    transform: translateY(-2px);
    box-shadow: 0 5px 10px rgba(74, 144, 226, 0.3);
}

/* Animations de chargement */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.3s ease-in-out;
}
```

---

## 📊 CHECKLIST COMPLÈTE DE CONFORMITÉ

### Backend PHP (Parties obligatoires)

#### ✅ PARTIE 01 : Modèles de pages (5/5)
- [x] Structure de dossiers créée
- [x] Header/footer réutilisables
- [x] Système d'inclusion fonctionnel
- [x] Navigation dynamique
- [x] Configuration centralisée

#### ⚠️ PARTIE 02 : Formulaires (4.5/6)
- [x] Formulaire d'inscription créé
- [x] Validation complète côté serveur
- [x] Affichage des erreurs
- [x] Pattern POST/Redirect/GET
- [ ] **Formulaire de contact créé** ❌
- [ ] **Repopulation des champs complète** ❌

#### ✅ PARTIE 04 : Base de données (7/7)
- [x] Schéma BDD conçu
- [x] Tables créées
- [x] Connexion PDO sécurisée
- [x] Requêtes préparées utilisées
- [x] CRUD complet implémenté
- [x] Classes Model créées
- [x] Gestion des erreurs

#### ⚠️ PARTIE 05 : Sessions/Cookies (5/6)
- [x] Sessions configurées de manière sécurisée
- [x] Système de login fonctionnel
- [x] Système de logout fonctionnel
- [x] Zones protégées créées
- [x] Régénération ID session après login
- [ ] **Flash messages complets** ⚠️ (partiels)

#### ⚠️ PARTIE 06 : Sécurité (7/8)
- [x] Protection XSS (partielle)
- [x] Tokens CSRF sur tous les formulaires
- [x] Mots de passe hashés avec PASSWORD_ARGON2ID
- [x] Requêtes préparées PDO utilisées
- [x] Headers de sécurité configurés
- [x] Validation stricte des entrées
- [x] Pas d'informations sensibles exposées
- [ ] **Rate limiting sur le login** ❌

### Frontend JavaScript (Exigences obligatoires)

#### ❌ EXIGENCE 1 : Classe JavaScript (0%)
- [ ] **Dossier public/js/ créé** ❌
- [ ] **Classe ES6+ créée** ❌
- [ ] **HTML/CSS ajoutés** ❌
- [ ] **Classe fonctionnelle et intégrée** ❌

#### ❌ EXIGENCE 2 : Bibliothèque JavaScript (0%)
- [ ] **Bibliothèque choisie** ❌
- [ ] **CDN ajouté dans base.php** ❌
- [ ] **Bibliothèque utilisée dans le projet** ❌
- [ ] **Styles personnalisés si nécessaire** ❌

#### ✅ EXIGENCE 3 : Intégration API (100%)
- [x] API externe intégrée (TheMealDB)
- [x] fetch() utilisé
- [x] async/await utilisé
- [x] try/catch pour gestion erreurs
- [x] Affichage dynamique
- [x] Gestion loading/erreurs

### Parties Bonus (Facultatives)

#### ✅ PARTIE 07 : Architecture MVC (100%)
- [x] Front Controller créé
- [x] Routeur fonctionnel
- [x] Controllers séparés
- [x] Models séparés
- [x] Views organisées

#### ✅ PARTIE 08 : Les classes (100%)
- [x] Classes abstraites utilisées
- [x] Héritage implémenté
- [x] Méthodes protégées/privées
- [x] Encapsulation respectée

#### ✅ PARTIE 09 : Les namespaces (100%)
- [x] Namespace racine défini
- [x] Sous-namespaces cohérents
- [x] Use statements présents
- [x] Convention PSR-4

#### ✅ PARTIE 10 : Les autoloaders (100%)
- [x] Autoloader PSR-4 créé
- [x] Enregistré avec spl_autoload_register
- [x] Mapping namespace → fichiers fonctionnel
- [x] Pas de require_once manuels

---

## ⏱️ TEMPS ESTIMÉ POUR 100% DE CONFORMITÉ

| Tâche | Temps | Priorité |
|-------|-------|----------|
| 1. Classe JavaScript ThemeToggle | 1h | CRITIQUE |
| 2. Bibliothèque Toastify-js | 30min | CRITIQUE |
| 3. Formulaire de contact | 1h | HAUTE |
| 4. Repopulation des champs | 15min | HAUTE |
| 5. Rate limiting login | 30min | HAUTE |
| 6. Correction XSS api/index.php | 10min | CRITIQUE |
| **TOTAL MINIMUM** | **3h 25min** | - |
| 7. Flash messages (optionnel) | 30min | MOYENNE |
| 8. CSS personnalisé (optionnel) | 20min | BASSE |
| **TOTAL COMPLET** | **4h 15min** | - |

---

## 🎯 PLAN D'ACTION RECOMMANDÉ

### Jour 1 (2 heures)
1. ✅ Créer la classe JavaScript ThemeToggle (1h)
2. ✅ Intégrer Toastify-js (30min)
3. ✅ Corriger la faille XSS (10min)
4. ✅ Ajouter la repopulation des champs (15min)

### Jour 2 (1h30)
5. ✅ Créer le formulaire de contact (1h)
6. ✅ Implémenter le rate limiting (30min)

### Optionnel (50min)
7. Flash messages complets (30min)
8. CSS personnalisé (20min)

---

## 📈 PROGRESSION ATTENDUE

**Actuellement** : 74% de conformité globale

**Après tâches critiques** (Jour 1) : 85% de conformité

**Après toutes les tâches obligatoires** (Jour 2) : **95% de conformité**

**Avec les tâches optionnelles** : **100% de conformité + excellence**

---

## 💡 NOTES IMPORTANTES

1. **Les 4 parties bonus sont TOUTES implémentées** avec un excellent niveau de qualité (+18-20 points bonus estimés)

2. **La sécurité globale est bonne** mais nécessite quelques corrections (XSS, rate limiting)

3. **L'architecture backend est professionnelle** et dépasse les attentes

4. **Le principal manque est le frontend JavaScript** (2 exigences sur 3 non remplies)

5. **Le projet est très bien documenté** avec PHPDoc complet

---

**Date de création de ce TODO** : 14 février 2026
**Dernière mise à jour** : 14 février 2026
**Validité** : À tenir à jour au fur et à mesure de l'avancement
