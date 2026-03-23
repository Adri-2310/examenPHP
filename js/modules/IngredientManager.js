/**
 * IngredientManager.js - Gestion dynamique des ingrédients
 *
 * OBJECTIF:
 * Permettre aux utilisateurs d'ajouter/supprimer des ingrédients dynamiquement
 * lors de la création ou modification d'une recette, sans rechargement de page.
 *
 * FONCTIONNALITÉS:
 * - Ajout illimité de lignes d'ingrédients avec un bouton
 * - Suppression individuelle de chaque ingrédient (bouton X)
 * - Pré-remplissage automatique sur la page d'édition
 * - Validation des ingrédients au submit du formulaire
 * - Gestion du JSON: stockage et récupération en base de données
 *
 * FORMAT DES DONNÉES:
 * Les ingrédients sont stockés en JSON dans la base:
 * [
 *   {"name": "Tomate", "qty": "500g"},
 *   {"name": "Basil", "qty": "10 feuilles"}
 * ]
 *
 * POINTS CLÉS:
 * - Chaque input a les noms: ingredients[name][] et ingredients[qty][]
 * - Le [] en PHP signifie tableau (collecté automatiquement)
 * - Sur la page d'édition, les données sont pré-remplies depuis data-ingredients
 * - Validation: champs name et qty sont requis
 *
 * @class IngredientManager
 * @param {void}
 *
 * @example
 * // Initialisation dans main.js (page "ajouter" uniquement)
 * if (document.getElementById('ingredients-wrapper')) {
 *   new IngredientManager();
 * }
 *
 * @example
 * // HTML attendu
 * <div id="ingredients-wrapper" data-ingredients='[{"name":"Tomate","qty":"500g"}]'>
 *   <!-- Lignes générées ici -->
 * </div>
 * <button id="add-ingredient-btn" class="btn btn-primary">+ Ajouter ingrédient</button>
 *
 * @author Marmiton-Exam v1.0
 */
class IngredientManager {
    /**
     * Constructeur - Initialise le gestionnaire d'ingrédients
     *
     * ACTIONS:
     * 1. Récupère le bouton "Ajouter ingrédient" (#add-ingredient-btn)
     * 2. Récupère le conteneur des ingrédients (#ingredients-wrapper)
     * 3. Ajoute listener de clic au bouton
     * 4. Pré-remplit les ingrédients existants (si édition)
     *
     * VÉRIFICATION:
     * - Vérifie que le DOM contient ces deux éléments
     * - Arrête silencieusement si éléments manquants (page sans formulaire)
     *
     * @constructor
     * @returns {void}
     */
    constructor() {
        // === ÉTAPE 1: RÉCUPÉRATION DES ÉLÉMENTS ===
        // Bouton pour ajouter une nouvelle ligne d'ingrédient
        this.addBtn = document.getElementById('add-ingredient-btn');

        // Conteneur où seront ajoutées toutes les lignes d'ingrédients
        // Cet élément contient aussi l'attribut data-ingredients (sur édition)
        this.wrapper = document.getElementById('ingredients-wrapper');

        // === ÉTAPE 2: VÉRIFICATION ===
        // Arrêter si les éléments n'existent pas
        // (Cela signifie qu'on n'est pas sur la page "ajouter/éditer")
        if (!this.addBtn || !this.wrapper) {
            return;
        }

        // === ÉTAPE 3: LISTENER DU BOUTON ===
        // Quand utilisateur clique sur "+ Ajouter ingrédient"
        // Appelle addInput() pour créer une nouvelle ligne vide
        this.addBtn.addEventListener('click', (e) => {
            e.preventDefault(); // Empêcher rechargement de page
            this.addInput(); // Créer nouvelle ligne
        });

        // === ÉTAPE 4: PRÉ-REMPLISSAGE ===
        // Si on est sur la page d'édition, il y a des ingrédients existants
        // dans l'attribut data-ingredients
        // Cette méthode les récupère et remplit les inputs
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
