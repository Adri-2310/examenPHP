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

        <!-- Barre de pagination -->
        <nav id="fav-pagination-container" class="d-flex justify-content-center mb-4" style="display: none;">
            <ul class="pagination" id="fav-pagination"></ul>
        </nav>

        <!-- Spinner de chargement -->
        <div id="loading-spinner" class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="text-muted mt-3">Chargement de vos favoris...</p>
            </div>
        </div>

        <div class="row" id="favorites-container">
            <?php foreach($favoris as $fav): ?>
                <div class="col-md-4 mb-4 favorite-card" data-id-api="<?= $fav->id_api ?>" data-fav-id="<?= $fav->id ?>" style="display: none;">
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

        <!-- Barre de pagination (bas) -->
        <nav id="fav-pagination-container-bottom" class="d-flex justify-content-center mt-4" style="display: none;">
            <ul class="pagination" id="fav-pagination-bottom"></ul>
        </nav>

        <!-- Script de gestion des filtres et pagination pour les favoris -->
        <script>
            const categoryFilter = document.getElementById('fav-category-filter');
            const areaFilter = document.getElementById('fav-area-filter');
            const favoriteCards = document.querySelectorAll('.favorite-card');
            const paginationContainer = document.getElementById('fav-pagination-container');
            const paginationBottomContainer = document.getElementById('fav-pagination-container-bottom');
            const paginationUl = document.getElementById('fav-pagination');
            const paginationUlBottom = document.getElementById('fav-pagination-bottom');
            const loadingSpinner = document.getElementById('loading-spinner');
            const favoritesContainer = document.getElementById('favorites-container');

            // Variables pour la pagination
            let currentPage = 1;
            const itemsPerPage = 9;

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
                                <span class="badge bg-warning mb-2 align-self-start">${meal.strArea || ''}</span>
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
             * Filtre les favoris selon les critères sélectionnés
             */
            function applyFilters() {
                const selectedCategory = categoryFilter.value;
                const selectedArea = areaFilter.value;

                // Obtenir les cartes filtrées (non cachées par les filtres)
                const filteredCards = Array.from(favoriteCards).filter(card => {
                    const idApi = card.dataset.idApi;
                    const details = favoritesData[idApi];

                    if (!details) return false;

                    const matchCategory = !selectedCategory || details.category === selectedCategory;
                    const matchArea = !selectedArea || details.area === selectedArea;

                    return matchCategory && matchArea;
                });

                // Réinitialiser à la page 1 et afficher
                currentPage = 1;
                displayPaginatedFavorites(filteredCards, 1);
            }

            /**
             * Affiche les favoris paginés
             */
            function displayPaginatedFavorites(cardsToShow, page) {
                currentPage = page;

                // Cacher le spinner dès qu'on affiche les favoris
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none !important';
                }

                // Calculer les indices
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;

                // Cacher tous les favoris
                favoriteCards.forEach(card => {
                    card.style.display = 'none';
                });

                // Afficher seulement ceux de la page
                cardsToShow.slice(startIndex, endIndex).forEach(card => {
                    card.style.display = '';
                });

                // Mettre à jour la pagination
                updatePaginationFavorites(cardsToShow);

                // Scroll vers le haut
                window.scrollTo({ top: document.querySelector('.d-flex.justify-content-between').offsetTop - 100, behavior: 'smooth' });
            }

            /**
             * Met à jour les barres de pagination des favoris
             */
            function updatePaginationFavorites(cardsToShow) {
                paginationUl.innerHTML = '';
                paginationUlBottom.innerHTML = '';

                const totalPages = Math.ceil(cardsToShow.length / itemsPerPage);

                // Fonction pour créer la pagination
                const createPaginationButtons = (paginationElement) => {
                    // Bouton précédent
                    const prevLi = document.createElement('li');
                    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                    const prevLink = document.createElement('a');
                    prevLink.className = 'page-link';
                    prevLink.href = '#';
                    prevLink.textContent = 'Précédent';
                    prevLink.onclick = (e) => {
                        e.preventDefault();
                        if (currentPage > 1) displayPaginatedFavorites(cardsToShow, currentPage - 1);
                    };
                    prevLi.appendChild(prevLink);
                    paginationElement.appendChild(prevLi);

                    // Numéros de pages
                    for (let i = 1; i <= totalPages; i++) {
                        const li = document.createElement('li');
                        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                        const link = document.createElement('a');
                        link.className = 'page-link';
                        link.href = '#';
                        link.textContent = i;
                        link.onclick = (e) => {
                            e.preventDefault();
                            displayPaginatedFavorites(cardsToShow, i);
                        };
                        li.appendChild(link);
                        paginationElement.appendChild(li);
                    }

                    // Bouton suivant
                    const nextLi = document.createElement('li');
                    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                    const nextLink = document.createElement('a');
                    nextLink.className = 'page-link';
                    nextLink.href = '#';
                    nextLink.textContent = 'Suivant';
                    nextLink.onclick = (e) => {
                        e.preventDefault();
                        if (currentPage < totalPages) displayPaginatedFavorites(cardsToShow, currentPage + 1);
                    };
                    nextLi.appendChild(nextLink);
                    paginationElement.appendChild(nextLi);
                };

                // Remplir les deux barres de pagination
                createPaginationButtons(paginationUl);
                createPaginationButtons(paginationUlBottom);

                // Montrer/cacher les conteneurs de pagination
                const showPagination = totalPages > 1;
                paginationContainer.style.display = showPagination ? '' : 'none';
                paginationBottomContainer.style.display = showPagination ? '' : 'none';
            }

            // Événements
            categoryFilter.addEventListener('change', applyFilters);
            areaFilter.addEventListener('change', applyFilters);

            // Charger les détails et afficher les favoris paginés au chargement de la page
            document.addEventListener('DOMContentLoaded', async () => {
                await loadFavoritesDetails();
                // Afficher la première page des favoris (le spinner se masquera automatiquement)
                const allCards = Array.from(favoriteCards);
                displayPaginatedFavorites(allCards, 1);
            });
        </script>
    <?php endif; ?>
</div>