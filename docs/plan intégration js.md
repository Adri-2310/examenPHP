C'est parti. Voici le fichier complet. Il contient **tout** : l'architecture, le HTML à modifier, la logique JS à écrire et les ajustements PHP nécessaires.

Copie ce contenu dans un fichier `docs/GUIDE_IMPLEMENTATION_JS.md`.

---

```markdown
# 📘 Guide Complet d'Implémentation JavaScript (Marmiton-Exam)

**Objectif** : Transformer l'application PHP statique en application dynamique moderne.
**Méthode** : Amélioration progressive (Le site fonctionne sans JS, mais est meilleur avec).
**Niveau** : Basé sur tes cours (DOM, Events, Fetch, Classes).

---

## 📂 1. Architecture & Fichiers
*Avant de coder, on prépare le terrain.*

### Structure des dossiers
Dans ton dossier `public/`, crée l'arborescence suivante :
```text
public/
└── js/
    ├── main.js                  # Point d'entrée principal
    └── modules/                 # Tes classes (Modularité)
        ├── IngredientManager.js # Pour la création de recettes
        ├── FormValidator.js     # Pour la validation login/register
        └── FavoriteToggler.js   # Pour les favoris en AJAX

```

### Liaison (Layout)

Ouvre `views/base.php` et ajoute cette ligne juste avant la fermeture `</body>` :

```html
<script type="module" src="/js/main.js"></script>

```

---

## 🍅 2. Gestion Dynamique des Ingrédients

*Permettre d'ajouter autant d'ingrédients que nécessaire sans recharger la page.*

### A. Modifications HTML (`views/recipes/ajouter.php`)

Remplace la zone actuelle des ingrédients par ceci :

* [ ] Un conteneur vide avec l'ID `ingredients-wrapper`.
* [ ] Un bouton de type "button" avec l'ID `add-ingredient-btn`.

```html
<label class="form-label">Ingrédients</label>
<div id="ingredients-wrapper">
    </div>
<button type="button" id="add-ingredient-btn" class="btn btn-secondary mt-2">+ Ajouter un ingrédient</button>

```

### B. Logique JavaScript (`modules/IngredientManager.js`)

* [ ] **Classe** : Créer une classe `IngredientManager`.
* [ ] **Constructeur** : Récupérer le bouton et le wrapper via `document.getElementById`.
* [ ] **Event** : Ajouter un `addEventListener('click')` sur le bouton "Ajouter".
* [ ] **Fonction `addInput()**` :
1. Créer une `div` avec `document.createElement('div')`.
2. Lui donner les classes Bootstrap : `row mb-2`.
3. Remplir son HTML (`innerHTML`) avec :
* Un input text `name="ingredients[name][]"` (Important pour PHP!).
* Un input text `name="ingredients[qty][]"`.
* Un bouton `<button type="button" class="btn-remove">X</button>`.


4. Ajouter la div au wrapper (`appendChild`).


* [ ] **Suppression** :
* Dans la fonction `addInput`, après avoir créé le HTML, sélectionner le bouton `.btn-remove`.
* Lui ajouter un click event qui fait `element.remove()` sur la ligne entière.



---

## 🔐 3. Validation de Formulaire (UX)

*Feedback instantané (couleur verte/rouge) pendant la frappe.*

### A. Modifications HTML

Ajoute des attributs `novalidate` sur tes balises `<form>` dans `login.php` et `register.php` pour désactiver la validation native du navigateur et utiliser la tienne.

### B. Logique JavaScript (`modules/FormValidator.js`)

* [ ] **Classe** : Créer une classe `FormValidator`.
* [ ] **Constructeur** : Sélectionner tous les inputs du formulaire.
* [ ] **Events** : Boucler sur les inputs et écouter `input` (frappe) et `blur` (perte de focus).
* [ ] **Fonction `validateField(input)**` :
* **Email** : Tester avec une Regex : `/^[^\s@]+@[^\s@]+\.[^\s@]+$/`.
* **Password** : Tester `input.value.length >= 8`.
* **Requis** : Tester `input.value.trim() !== ''`.


* [ ] **UI Feedback** :
* **Si erreur** : `input.classList.add('is-invalid')`.
* **Si succès** : `input.classList.remove('is-invalid')`, `input.classList.add('is-valid')`.


* [ ] **Blocage Submit** :
* Écouter l'événement `submit` du formulaire.
* Si un champ a la classe `is-invalid`, faire `e.preventDefault()` (empêcher l'envoi).



---

## ❤️ 4. Favoris Asynchrones (AJAX / Fetch)

*Ajouter aux favoris sans recharger la page. Le plus technique.*

### A. Modifications Backend PHP (`controllers/FavoritesController.php`)

Tu dois créer une méthode qui répond en JSON.

* [ ] Créer une méthode `toggle()`.
* [ ] Elle doit lire l'input JSON : `$data = json_decode(file_get_contents('php://input'), true);`.
* [ ] Vérifier si c'est ajouté ou supprimé.
* [ ] **CRUCIAL** : Ne pas faire de `return $this->view(...)`. Faire :
```php
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'isFavorite' => $newState]);
exit;

```



### B. Modifications HTML (Vues liste recettes)

Remplace les liens `<a>` par des boutons :

```html
<button class="btn-toggle-fav" data-id="<?= $recipe['id'] ?>">
    <?= $isFav ? '❤️' : '🤍' ?>
</button>

```

### C. Logique JavaScript (`modules/FavoriteToggler.js`)

* [ ] **Sélecteur** : `document.querySelectorAll('.btn-toggle-fav')`.
* [ ] **Boucle** : Ajouter un listener `click` sur chaque bouton.
* [ ] **Handler (Async)** :
1. `e.preventDefault()`.
2. Récupérer l'ID : `const id = this.dataset.id`.
3. **Fetch** :
```javascript
const response = await fetch('/favorites/toggle', {
    method: 'POST',
    body: JSON.stringify({ id: id })
});
const data = await response.json();

```


4. **UI Update** :
* Si `data.isFavorite` est true : changer le texte/icône du bouton en ❤️.
* Sinon : changer en 🤍.


5. **Notification** : Appeler `Toastify(...)` (tu l'as déjà installé).



---

## 🚀 5. Assemblage (`main.js`)

C'est ici que tu actives tout.

```javascript
import IngredientManager from './modules/IngredientManager.js';
import FormValidator from './modules/FormValidator.js';
import FavoriteToggler from './modules/FavoriteToggler.js';

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Activer le gestionnaire d'ingrédients si on est sur la page "Ajouter"
    if(document.getElementById('ingredients-wrapper')) {
        new IngredientManager();
    }

    // 2. Activer la validation si un formulaire existe
    const forms = document.querySelectorAll('form');
    if(forms.length > 0) {
        new FormValidator(forms);
    }

    // 3. Activer les favoris partout
    new FavoriteToggler();
    
    console.log("🚀 Marmiton JS Loaded !");
});

```

---

## ✅ Checklist de vérification finale

1. [ ] Le fichier `main.js` est chargé (voir Console F12).
2. [ ] Je peux ajouter/supprimer des lignes d'ingrédients.
3. [ ] PHP reçoit bien le tableau `ingredients` lors de la création de recette (`var_dump($_POST)` pour vérifier).
4. [ ] Les champs email deviennent rouges si le format est mauvais.
5. [ ] Le clic sur le cœur ne recharge pas la page mais change l'icône.
6. [ ] Une notification Toastify apparaît lors de l'ajout aux favoris.

```

```