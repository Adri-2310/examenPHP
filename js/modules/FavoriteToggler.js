/**
 * FavoriteToggler.js - Gestion des favoris avec AJAX
 *
 * OBJECTIF:
 * Permettre aux utilisateurs d'ajouter/retirer des recettes API aux favoris
 * sans rechargement de page. Utilise Fetch API pour les requêtes AJAX.
 *
 * FONCTIONNALITÉS:
 * - Toggle favoris (ajouter/retirer) avec un seul clic
 * - Suppression des favoris avec confirmation
 * - Feedback immédiat avec boutons animés et toasts notifications
 * - Suppression visuelle des cards avec animation (fade out)
 * - Gestion des erreurs de connexion/serveur
 * - CSRF protection (token inclus dans les données)
 *
 * WORKFLOW UTILISATEUR:
 * 1. Clic sur bouton "Ajouter aux favoris" (🤍)
 * 2. Requête AJAX vers /favorites/toggle
 * 3. Serveur ajoute le favori en base
 * 4. Réponse: {success: true, isFavorite: true}
 * 5. Bouton change: couleur rouge + texte "Retirer"
 * 6. Notification toast affichée
 *
 * BOUTONS GÉRÉS:
 * - .btn-toggle-fav : Ajouter/Retirer favoris (recherche + détails API)
 * - .btn-delete-fav : Supprimer complètement le favori (liste favoris)
 *
 * @class FavoriteToggler
 * @param {void}
 *
 * @example
 * // Initialisation dans main.js
 * new FavoriteToggler();
 *
 * @author Marmiton-Exam v1.0
 * @see ../notification.js pour Notifications.success/error()
 */
class FavoriteToggler {
    /**
     * Constructeur - Initialise les listeners pour tous les boutons favoris
     *
     * ACTIONS:
     * 1. Sélectionne tous les boutons .btn-toggle-fav (toggle add/remove)
     * 2. Sélectionne tous les boutons .btn-delete-fav (suppression définitive)
     * 3. Ajoute listeners de clic à tous ces boutons
     *
     * SÉLECTEURS CSS:
     * - .btn-toggle-fav : Bouton pour ajouter/retirer (couleur change)
     * - .btn-delete-fav : Bouton pour supprimer (avec confirmation)
     *
     * @constructor
     * @returns {void}
     *
     * @example
     * // HTML attendu
     * <button class="btn btn-success btn-toggle-fav"
     *         data-id="12345"
     *         data-titre="Pizza Margherita"
     *         data-image="https://..."
     *         data-csrf="token...">
     *   ❤️ Ajouter aux favoris
     * </button>
     */
    constructor() {
        // === SECTION 1: BOUTONS TOGGLE (Ajouter/Retirer) ===
        // Ces boutons apparaissent sur les recettes API (résultats recherche, détails)
        // Clic = toggle favori + changement couleur/texte du bouton
        this.addFavoriteButtons = document.querySelectorAll('.btn-toggle-fav');
        this.addFavoriteButtons.forEach(btn => {
            btn.addEventListener('click', (e) => this.handleAddFavorite(e));
        });

        // === SECTION 2: BOUTONS SUPPRESSION (Delete) ===
        // Ces boutons apparaissent dans la liste des favoris
        // Clic = demande confirmation + suppression du favori + animation fade
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
