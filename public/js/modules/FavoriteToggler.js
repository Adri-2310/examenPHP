/**
 * FavoriteToggler.js
 *
 * Gère l'ajout/suppression aux favoris via AJAX (Fetch).
 * Permet de basculer les favoris sans rechargement de page.
 * Notifications Toastify pour le feedback utilisateur.
 *
 * @class FavoriteToggler
 * @example
 * // Utilisation dans main.js
 * new FavoriteToggler();
 */
class FavoriteToggler {
    /**
     * Initialise les écouteurs d'événements pour les boutons favoris
     * @constructor
     */
    constructor() {
        // Gérer les boutons d'ajout de favoris (.btn-toggle-fav)
        this.addFavoriteButtons = document.querySelectorAll('.btn-toggle-fav');
        this.addFavoriteButtons.forEach(btn => {
            btn.addEventListener('click', (e) => this.handleAddFavorite(e));
        });

        // Gérer les boutons de suppression de favoris (.btn-delete-fav)
        this.deleteFavoriteButtons = document.querySelectorAll('.btn-delete-fav');
        this.deleteFavoriteButtons.forEach(btn => {
            btn.addEventListener('click', (e) => this.handleDeleteFavorite(e));
        });
    }

    /**
     * Gère l'ajout/suppression d'une recette aux favoris (toggle)
     * Effectue une requête AJAX et met à jour le bouton + affiche une notification
     *
     * @async
     * @param {Event} e - Événement du clic sur le bouton
     * @returns {Promise<void>}
     */
    async handleAddFavorite(e) {
        e.preventDefault();
        const button = e.target.closest('.btn-toggle-fav');

        // Récupérer les données du bouton
        const id = button.dataset.id;
        const titre = button.dataset.titre;
        const image = button.dataset.image;
        const csrf = button.dataset.csrf;

        // Désactiver le bouton pendant la requête
        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = '⏳ Chargement...';

        try {
            // Créer les données de la requête
            const formData = new FormData();
            formData.append('id_api', id);
            formData.append('titre', titre);
            formData.append('image_url', image);
            formData.append('csrf_token', csrf);

            // Faire le fetch POST
            const response = await fetch('/?url=favorites/toggle', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success && data.isFavorite) {
                // Succès : changé en favori
                button.classList.remove('btn-success');
                button.classList.add('btn-danger');
                button.textContent = '🤍 Retirer des favoris';
                button.dataset.added = 'true';

                Notifications.success('Recette ajoutée à vos favoris ! ❤️');
            } else if (data.success && !data.isFavorite) {
                // Succès : retiré des favoris
                button.classList.remove('btn-danger');
                button.classList.add('btn-success');
                button.textContent = '❤️ Ajouter aux favoris';
                button.dataset.added = 'false';

                Notifications.success('Recette retirée de vos favoris');
            } else {
                Notifications.error(data.message || 'Erreur lors de la modification');
            }
        } catch (error) {
            Notifications.error('Erreur de connexion');
        } finally {
            // Réactiver le bouton
            button.disabled = false;
            if (!button.dataset.added || button.dataset.added === 'false') {
                button.textContent = originalText;
            }
        }
    }

    /**
     * Supprime une recette des favoris avec confirmation
     * Effectue une requête AJAX et retire la card du DOM avec animation
     *
     * @async
     * @param {Event} e - Événement du clic sur le bouton de suppression
     * @returns {Promise<void>}
     */
    async handleDeleteFavorite(e) {
        e.preventDefault();
        const button = e.target.closest('.btn-delete-fav');

        if (!button) {
            return;
        }

        // Confirmation avant suppression
        if (!confirm('Êtes-vous sûr de vouloir retirer ce favori ?')) {
            return;
        }

        // Récupérer les données du bouton
        const idApi = button.dataset.idApi;
        const csrf = button.dataset.csrf;

        // Désactiver le bouton pendant la requête
        button.disabled = true;
        button.textContent = '⏳ Suppression...';

        try {
            // Créer les données de la requête
            const formData = new FormData();
            formData.append('id_api', idApi);
            formData.append('csrf_token', csrf);

            // Faire le fetch POST
            const response = await fetch('/?url=favorites/toggle', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success && !data.isFavorite) {
                // Succès : favori supprimé
                // Retirer la card du DOM avec animation
                const card = button.closest('.col-md-4, .col-lg-3, .col-sm-6');
                if (card) {
                    card.style.transition = 'opacity 0.3s ease';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        Notifications.success('Favori supprimé');
                    }, 300);
                }
            } else {
                Notifications.error(data.message || 'Erreur lors de la suppression');
                button.disabled = false;
                button.textContent = '🗑️ Retirer';
            }
        } catch (error) {
            Notifications.error('Erreur de connexion');
            button.disabled = false;
            button.textContent = '🗑️ Retirer';
        }
    }
}

// Exposer la classe globalement pour utilisation dans main.js
window.FavoriteToggler = FavoriteToggler;
