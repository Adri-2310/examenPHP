# 🏗️ Architecture MVC - Marmiton-Exam

**Version:** 1.0.0
**Pattern:** MVC (Model-View-Controller)
**Paradigme:** OOP avec PSR-4 Autoloading

---

## 📊 Vue d'ensemble

```
┌─────────────────────────────────────────────────────┐
│                    CLIENT (Browser)                  │
│  HTML + CSS + JavaScript (Vanilla ES6+)            │
└────────────────────┬────────────────────────────────┘
                     │ HTTP Request
                     ↓
┌─────────────────────────────────────────────────────┐
│              PUBLIC/INDEX.PHP (Entry Point)         │
│  - Charge l'Autoloader                             │
│  - Inclut App\Core\Main (Front Controller)         │
└────────────────────┬────────────────────────────────┘
                     │
                     ↓
┌─────────────────────────────────────────────────────┐
│          MAIN.PHP (Routeur/Dispatcher)              │
│  - Parse l'URL: /?url=controller/action/params     │
│  - Instancie le Controller approprié               │
│  - Appelle la méthode action avec paramètres       │
└────────────────────┬────────────────────────────────┘
                     │
        ┌────────────┴────────────┐
        ↓                         ↓
   ┌─────────────┐         ┌──────────────┐
   │ CONTROLLER  │         │    MODEL     │
   │             │◄───────►│              │
   │  - Logique  │         │ - Base datos │
   │  - Flux     │         │ - Requêtes   │
   └──────┬──────┘         └──────────────┘
          │
          ↓
   ┌──────────────┐
   │    VIEW      │
   │   (HTML)     │
   │  - Template  │
   │  - Affichage │
   └──────┬───────┘
          │
          ↓
    HTTP Response
```

---

## 🗂️ Structure des dossiers

```
examenPHP/
│
├── public/                          # 🌐 POINT D'ENTRÉE WEB
│   ├── index.php                   # Front controller unique
│   ├── css/
│   │   ├── style.css              # Styles principaux
│   │   ├── theme-dark.css         # Thème sombre
│   │   └── theme-light.css        # Thème clair
│   ├── js/
│   │   ├── main.js                # Point d'entrée JS
│   │   ├── notification.js        # Système toasts
│   │   ├── classes/
│   │   │   └── ThemeToggle.js    # Toggle thème
│   │   └── modules/
│   │       ├── FormValidator.js      # Validation forms
│   │       ├── FavoriteToggler.js    # Gestion favoris
│   │       ├── PasswordToggler.js    # Toggle password
│   │       └── IngredientManager.js  # Ingrédients dynamiques
│   └── uploads/                   # Images uploadées
│
├── src/                            # 📦 LOGIQUE APPLICATION
│   ├── controllers/               # 🎮 CONTROLLERS (Logique métier)
│   │   ├── MainController.php
│   │   ├── RecipesController.php
│   │   ├── UsersController.php
│   │   ├── FavoritesController.php
│   │   ├── ApiController.php
│   │   └── ContactController.php
│   │
│   ├── models/                    # 💾 MODELS (Accès données)
│   │   ├── RecipesModel.php
│   │   ├── UsersModel.php
│   │   └── FavoritesModel.php
│   │
│   └── core/                      # ⚙️ FRAMEWORK INTERNE
│       ├── Main.php              # Routeur (dispatcher)
│       ├── Controller.php        # Classe de base Controllers
│       ├── Model.php            # Classe de base Models
│       ├── Db.php               # Connexion PDO
│       └── ErrorHandler.php     # Gestion erreurs
│
├── views/                         # 🎨 TEMPLATES HTML
│   ├── base.php                  # Layout principal
│   ├── components/
│   │   ├── header.php           # Navigation
│   │   └── footer.php           # Pied de page
│   ├── main/
│   │   └── index.php            # Accueil
│   ├── recipes/
│   │   ├── index.php            # Liste recettes
│   │   ├── lire.php             # Détail recette
│   │   ├── ajouter.php          # Formulaire création
│   │   └── edit.php             # Formulaire édition
│   ├── favorites/
│   │   └── index.php            # Liste favoris
│   ├── api/
│   │   ├── index.php            # Recherche recettes API
│   │   └── lire.php             # Détail recette API
│   ├── users/
│   │   ├── login.php            # Connexion
│   │   ├── register.php         # Inscription
│   │   ├── profile.php          # Profil utilisateur
│   │   └── contact.php          # Formulaire contact
│   └── errors/
│       ├── 404.php              # Page non trouvée
│       ├── 403.php              # Accès interdit
│       ├── 500.php              # Erreur serveur
│       └── database.php         # Erreur base de données
│
├── database/
│   └── examenPhp.sql            # Schema + données initiales
│
├── logs/
│   └── errors.log               # Fichier de logs (généré)
│
├── docs/                        # 📚 DOCUMENTATION TECHNIQUE
│   ├── API_ENDPOINTS.md        # Guide endpoints (NOUVEAU)
│   ├── ARCHITECTURE_MVC.md     # Ce fichier
│   ├── GESTION_ERREURS_BASIQUE.md
│   ├── TOASTIFY_GUIDE.md
│   └── CHECKLIST_AMELIORATIONS_UI.md
│
├── Autoloader.php              # PSR-4 Autoloader
├── .htaccess                   # Réécriture URLs
├── .gitignore                  # Fichiers Git ignorés
└── README.md                   # Documentation générale
```

