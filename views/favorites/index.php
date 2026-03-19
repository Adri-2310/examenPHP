<?php
/**
 * Vue : favorites/index.php
 *
 * Description : Page de liste des recettes favorites (provenant de l'API TheMealDB)
 * Affiche toutes les recettes sauvegardées depuis l'API externe.
 *
 * Variables attendues :
 * @var array $favoris   Tableau d'objets favoris (de FavoritesModel::findAllByUserId)
 *   - $fav->id (int) : ID du favori
 *   - $fav->id_api (string) : ID de la recette dans TheMealDB
 *   - $fav->titre (string) : Nom de la recette
 *   - $fav->image_url (string) : URL de l'image
 * @var string $titre    Titre de la page
 *
 * Variables de session requises :
 * @var array $_SESSION['user']   Utilisateur connecté (vérification faite dans le contrôleur)
 *
 * Fonctionnalités :
 * - Affichage en grille (cards Bootstrap)
 * - Lien vers la recette complète sur TheMealDB (target="_blank")
 * - Bouton de suppression avec confirmation JavaScript
 * - Message si aucun favori
 *
 * @package    Views\Favorites
 * @created    2026
 */
?>
<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>❤️ Mes Favoris (Internet)</h1>
        <a href="/api" class="btn btn-outline-primary">🔍 Chercher l'inspiration</a>
    </div>

    <?php if(empty($favoris)): ?>
        <div class="alert alert-info">
            Vous n'avez pas encore de favoris. <a href="/api">Allez chercher de l'inspiration !</a>
        </div>
    <?php else: ?>
        <!-- Filtres (Catégorie et Région) -->
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="fav-category-filter" class="form-label">Catégorie</label>
                <select class="form-select" id="fav-category-filter">
                    <option value="">-- Toutes les catégories --</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="fav-area-filter" class="form-label">Région</label>
                <select class="form-select" id="fav-area-filter">
                    <option value="">-- Toutes les régions --</option>
                </select>
            </div>
        </div>

        <div class="row" id="favorites-container">
            <?php foreach($favoris as $fav): ?>
                <div class="col-md-4 mb-4 favorite-card" data-id-api="<?= $fav->id_api ?>" data-fav-id="<?= $fav->id ?>">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= $fav->image_url ?>" class="card-img-top" alt="<?= htmlspecialchars($fav->titre) ?>" loading="lazy">

                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($fav->titre) ?></h5>
                            <div class="fav-details"></div>
                        </div>

                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <a href="/api/lireRecette/<?= $fav->id_api ?>" class="btn btn-sm btn-info text-white">Voir la recette</a>

                            <!-- Bouton suppression AJAX -->
                            <button type="button" class="btn-delete-fav btn btn-sm btn-danger"
                                    data-id-api="<?= $fav->id_api ?>"
                                    data-fav-id="<?= $fav->id ?>"
                                    data-csrf="<?= $_SESSION['csrf_token'] ?>"
                                    title="Retirer des favoris">
                                🗑️ Retirer
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Script de gestion des filtres pour les favoris -->
        <script>
            const categoryFilter = document.getElementById('fav-category-filter');
            const areaFilter = document.getElementById('fav-area-filter');
            const favoriteCards = document.querySelectorAll('.favorite-card');

            // Objet pour stocker les détails des favoris
            const favoritesData = {};

            /**
             * Charge les détails (catégorie, région) pour chaque favori
             */
            async function loadFavoritesDetails() {
                const cards = Array.from(favoriteCards);

                for (const card of cards) {
                    const idApi = card.dataset.idApi;
                    const detailsDiv = card.querySelector('.fav-details');

                    try {
                        const response = await fetch(`/api/getMealDetails/${idApi}`);
                        const data = await response.json();

                        if (data.meals && data.meals[0]) {
                            const meal = data.meals[0];
                            favoritesData[idApi] = {
                                category: meal.strCategory || '',
                                area: meal.strArea || ''
                            };

                            // Afficher les badges
                            detailsDiv.innerHTML = `
                                <span class="badge bg-info mb-2 align-self-start">${meal.strCategory || ''}</span>
                                <span class="badge bg-secondary mb-2 align-self-start">${meal.strArea || ''}</span>
                            `;

                            // Ajouter les catégories et régions aux filtres
                            addOptionIfNotExists(categoryFilter, meal.strCategory);
                            addOptionIfNotExists(areaFilter, meal.strArea);
                        }
                    } catch (error) {
                        console.error(`Erreur lors du chargement des détails de ${idApi}:`, error);
                    }
                }
            }

            /**
             * Ajoute une option au select si elle n'existe pas déjà
             */
            function addOptionIfNotExists(selectElement, value) {
                if (!value) return;

                const exists = Array.from(selectElement.options).some(opt => opt.value === value);
                if (!exists) {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = value;
                    selectElement.appendChild(option);
                }
            }

            /**
             * Filtre les favoris affichés selon les critères sélectionnés
             */
            function applyFilters() {
                const selectedCategory = categoryFilter.value;
                const selectedArea = areaFilter.value;

                favoriteCards.forEach(card => {
                    const idApi = card.dataset.idApi;
                    const details = favoritesData[idApi];

                    if (!details) {
                        card.style.display = 'none';
                        return;
                    }

                    // Vérifier si la recette correspond aux filtres sélectionnés
                    const matchCategory = !selectedCategory || details.category === selectedCategory;
                    const matchArea = !selectedArea || details.area === selectedArea;

                    card.style.display = (matchCategory && matchArea) ? '' : 'none';
                });
            }

            // Événements
            categoryFilter.addEventListener('change', applyFilters);
            areaFilter.addEventListener('change', applyFilters);

            // Charger les détails au chargement de la page
            document.addEventListener('DOMContentLoaded', loadFavoritesDetails);
        </script>
    <?php endif; ?>
</div>