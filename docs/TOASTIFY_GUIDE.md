# Guide d'utilisation de Toastify

## 📋 Vue d'ensemble

Toastify est une librairie JavaScript légère qui affiche des notifications non-intrusives (toasts) sur votre application web. Elle est maintenant complètement intégrée dans le projet pour offrir une meilleure expérience utilisateur lors des actions importantes.

## 🎯 Fonctionnalités intégrées

### Notifications disponibles

Trois types de notifications sont disponibles via l'objet JavaScript `Notifications` :

1. **Succès** - `Notifications.success(message)` - Gradient vert
2. **Erreur** - `Notifications.error(message)` - Gradient orange-rouge
3. **Info** - `Notifications.info(message)` - Gradient bleu

### Exemple d'utilisation côté JavaScript

```javascript
// Notifier l'utilisateur d'une action réussie
Notifications.success('Votre profil a été mis à jour !');

// Afficher une erreur
Notifications.error('Veuillez remplir tous les champs');

// Afficher une information
Notifications.info('Les modifications seront appliquées');
```

## 🔧 Intégration dans le code

### En PHP (côté serveur)

Pour afficher des toasts depuis PHP, utilisez le système de session `$_SESSION['toasts']` :

```php
// Dans un contrôleur
$_SESSION['toasts'][] = [
    'type' => 'success',
    'message' => 'Recette créée avec succès !'
];

// Redirection
header('Location: /recipes');
```

**Types de `type` acceptés** : `'success'`, `'error'`, `'info'`

### Dans les vues PHP

Pour afficher les erreurs en toast dans les formulaires :

```php
<?php if(isset($erreur)): ?>
    <div class="alert alert-danger"><?= $erreur ?></div>
    <script>
        Notifications.error('<?= addslashes($erreur) ?>');
    </script>
<?php endif; ?>
```

## 📁 Fichiers impliqués

### Configuration
- **[views/base.php](../views/base.php)** - Template principal contenant la logique d'affichage des toasts
- **[public/js/notification.js](../public/js/notification.js)** - Helper JavaScript Toastify

### CDN Toastify
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
```

## 🚀 Fonctionnalités des toasts

- **Durée** : 3 secondes par défaut
- **Position** : En haut à droite
- **Auto-masquage** : Les toasts disparaissent automatiquement
- **Pause au survol** : Le timer s'arrête si vous passez la souris sur le toast
- **Couleurs dégradées** : Utilise des dégradés pour une meilleure visibilité

## 📊 Points d'intégration actuels

### Authentification ([src/controllers/UsersController.php](../src/controllers/UsersController.php))
- ✅ Succès de connexion
- ✅ Succès de déconnexion
- ✅ Succès d'inscription
- ✅ Erreur email invalide (formulaire)
- ✅ Erreur identifiants incorrects (formulaire)
- ✅ Erreur mot de passe trop court (formulaire)

### Recettes ([src/controllers/RecipesController.php](../src/controllers/RecipesController.php))
- ✅ Succès création de recette
- ✅ Succès modification de recette
- ✅ Succès suppression de recette
- ✅ Erreur champs obligatoires (formulaire)

### Favoris ([src/controllers/FavoritesController.php](../src/controllers/FavoritesController.php))
- ✅ Succès ajout aux favoris
- ✅ Info recette déjà en favori
- ✅ Succès suppression de favori

### Vues avec toasts d'erreur
- ✅ [views/auth/login.php](../views/auth/login.php) - Erreurs de connexion
- ✅ [views/auth/register.php](../views/auth/register.php) - Erreurs d'inscription
- ✅ [views/recipes/ajouter.php](../views/recipes/ajouter.php) - Erreurs de création
- ✅ [views/recipes/edit.php](../views/recipes/edit.php) - Erreurs de modification

## 💡 Bonnes pratiques

### Pour ajouter un nouveau toast

1. **En PHP (après une action sur la page suivante)** :
```php
// Dans le contrôleur
$_SESSION['toasts'][] = [
    'type' => 'success',
    'message' => 'Action effectuée avec succès'
];
header('Location: /page-suivante');
```

2. **En PHP (affichage direct dans une vue)** :
```php
<?php if($messageSucces): ?>
    <script>
        Notifications.success('<?= addslashes($messageSucces) ?>');
    </script>
<?php endif; ?>
```

3. **En JavaScript (pour les actions dynamiques)** :
```javascript
// Après une requête AJAX
fetch('/api/endpoint')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Notifications.success(data.message);
        } else {
            Notifications.error(data.message);
        }
    });
```

## 🎨 Personnalisation

Pour modifier les paramètres des toasts, éditez [public/js/notification.js](../public/js/notification.js) :

```javascript
success(message) {
    Toastify({
        text: message,
        duration: 3000,      // Durée en millisecondes
        gravity: "top",      // Position verticale : "top" ou "bottom"
        position: "right",   // Position horizontale : "left" ou "right"
        backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)", // Couleur
        stopOnFocus: true    // Pause au survol
    }).showToast();
}
```

## 🔗 Documentation officielle

Pour plus d'informations sur Toastify : https://apvarun.github.io/toastify-js/

---

**Intégration complétée le** : 2026-02-14
