/**
 * main.js - Point d'entrée JavaScript principal
 *
 * RESPONSABILITÉS:
 * - Chargement dynamique des modules JavaScript
 * - Initialisation des modules au bon moment (DOMContentLoaded)
 * - Gestion du thème global
 * - Coordination entre les différents modules
 *
 * FLUX D'EXÉCUTION:
 * 1. DOMContentLoaded déclenche l'initialisation
 * 2. ThemeToggle initialise le thème sombre/clair
 * 3. Modules chargés dynamiquement en arrière-plan
 * 4. Après chargement (100ms), les modules sont initialisés selon le contexte
 *
 * MODULES GÉRÉS:
 * - IngredientManager: Gestion dynamique des ingrédients (page ajouter)
 * - FormValidator: Validation des formulaires en temps réel
 * - FavoriteToggler: Ajout/suppression des favoris (AJAX)
 * - PasswordToggler: Affichage/masquage des mots de passe
 * - ThemeToggle: Basculement thème sombre/clair (global)
 *
 * @author Marmiton-Exam v1.0
 * @see public/js/classes/ThemeToggle.js
 * @see public/js/modules/
 */

/**
 * Charge dynamiquement les modules JavaScript de manière asynchrone
 *
 * RAISON DU CHARGEMENT DYNAMIQUE:
 * - Réduit le taille initiale du bundle
 * - Évite les erreurs "module not defined" sur certaines pages
 * - Permet l'initialisation contrôlée avec setTimeout
 *
 * @function loadModules
 * @returns {void}
 *
 * @example
 * // Appelé automatiquement dans DOMContentLoaded
 * loadModules();
 */
function loadModules() {
    const scripts = [
        '/js/modules/IngredientManager.js',
        '/js/modules/FormValidator.js',
        '/js/modules/FavoriteToggler.js',
        '/js/modules/PasswordToggler.js'
    ];

    scripts.forEach(src => {
        const script = document.createElement('script');
        script.src = src;
        script.async = true; // Chargement asynchrone pour ne pas bloquer
        document.body.appendChild(script);
    });
}

/**
 * Initialise tous les modules JavaScript quand le DOM est prêt
 *
 * SÉQUENCE D'INITIALISATION:
 * 1. ThemeToggle - Applique immédiatement le thème sauvegardé
 * 2. loadModules() - Lance le chargement asynchrone des modules
 * 3. setTimeout(50ms) - Attend que les modules soient chargés (optimisé)
 * 4. Initialise les modules selon le contexte de la page
 *
 * @event DOMContentLoaded
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1️⃣ THÈME GLOBAL
    // Doit être initialisé immédiatement pour éviter le flash FOUC
    // (Flash of Unstyled Content)
    new ThemeToggle();

    // 2️⃣ CHARGEMENT DES MODULES
    // Charge les modules en arrière-plan sans bloquer
    loadModules();

    // 3️⃣ INITIALISATION DES MODULES
    // Attend 50ms pour que les modules se chargent avant initialisation (optimisé)
    // Note: Un timing trop court peut causer des erreurs "undefined"
    setTimeout(() => {
        // ===== MODULE 1: Gestion des ingrédients =====
        // Contexte: Seulement sur la page "Ajouter une recette"
        // Élément clé: #ingredients-wrapper
        if(document.getElementById('ingredients-wrapper') && typeof IngredientManager !== 'undefined') {
            new IngredientManager();
            console.debug('[Main] IngredientManager initialisé');
        }

        // ===== MODULE 2: Validation des formulaires =====
        // Contexte: Sur toutes les pages avec formulaires
        // Cible: Tous les <form> du document
        // Fonctionnalité: Validation temps réel + prévention submit invalide
        const forms = document.querySelectorAll('form');
        if(forms.length > 0 && typeof FormValidator !== 'undefined') {
            new FormValidator(forms);
            console.debug(`[Main] FormValidator initialisé pour ${forms.length} formulaire(s)`);
        }

        // ===== MODULE 3: Gestion des favoris =====
        // Contexte: Disponible globalement (recherche + favoris)
        // Cible: Boutons .btn-toggle-fav et .btn-delete-fav
        // Fonctionnalité: AJAX add/remove favoris sans rechargement
        if(typeof FavoriteToggler !== 'undefined') {
            new FavoriteToggler();
            console.debug('[Main] FavoriteToggler initialisé');
        }

        // ===== MODULE 4: Affichage/masquage mot de passe =====
        // Contexte: Sur les pages d'authentification (login, register, contact)
        // Cible: Tous les <input type="password">
        // Fonctionnalité: Toggle password visibility avec icône 👁️
        if(typeof PasswordToggler !== 'undefined') {
            new PasswordToggler();
            console.debug('[Main] PasswordToggler initialisé');
        }

    }, 50); // Délai d'attente réduit pour chargement plus rapide des modules
});