/**
 * IngredientManager.js
 *
 * Gère l'ajout/suppression dynamique des ingrédients dans le formulaire de création de recettes.
 * Permet aux utilisateurs d'ajouter autant d'ingrédients que nécessaire sans rechargement de page.
 *
 * @class IngredientManager
 * @example
 * // Utilisation dans main.js
 * new IngredientManager();
 */
class IngredientManager {
    /**
     * Initialise le gestionnaire d'ingrédients
     * Récupère les éléments du DOM et ajoute les écouteurs d'événements
     * @constructor
     */
    constructor() {
        // Récupérer les éléments du DOM
        this.addBtn = document.getElementById('add-ingredient-btn');
        this.wrapper = document.getElementById('ingredients-wrapper');

        // Vérifier que les éléments existent
        if (!this.addBtn || !this.wrapper) {
            return;
        }

        // Ajouter un listener 'click' sur le bouton
        this.addBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.addInput();
        });

        // Pré-remplir les ingrédients existants (pour la page d'édition)
        this.populateExistingIngredients();
    }

    /**
     * Pré-remplit les ingrédients existants (pour la modification de recettes)
     * Parse le JSON stocké en attribut data-ingredients et crée les inputs correspondants
     *
     * @returns {void}
     */
    populateExistingIngredients() {
        // Récupérer les ingrédients stockés en attribut data-ingredients
        const dataIngredients = this.wrapper.getAttribute('data-ingredients');

        if (!dataIngredients) {
            // Pas de données pré-remplies, créer un input vide par défaut
            this.addInput();
            return;
        }

        try {
            const ingredients = JSON.parse(dataIngredients);

            if (Array.isArray(ingredients) && ingredients.length > 0) {
                // Créer les inputs pour chaque ingrédient existant
                ingredients.forEach(ingredient => {
                    this.addInput(ingredient.name, ingredient.qty);
                });
            } else {
                // Tableau vide, créer un input vide par défaut
                this.addInput();
            }
        } catch (error) {
            // Erreur silencieuse
            this.addInput();
        }
    }

    /**
     * Ajoute dynamiquement une ligne d'ingrédient au formulaire
     * Crée les champs nom et quantité + bouton de suppression
     *
     * @param {string} name - Nom de l'ingrédient (valeur initiale, optionnel)
     * @param {string} qty - Quantité de l'ingrédient (valeur initiale, optionnel)
     * @returns {void}
     */
    addInput(name = '', qty = '') {
        // Créer une div wrapper pour la ligne d'ingrédient
        const div = document.createElement('div');
        div.className = 'row mb-2 gap-2';

        // Créer l'HTML pour le champ nom
        const nameCol = document.createElement('div');
        nameCol.className = 'col-md-6';
        const nameInput = document.createElement('input');
        nameInput.type = 'text';
        nameInput.className = 'form-control';
        nameInput.name = 'ingredients[name][]';
        nameInput.placeholder = 'Ex: Tomate';
        nameInput.value = name;
        nameInput.required = true;
        nameCol.appendChild(nameInput);

        // Créer l'HTML pour le champ quantité
        const qtyCol = document.createElement('div');
        qtyCol.className = 'col-md-4';
        const qtyInput = document.createElement('input');
        qtyInput.type = 'text';
        qtyInput.className = 'form-control';
        qtyInput.name = 'ingredients[qty][]';
        qtyInput.placeholder = 'Ex: 500g';
        qtyInput.value = qty;
        qtyInput.required = true;
        qtyCol.appendChild(qtyInput);

        // Créer le bouton de suppression
        const btnCol = document.createElement('div');
        btnCol.className = 'col-md-2';
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger w-100';
        removeBtn.textContent = '✕';
        removeBtn.title = 'Supprimer cet ingrédient';

        // Ajouter le listener pour supprimer la ligne
        removeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            div.remove();
        });

        // Assembler les colonnes
        btnCol.appendChild(removeBtn);
        div.appendChild(nameCol);
        div.appendChild(qtyCol);
        div.appendChild(btnCol);

        // Ajouter la ligne au wrapper
        this.wrapper.appendChild(div);

        // Focus sur le nouvel input de nom (seulement si c'est un nouvel input vide)
        if (!name) {
            nameInput.focus();
        }
    }
}

// Exposer la classe globalement pour utilisation dans main.js
window.IngredientManager = IngredientManager;