---

## 🎮 CONTROLLERS - Logique métier

### Responsabilités

1. **Traiter les requêtes HTTP**
   - Récupérer les paramètres GET/POST
   - Vérifier l'authentification/autorisation
   - Appeler les Models appropriés

2. **Orchestrer la logique métier**
   - Valider les données
   - Appeler les actions Models
   - Gérer les redirections

3. **Renvoyer les réponses**
   - Rendre une view
   - Rediriger l'utilisateur
   - Retourner du JSON (API)

### Hiérarchie

```
Controller.php (classe de base)
  │
  ├── MainController.php
  ├── RecipesController.php
  ├── UsersController.php
  ├── FavoritesController.php
  ├── ApiController.php
  └── ContactController.php
```

### Exemple: RecipesController

```php
class RecipesController extends Controller
{
    /**
     * Lister les recettes de l'utilisateur
     * GET /?url=recipes/index
     */
    public function index() {
        $recipes = RecipesModel::getUserRecipes($this->userId);
        $this->view('recipes/index', ['recipes' => $recipes]);
    }

    /**
     * Consulter une recette
     * GET /?url=recipes/lire/5
     */
    public function lire($id) {
        $recipe = RecipesModel::getById($id);
        if (!$recipe) {
            ErrorHandler::displayErrorPageWithLayout(404);
        }
        $this->view('recipes/lire', ['recipe' => $recipe]);
    }

    /**
     * Créer une recette
     * POST /?url=recipes/ajouter
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation + traitement
            $recipe = RecipesModel::create($data);
            header("Location: /?url=recipes/lire/{$recipe['id']}");
        }
        $this->view('recipes/ajouter');
    }
}
```

---

## 💾 MODELS - Accès aux données

### Responsabilités

1. **Requêtes SQL**
   - SELECT, INSERT, UPDATE, DELETE
   - Requêtes préparées (PDO) = Sécurité

2. **Logique métier bas niveau**
   - Validation des données
   - Calculs simples
   - Gestion des relations

3. **Retour des données**
   - Tableau associatif PHP
   - Format attendu par Controller

### Hiérarchie

```
Model.php (classe de base)
  │
  ├── UsersModel.php
  ├── RecipesModel.php
  └── FavoritesModel.php
```

### Exemple: UsersModel

