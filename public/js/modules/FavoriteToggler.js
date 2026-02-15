/**
 * FavoriteToggler.js
 *
 * Gère l'ajout/suppression aux favoris via AJAX (Fetch).
 * Permet de basculer les favoris sans rechargement de page.
 * Le cœur change d'icône immédiatement (❤️ ou 🤍) et une notification Toastify s'affiche.
 */
class FavoriteToggler {
    constructor() {
        // TODO: Initialisation du gestionnaire de favoris
        // - Sélectionner tous les boutons .btn-toggle-fav
        // - Ajouter un listener 'click' sur chaque bouton
    }

    async toggleFavorite(button) {
        // TODO: Basculer l'état du favori via Fetch
        // - Récupérer l'ID de la recette (button.dataset.id)
        // - Faire un POST /favorites/toggle avec JSON { id: id }
        // - Mettre à jour le bouton avec le nouvel état (❤️ ou 🤍)
        // - Afficher une notification Toastify
    }
}

// Exposer la classe globalement pour utilisation dans main.js
window.FavoriteToggler = FavoriteToggler;
