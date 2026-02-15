/**
 * Point d'entrée JavaScript principal
 *
 * Initialise tous les modules JavaScript de l'application.
 */

// Charger les modules JavaScript
function loadModules() {
    const scripts = [
        '/js/modules/IngredientManager.js',
        '/js/modules/FormValidator.js',
        '/js/modules/FavoriteToggler.js'
    ];

    scripts.forEach(src => {
        const script = document.createElement('script');
        script.src = src;
        document.body.appendChild(script);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Initialisation du toggle de thème
    new ThemeToggle();

    // Charger les modules
    loadModules();

    // Attendre que les modules se chargent, puis les initialiser
    setTimeout(() => {
        // 1. Activer le gestionnaire d'ingrédients si on est sur la page "Ajouter"
        if(document.getElementById('ingredients-wrapper') && typeof IngredientManager !== 'undefined') {
            new IngredientManager();
        }

        // 2. Activer la validation si des formulaires existent
        const forms = document.querySelectorAll('form');
        if(forms.length > 0 && typeof FormValidator !== 'undefined') {
            new FormValidator(forms);
        }

        // 3. Activer les favoris partout
        if(typeof FavoriteToggler !== 'undefined') {
            new FavoriteToggler();
        }

        console.log("🚀 Marmiton JS Loaded !");
    }, 100);
});