```php
class UsersModel extends Model
{
    /**
     * Récupérer utilisateur par email
     * @param string $email
     * @return array|null
     */
    public static function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $result = self::query($sql, [':email' => $email]);
        return $result[0] ?? null;
    }

    /**
     * Vérifier existence nom d'utilisateur
     * @param string $nom
     * @return bool
     */
    public static function nomExists($nom) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE nom = :nom";
        $result = self::query($sql, [':nom' => $nom]);
        return $result[0]['count'] > 0;
    }

    /**
     * Créer un nouvel utilisateur
     * @param array $data {email, nom, password}
     * @return int ID du nouvel utilisateur
     */
    public static function create($data) {
        $sql = "INSERT INTO users (email, nom, password, created_at)
                VALUES (:email, :nom, :password, NOW())";
        self::execute($sql, [
            ':email' => $data['email'],
            ':nom' => $data['nom'],
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT)
        ]);
        return self::lastInsertId();
    }
}
```

---

## 🎨 VIEWS - Présentation

### Responsabilités

1. **Affichage des données**
   - Boucles sur les données (foreach)
   - Conditionnels (if/else)
   - Formatage du contenu

2. **Formulaires HTML**
   - Structure sémantique
   - Labels et validations
   - Tokens CSRF

3. **Navigation et UX**
   - Liens internes
   - Boutons d'action
   - Messages d'erreur/succès

### Structure d'une view

```html
<?php
// 1. Variables pré-remplies par le Controller
// $recipes, $user, $errors, etc.
?>

<div class="container">
  <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>

  <!-- Contenu -->
  <?php foreach ($recipes as $recipe): ?>
    <div class="card">
      <h2><?php echo htmlspecialchars($recipe['title']); ?></h2>
      <p><?php echo htmlspecialchars($recipe['description']); ?></p>
    </div>
  <?php endforeach; ?>
</div>
```

### Exemple: base.php (Layout)

```php
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $titre ?? 'Marmiton'; ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <?php include 'components/header.php'; ?>

    <main class="container">
        <?php include "pages/{$view}.php"; ?>
    </main>

    <?php include 'components/footer.php'; ?>
    <script src="/public/js/main.js"></script>
</body>
</html>
```

---

## ⚙️ CORE - Framework interne

### Main.php (Routeur)

**Responsabilité:** Parser l'URL et dispatcher vers le bon Controller

**Processus:**
```
1. URL: /?url=recipes/lire/5
2. Parse: ['recipes', 'lire', 5]
3. Controller: RecipesController
4. Méthode: lire
5. Paramètres: [5]
6. Appel: $controller->lire(5)
```

### Db.php (Connexion)

**Pattern:** Singleton (une instance unique)

```php
// Accès global
$db = Db::getInstance();
$result = $db->query($sql, $params);
```

**Méthodes:**
- `query($sql, $params)` - SELECT
- `execute($sql, $params)` - INSERT/UPDATE/DELETE
- `lastInsertId()` - Dernier ID inséré

### ErrorHandler.php (Gestion erreurs)

**Centralize:** Logging + Affichage

```php
// Enregistrer une erreur
ErrorHandler::log(
    "Erreur base de données: ...",
    ErrorHandler::TYPE_ERROR,
    "Une erreur est survenue",
    ['file' => __FILE__, 'line' => __LINE__]
);

// Afficher page d'erreur
ErrorHandler::displayErrorPageWithLayout(404);
```

### Controller.php et Model.php (Classes de base)

**Fournissent:**
- Méthodes helper (`$this->view()`)
- Accès aux données (`$this->userId`)
- Fonctions communes

---

## 🔄 FLUX D'UNE REQUÊTE

### Exemple: Créer une recette

