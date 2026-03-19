# 🎯 Patterns et Bonnes Pratiques - Marmiton-Exam

**Version:** 1.0.0
**Date:** 19 mars 2026
**Objective:** Guide des patterns utilisés et bonnes pratiques appliquées

---

## 🏛️ PATTERNS DE CONCEPTION UTILISÉS

### 1. MVC (Model-View-Controller)

**Objectif:** Séparation des responsabilités

```
Model        → Données, logique métier bas-niveau
Controller   → Logique applicative, orchestration
View         → Affichage, présentation
```

**Implémentation:**
```
src/models/RecipesModel.php          ← Model
src/controllers/RecipesController.php ← Controller
views/recipes/index.php              ← View
```

**Avantage:** Facile de tester, modifier, scaling

---

### 2. Front Controller

**Objectif:** Point d'entrée unique pour toutes les requêtes

```
public/index.php → Charge Application → Main.php → Routes request
```

**Bénéfices:**
- Contrôle centralisé
- Sécurité renforcée
- Gestion des erreurs globale

---

### 3. Singleton

**Utilisé pour:** Connexion base de données

```php
class Db {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```

**Raison:** Une seule connexion PDO, réutilisée partout

---

### 4. Data Mapper

**Utilisé pour:** Models mappent les entités SQL en PHP

```php
// RecipesModel.php
public static function getById($id) {
    // SQL → Tableau PHP
    return ['id' => 5, 'title' => 'Pizza', ...];
}
```

**Avantage:** Flexibilité dans les requêtes

---

### 5. Template View

**Utilisé pour:** Séparer PHP logique de PHP affichage

```php
// views/recipes/lire.php
<?php
// Variables pré-remplies: $recipe, $user

// Affichage uniquement
echo htmlspecialchars($recipe['title']);
?>
```

**Raison:** Lisibilité, sécurité XSS

---

## ✅ BONNES PRATIQUES PHP

### 1. Validation des données

```php
// ❌ MAUVAIS
$email = $_POST['email'];
$user = UsersModel::getByEmail($email);

// ✅ BON
$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new Exception("Email invalide");
}
$user = UsersModel::getByEmail($email);
```

**Règle:** Valider TOUJOURS les données utilisateur

---

### 2. Prévention SQL Injection

```php
// ❌ DANGEREUX - SQL Injection possible
$sql = "SELECT * FROM users WHERE email = '{$_POST['email']}'";

// ✅ SÉCURISÉ - Requête préparée
$sql = "SELECT * FROM users WHERE email = :email";
$result = $db->query($sql, [':email' => $email]);
```

**Règle:** Toujours utiliser les requêtes préparées (PDO)

**Exemple dans le code:**
```php
// src/models/UsersModel.php
public static function getByEmail($email) {
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $result = self::query($sql, [':email' => $email]);
    return $result[0] ?? null;
}
```

---

### 3. Prévention XSS

```php
// ❌ DANGEREUX
<?php echo $_POST['nom']; ?>

// ✅ SÉCURISÉ
<?php echo htmlspecialchars($_POST['nom']); ?>
```

**Règle:** Toujours échapper les données utilisateur en output

**Utilisation dans le code:**
```php
// views/recipes/lire.php
<h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
```

---

### 4. Hashing des mots de passe

```php
// ❌ DANGEREUX - Stockage en clair
$password = $_POST['password'];
$sql = "INSERT INTO users (password) VALUES ('$password')";

// ✅ SÉCURISÉ - Bcrypt
$password = $_POST['password'];
$hashed = password_hash($password, PASSWORD_BCRYPT);
$sql = "INSERT INTO users (password) VALUES (:password)";

// Vérification
if (password_verify($_POST['password'], $user['password'])) {
    // Correct!
}
```

**Implémentation dans le code:**
```php
// src/models/UsersModel.php
public static function create($data) {
    $sql = "INSERT INTO users (email, nom, password, created_at)
            VALUES (:email, :nom, :password, NOW())";
    self::execute($sql, [
        ':email' => $data['email'],
        ':nom' => $data['nom'],
        ':password' => password_hash($data['password'], PASSWORD_BCRYPT)
    ]);
}
```

---

### 5. CSRF Protection

```php
// Générer un token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Dans le formulaire
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// Vérifier avant traitement
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    throw new Exception("CSRF token invalide");
}
```

**Implémentation dans le code:**
```php
// src/controllers/RecipesController.php
public function ajouter() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérifier CSRF
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            throw new Exception("CSRF token invalide");
        }
        // Traiter...
    }
}
```

---

### 6. Gestion des erreurs

```php
// ❌ DANGEREUX - Expose les détails techniques
try {
    $result = $db->query($sql);
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}

// ✅ SÉCURISÉ - Message générique + Logging
try {
    $result = $db->query($sql);
} catch (Exception $e) {
    ErrorHandler::logDatabaseError($e, "Création recette");
    ErrorHandler::displayErrorPageWithLayout(500);
}
```

**Implémentation:**
```php
// src/core/ErrorHandler.php
public static function logDatabaseError($exception, $userAction) {
    $errorMessage = $exception->getMessage();
    $userMessage = "Une erreur est survenue. Veuillez réessayer.";
    self::log($errorMessage, self::TYPE_ERROR, $userMessage);
}
```

---

## ✅ BONNES PRATIQUES JAVASCRIPT

### 1. Modules ES6

```javascript
// ❌ MAUVAIS - Variables globales
function validateForm() { ... }

// ✅ BON - Classes encapsulées
class FormValidator {
    validateField(input) { ... }
}
```

