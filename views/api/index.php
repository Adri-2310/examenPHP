<?php
/**
 * Vue : api/index.php
 *
 * Description : Page de recherche de recettes via l'API TheMealDB
 * Interface de recherche avec appels AJAX vers l'API externe.
 *
 * Variables attendues :
 * @var string $titre   Titre de la page
 *
 * Variables de session requises :
 * @var array $_SESSION['user']   Utilisateur connecté (vérification faite dans le contrôleur)
 *
 * Fonctionnalités :
 * - Barre de recherche (requêtes en anglais : Chicken, Pasta, etc.)
 * - Appels AJAX vers TheMealDB API (côté client)
 * - Affichage dynamique des résultats en grille
 * - Formulaires d'ajout aux favoris (soumission vers /favorites/add)
 * - Gestion des erreurs et états de chargement
 *
 * API externe :
 * - Endpoint : https://www.themealdb.com/api/json/v1/1/search.php?s={query}
 * - Documentation : https://www.themealdb.com/api.php
 *
 * JavaScript inclus :
 * - Gestion de la recherche avec fetch()
 * - Génération dynamique des cards de résultats
 * - Affichage de spinner pendant le chargement
 *
 * @package    Views\Api
 * @created    2026
 */
?>
<div class="container mt-4 mb-5">
    <div class="text-center mb-4">
        <h1>🌍 Inspiration du Web</h1>
        <p class="text-muted">Cherchez une recette (en anglais, ex: Chicken, Pasta, Cake...) et ajoutez-la à vos favoris !</p>
    </div>

    <!-- Barre de recherche -->
    <div class="input-group mb-3 shadow-sm">
        <input type="text" id="search-input" class="form-control form-control-lg" placeholder="Ex: Pizza, Beef, Chocolate...">
        <button class="btn btn-primary btn-lg" id="search-btn">🔍 Rechercher</button>
    </div>

    <!-- Filtres (Catégorie et Région) -->
    <div class="row mb-4">
        <div class="col-md-6">
            <label for="category-filter" class="form-label">Catégorie</label>
            <select class="form-select" id="category-filter">
                <option value="">-- Toutes les catégories --</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="area-filter" class="form-label">Région</label>
            <select class="form-select" id="area-filter">
                <option value="">-- Toutes les régions --</option>
            </select>
        </div>
    </div>

    <!-- Zone d'affichage des résultats (remplie par JavaScript) -->
    <div class="row" id="results-area"></div>

    <!-- Barre de pagination -->
    <nav id="pagination-container" class="d-flex justify-content-center mt-4" style="display: none;">
        <ul class="pagination" id="pagination"></ul>
    </nav>
</div>