```
1. UTILISATEUR CLIQUE
   → Formulaire rempli
   → Bouton "Créer" cliqué
   → POST /public/?url=recipes/ajouter

2. PUBLIC/INDEX.PHP
   → require 'Autoloader.php'
   → require 'src/core/Main.php'
   → Main::dispatch()

3. MAIN.PHP (Routeur)
   → Parse l'URL: ['recipes', 'ajouter']
   → Charge: App\Controllers\RecipesController
   → Instancie: new RecipesController()
   → Appelle: $controller->ajouter()

4. RECIPESCONTROLLER
   → Récupère $_POST['title'], ['description'], etc.
   → Valide les données
   → Appelle RecipesModel::create($data)

5. RECIPESMODEL
   → Exécute: INSERT INTO recipes (...)
   → Retourne: l'ID de la nouvelle recette

6. RECIPESCONTROLLER (suite)
   → Redirection: header("Location: /?url=recipes/lire/5")

7. NAVIGATEUR
   → Suivit la redirection
   → Nouvelle requête GET /?url=recipes/lire/5
   → Le cycle recommence...

8. RECIPESCONTROLLER (lire)
   → Appelle RecipesModel::getById(5)
   → Retourne la recette

9. CONTROLLER
   → Appelle $this->view('recipes/lire', ['recipe' => ...])

10. VIEW (recipes/lire.php)
    → Affiche la recette avec HTML
    → Incluse dans base.php (layout)

11. RÉPONSE HTTP
    → HTML complet envoyé au navigateur
    → Navigateur affiche la page
```

---

## 🔐 Sécurité dans l'architecture

### 1. Validation à plusieurs niveaux

```
CLIENT (JS)           → Temps réel, UX
        ↓
CONTROLLER (PHP)      → Logique métier
        ↓
MODEL (PHP)           → Avant d'entrer en base
        ↓
BASE DE DONNÉES       → Contraintes SQL
```

### 2. Injection SQL

**❌ DANGEREUX:**
```php
$sql = "SELECT * FROM users WHERE email = '{$email}'";
```

**✅ SÉCURISÉ:**
```php
$sql = "SELECT * FROM users WHERE email = :email";
$db->query($sql, [':email' => $email]);
```

### 3. XSS (Cross-Site Scripting)

**❌ DANGEREUX:**
```php
<?php echo $user_input; ?>
```

**✅ SÉCURISÉ:**
```php
<?php echo htmlspecialchars($user_input); ?>
```

### 4. CSRF (Cross-Site Request Forgery)

**Tous les formulaires POST incluent:**
```html
<input type="hidden" name="csrf_token" value="...">
```

---

## 📈 Avantages du MVC

| Aspect | Avantage |
|--------|----------|
| **Maintenabilité** | Code organisé, facile à modifier |
| **Testabilité** | Chaque partie testable indépendamment |
| **Réutilisabilité** | Models partagés entre plusieurs Controllers |
| **Scalabilité** | Facile d'ajouter de nouvelles fonctionnalités |
| **Sécurité** | Validation centralisée |
| **Performance** | Cache, requêtes optimisées |

---

## 🛠️ Ajouter une nouvelle fonctionnalité

### Étapes

1. **Créer le Model** (`src/models/NewModel.php`)
   - Ajouter les requêtes SQL
   - Valider les données

2. **Créer le Controller** (`src/controllers/NewController.php`)
   - Définir les actions (méthodes)
   - Appeler les Models
   - Rendre les views

3. **Créer les Views** (`views/new/...`)
   - index.php (liste)
   - lire.php (détail)
   - ajouter.php (création)
   - edit.php (modification)

4. **Ajouter les routes** (accès via liens)
   - Navigation header
   - Liens internes

5. **Tester**
   - Validation client/serveur
   - Erreurs (404, 403, 500)
   - Sécurité CSRF, XSS, injection SQL

---

## 📞 Support

Pour des questions sur l'architecture:
- Voir `src/core/Main.php` pour le routage
- Voir `src/core/Db.php` pour accès données
- Voir `src/controllers/` pour exemples
- Voir `src/models/` pour requêtes SQL