**Implémentation:**
```javascript
// public/js/modules/FormValidator.js
class FormValidator {
    constructor(forms) { ... }
    validateField(input) { ... }
}
window.FormValidator = FormValidator; // Exposition contrôlée
```

---

### 2. Validation temps réel

```javascript
// ✅ BON - Feedback utilisateur immédiat
input.addEventListener('input', () => {
    this.validateField(input);
});
```

**Avantage:** L'utilisateur sait immédiatement si ses données sont correctes

---

### 3. Prévention XSS côté client

```javascript
// ❌ DANGEREUX
element.innerHTML = userInput;

// ✅ SÉCURISÉ
element.textContent = userInput; // Ou htmlspecialchars côté serveur
```

---

### 4. Gestion d'erreurs AJAX

```javascript
// ✅ BON - Gestion d'erreurs
try {
    const response = await fetch(url, {method: 'POST'});
    const data = await response.json();
    if (data.success) {
        Notifications.success('Succès!');
    } else {
        Notifications.error(data.message);
    }
} catch (error) {
    Notifications.error('Erreur de connexion');
}
```

**Implémentation:**
```javascript
// public/js/modules/FavoriteToggler.js
async handleAddFavorite(e) {
    try {
        const response = await fetch('/?url=favorites/toggle', {...});
        const data = await response.json();
        if (data.success) {
            Notifications.success('Ajouté aux favoris!');
        }
    } catch (error) {
        Notifications.error('Erreur de connexion');
    }
}
```

---

## ✅ BONNES PRATIQUES BASE DE DONNÉES

### 1. Normalisation (3NF)

**Table users:**
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    nom VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Table recipes:**
```sql
CREATE TABLE recipes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    ingredients JSON,           -- Ingrédients stockés en JSON
    instructions TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**Avantages:**
- Évite la redondance
- Maintien de l'intégrité
- Performances optimales

### 2. Clés étrangères

```sql
-- Garantit que recipe.user_id existe dans users.id
ALTER TABLE recipes ADD CONSTRAINT fk_user
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
```

**Impactée:**
- Impossible de créer une recette orpheline
- Suppression d'user = suppression de ses recettes

### 3. Indexation

```sql
-- Recherches rapides
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE recipes ADD INDEX idx_user_id (user_id);
```

**Impact:** Requêtes ~100x plus rapides

---

## ✅ BONNES PRATIQUES PERFORMANCE

### 1. Cache en session

```php
// ✅ BON - Évite requêtes API répétées
$cacheKey = 'cache_api_search_pizza';
if (isset($_SESSION[$cacheKey])) {
    $results = $_SESSION[$cacheKey];
} else {
    $results = TheMealDB::search('pizza');
    $_SESSION[$cacheKey] = $results;
}
```

**Implémentation:**
```php
// src/controllers/ApiController.php
public function index() {
    $search = $_GET['search'] ?? '';
    $cacheKey = 'cache_api_search_' . md5($search);

    if (isset($_SESSION[$cacheKey])) {
        $recipes = $_SESSION[$cacheKey];
    } else {
        $recipes = $this->searchTheMealDb($search);
        $_SESSION[$cacheKey] = $recipes;
    }
}
```

### 2. Pagination

```php
// ✅ BON - Affiche 12 résultats par page
$page = $_GET['page'] ?? 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

$recipes = RecipesModel::getAll($offset, $perPage);
```

---

## 🔒 SÉCURITÉ - CHECKLIST

- [x] Validation serveur (pas seulement client)
- [x] Requêtes préparées (prévention SQL injection)
- [x] htmlspecialchars (prévention XSS)
- [x] password_hash (Bcrypt pour mots de passe)
- [x] CSRF tokens sur tous les POST
- [x] Gestion d'erreurs sans exposition technique
- [x] Rate limiting (login: 3 tentatives/5 min)
- [x] Sessions sécurisées (HttpOnly, Secure, SameSite)
- [x] Validation MIME type (uploads)
- [x] Limite de taille fichiers (uploads)

---

## 🎯 STANDARDS DE CODE

### Nommage

**Classes:** PascalCase
```php
class RecipesController { }
class UsersModel { }
```

**Méthodes/Variables:** camelCase
```php
public function validateField($input) { }
private $addFavoriteButtons;
```

**Constantes:** UPPER_SNAKE_CASE
```php
const TYPE_ERROR = 'ERROR';
const MAX_LOGIN_ATTEMPTS = 3;
```

### Indentation

- **PHP:** 4 espaces
- **JavaScript:** 4 espaces
- **HTML/CSS:** 4 espaces

### Commentaires

**PHP:**
```php
/**
 * Description courte
 *
 * Description plus longue si nécessaire
 *
 * @param Type $name Description
 * @return Type Description
 */
```

**JavaScript:**
```javascript
/**
 * Description courte
 *
 * @param {Type} name - Description
 * @returns {Type} Description
 */
```

---

## 📈 AMÉLIORATION CONTINUE

### Pour v1.1

- [ ] Tests unitaires (PHPUnit pour Models)
- [ ] Tests d'intégration (Selenium pour UI)
- [ ] Documentation API (OpenAPI/Swagger)
- [ ] Monitoring (erreurs, performance)
- [ ] CI/CD pipeline (GitHub Actions)
- [ ] Optimisation images (compression)
- [ ] SEO (sitemap, meta tags)

---

## 📞 Ressources

- **PSR-4 (Autoloading):** https://www.php-fig.org/psr/psr-4/
- **OWASP Top 10:** https://owasp.org/www-project-top-ten/
- **PHP Best Practices:** https://www.php-fig.org/
- **MDN Web Docs:** https://developer.mozilla.org/

---

**Document généré:** 19 mars 2026
**Validité:** Pour Marmiton-Exam v1.0.0