<!-- Script de recherche API (AJAX avec fetch) -->
<script>
    /**
     * Échappe les caractères HTML pour prévenir XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    const csrfToken = "<?= $_SESSION['csrf_token'] ?>";
    const searchBtn = document.getElementById('search-btn');
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const areaFilter = document.getElementById('area-filter');
    const resultsArea = document.getElementById('results-area');
    const paginationContainer = document.getElementById('pagination-container');
    const paginationUl = document.getElementById('pagination');

    // Variables pour la pagination
    let allMealsData = []; // Tous les résultats
    let currentPage = 1;
    const itemsPerPage = 9;

    /**
     * Charge les catégories disponibles via endpoint PHP avec cache localStorage (7 jours)
     */
    async function loadCategories() {
        try {
            // Vérifier le cache localStorage
            const cached = localStorage.getItem('categories_cache');
            let data = null;

            if (cached) {
                try {
                    data = JSON.parse(cached);
                } catch (e) {
                    localStorage.removeItem('categories_cache');
                }
            }

            // Si pas en cache, fetcher depuis l'API
            if (!data) {
                const response = await fetch('/api/getCategories');
                data = await response.json();
                // Sauvegarder en cache
                localStorage.setItem('categories_cache', JSON.stringify(data));
            }

            if (data.meals) {
                data.meals.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.strCategory;
                    option.textContent = cat.strCategory;
                    categoryFilter.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Erreur lors du chargement des catégories:', error);
        }
    }

    /**
     * Charge les régions (areas) disponibles via endpoint PHP avec cache localStorage (7 jours)
     */
    async function loadAreas() {
        try {
            // Vérifier le cache localStorage
            const cached = localStorage.getItem('areas_cache');
            let data = null;

            if (cached) {
                try {
                    data = JSON.parse(cached);
                } catch (e) {
                    localStorage.removeItem('areas_cache');
                }
            }

            // Si pas en cache, fetcher depuis l'API
            if (!data) {
                const response = await fetch('/api/getAreas');
                data = await response.json();
                // Sauvegarder en cache
                localStorage.setItem('areas_cache', JSON.stringify(data));
            }

            if (data.meals) {
                data.meals.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area.strArea;
                    option.textContent = area.strArea;
                    areaFilter.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Erreur lors du chargement des régions:', error);
        }
    }

    /**
     * Crée une recette pour affichage (retourne l'élément au lieu de l'ajouter)
     */
    function createMealElement(meal) {
        const col = document.createElement('div');
        col.className = 'col-md-4 mb-4';

        col.innerHTML = `
            <div class="card h-100 shadow-sm">
                <img src="${meal.strMealThumb}" class="card-img-top" alt="${meal.strMeal}" loading="lazy">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">${escapeHtml(meal.strMeal)}</h5>
                    <span class="badge bg-info mb-2 align-self-start">${escapeHtml(meal.strCategory)}</span>
                    <p class="badge bg-warning mb-2 align-self-start">${escapeHtml(meal.strArea)}</p>
                    <p class="card-text small flex-grow-1">${escapeHtml(meal.strInstructions ? meal.strInstructions.substring(0, 100) : 'Pas de description')}...</p>

                    <form action="/favorites/add" method="POST" class="mt-auto">
                        <input type="hidden" name="csrf_token" value="${csrfToken}">
                        <input type="hidden" name="id_api" value="${escapeHtml(meal.idMeal)}">
                        <input type="hidden" name="titre" value="${escapeHtml(meal.strMeal)}">
                        <input type="hidden" name="image_url" value="${escapeHtml(meal.strMealThumb)}">
                        <button type="submit" class="btn btn-danger w-100">
                            ❤️ Ajouter à mes favoris
                        </button>
                    </form>
                </div>
            </div>
        `;
        return col;
    }

    /**
     * Affiche plusieurs recettes en une seule opération (batch rendering)
     * Utilise DocumentFragment pour un seul reflow au lieu de N
     */
    function displayMealsInBatch(meals) {
        const fragment = document.createDocumentFragment();
        meals.forEach(meal => {
            const col = createMealElement(meal);
            fragment.appendChild(col);
        });
        resultsArea.appendChild(fragment);
    }

    /**
     * Affiche les résultats paginés et met à jour la barre de pagination
     */
    function displayPaginatedResults(page = 1) {
        currentPage = page;
        resultsArea.innerHTML = '';

        // Calculer les indices de début et fin
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const mealsToShow = allMealsData.slice(startIndex, endIndex);

        // Afficher les recettes en batch (un seul reflow DOM)
        displayMealsInBatch(mealsToShow);

        // Mettre à jour la pagination
        updatePagination();

        // Scroll vers le haut (sans animation pour plus de rapidité)
        resultsArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /**
     * Crée et affiche la barre de pagination
     */
    function updatePagination() {
        paginationUl.innerHTML = '';

        const totalPages = Math.ceil(allMealsData.length / itemsPerPage);

        // Bouton précédent
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        const prevLink = document.createElement('a');
        prevLink.className = 'page-link';
        prevLink.href = '#';
        prevLink.textContent = 'Précédent';
        prevLink.onclick = (e) => {
            e.preventDefault();
            if (currentPage > 1) displayPaginatedResults(currentPage - 1);
        };
        prevLi.appendChild(prevLink);
        paginationUl.appendChild(prevLi);

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
                displayPaginatedResults(i);
            };
            li.appendChild(link);
            paginationUl.appendChild(li);
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
            if (currentPage < totalPages) displayPaginatedResults(currentPage + 1);
        };
        nextLi.appendChild(nextLink);
        paginationUl.appendChild(nextLi);

        // Montrer/cacher le conteneur de pagination
        paginationContainer.style.display = totalPages > 1 ? '' : 'none';
    }

    /**
     * Effectue la recherche en combinant texte + filtres
     */
    /**
     * Batch-load les détails des repas par groupes de 10 au lieu de tout charger en parallèle
     * Réduit la charge serveur et les délais d'attente
     */
    async function batchLoadMealDetails(mealIds) {
        const batchSize = 10;
        const allDetails = [];

        for (let i = 0; i < mealIds.length; i += batchSize) {
            const batch = mealIds.slice(i, i + batchSize);
            const promises = batch.map(id =>
                fetch(`/api/getMealDetails/${id}`).then(r => r.json().catch(() => ({ meals: null })))
            );
            const results = await Promise.all(promises);
            const details = results.map(r => r.meals?.[0]).filter(Boolean);
            allDetails.push(...details);
        }

        return allDetails;
    }

    async function search() {
        const query = searchInput.value.trim();
        const category = categoryFilter.value;
        const area = areaFilter.value;

        // Vérifier qu'au moins un critère est fourni
        if (!query && !category && !area) {
            alert('Entrez une recherche ou sélectionnez une catégorie/région');
            return;
        }

        // Vérifier le cache localStorage
        const cacheKey = `meals_${query}_${category}_${area}`;
        const cached = localStorage.getItem(cacheKey);
        if (cached) {
            try {
                allMealsData = JSON.parse(cached);
                currentPage = 1;
                displayPaginatedResults(1);
                return;
            } catch (e) {
                console.warn('Cache invalide, nouvelle recherche');
            }
        }

        resultsArea.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary"></div><p>Recherche...</p></div>';

        try {
            let allMeals = [];

            // 1. Recherche par texte (si fourni)
            if (query) {
                const response = await fetch(`https://www.themealdb.com/api/json/v1/1/search.php?s=${encodeURIComponent(query)}`);
                const data = await response.json();
                if (data.meals) allMeals = data.meals;
            }

            // 2. Filtre par catégorie (si fourni) - Batch load les détails
            if (category) {
                const response = await fetch(`/api/filterByCategory/${encodeURIComponent(category)}`);
                const data = await response.json();
                if (data.meals && data.meals.length > 0) {
                    // Batch-load les détails pour réduire les appels API
                    const mealIds = data.meals.map(m => m.idMeal);
                    const fullMeals = await batchLoadMealDetails(mealIds);

                    // Créer un Map pour O(1) lookup au lieu de O(n²) avec find()
                    const fullMealsMap = new Map(fullMeals.map(m => [m.idMeal, m]));

                    if (allMeals.length > 0) {
                        // Intersection avec résultats existants
                        allMeals = allMeals.filter(m => fullMealsMap.has(m.idMeal))
                                          .map(m => fullMealsMap.get(m.idMeal) || m);
                    } else {
                        allMeals = fullMeals;
                    }
                }
            }

            // 3. Filtre par région (si fourni) - Batch load les détails
            if (area) {
                const response = await fetch(`/api/filterByArea/${encodeURIComponent(area)}`);
                const data = await response.json();
                if (data.meals && data.meals.length > 0) {
                    // Batch-load les détails pour réduire les appels API
                    const mealIds = data.meals.map(m => m.idMeal);
                    const fullMeals = await batchLoadMealDetails(mealIds);

                    // Créer un Map pour O(1) lookup
                    const fullMealsMap = new Map(fullMeals.map(m => [m.idMeal, m]));

                    if (allMeals.length > 0) {
                        // Intersection avec résultats existants
                        allMeals = allMeals.filter(m => fullMealsMap.has(m.idMeal))
                                          .map(m => fullMealsMap.get(m.idMeal) || m);
                    } else {
                        allMeals = fullMeals;
                    }
                }
            }

            if (allMeals.length === 0) {
                resultsArea.innerHTML = '<div class="alert alert-warning w-100 text-center">Aucune recette trouvée.</div>';
                paginationContainer.style.display = 'none';
                return;
            }

            // Dédupliquer les résultats et stocker en mémoire
            allMealsData = Array.from(new Map(allMeals.map(m => [m.idMeal, m])).values());

            // Sauvegarder en cache localStorage (expire après 24h via date ou localStorage clearing)
            localStorage.setItem(cacheKey, JSON.stringify(allMealsData));

            // Afficher les résultats paginés (page 1)
            currentPage = 1;
            displayPaginatedResults(1);

        } catch (error) {
            console.error(error);
            resultsArea.innerHTML = '<div class="alert alert-danger w-100 text-center">Erreur API.</div>';
        }
    }

    // Événements
    searchBtn.addEventListener('click', search);
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') search();
    });
    categoryFilter.addEventListener('change', search);
    areaFilter.addEventListener('change', search);

    // Charger catégories et régions au chargement de la page
    document.addEventListener('DOMContentLoaded', () => {
        loadCategories();
        loadAreas();
    });
</script>