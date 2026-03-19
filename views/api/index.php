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
     * Charge les catégories disponibles via endpoint PHP
     */
    async function loadCategories() {
        try {
            const response = await fetch('/api/getCategories');
            const data = await response.json();

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
     * Charge les régions (areas) disponibles via endpoint PHP
     */
    async function loadAreas() {
        try {
            const response = await fetch('/api/getAreas');
            const data = await response.json();

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
     * Affiche une recette dans la grille de résultats
     */
    function displayMeal(meal) {
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
        resultsArea.appendChild(col);
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

        // Afficher les recettes de cette page
        mealsToShow.forEach(meal => displayMeal(meal));

        // Mettre à jour la pagination
        updatePagination();

        // Scroll vers le haut
        window.scrollTo({ top: resultsArea.offsetTop - 100, behavior: 'smooth' });
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
    async function search() {
        const query = searchInput.value.trim();
        const category = categoryFilter.value;
        const area = areaFilter.value;

        // Vérifier qu'au moins un critère est fourni
        if (!query && !category && !area) {
            alert('Entrez une recherche ou sélectionnez une catégorie/région');
            return;
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

            // 2. Filtre par catégorie (si fourni)
            if (category) {
                const response = await fetch(`/api/filterByCategory/${encodeURIComponent(category)}`);
                const data = await response.json();
                if (data.meals) {
                    // Charger les détails complets pour avoir strCategory et strArea
                    const detailPromises = data.meals.map(meal =>
                        fetch(`/api/getMealDetails/${meal.idMeal}`).then(r => r.json())
                    );
                    const details = await Promise.all(detailPromises);
                    const fullMeals = details.map(d => d.meals && d.meals[0]).filter(Boolean);

                    // Si on a déjà des résultats de recherche texte, faire l'intersection
                    if (allMeals.length > 0) {
                        const fullMealIds = new Set(fullMeals.map(m => m.idMeal));
                        allMeals = allMeals.filter(m => fullMealIds.has(m.idMeal));
                        // Ajouter les détails complets (strArea, etc)
                        allMeals = allMeals.map(meal => {
                            const full = fullMeals.find(f => f.idMeal === meal.idMeal);
                            return full || meal;
                        });
                    } else {
                        allMeals = fullMeals;
                    }
                }
            }

            // 3. Filtre par région (si fourni)
            if (area) {
                const response = await fetch(`/api/filterByArea/${encodeURIComponent(area)}`);
                const data = await response.json();
                if (data.meals) {
                    // Charger les détails complets pour avoir strCategory et strArea
                    const detailPromises = data.meals.map(meal =>
                        fetch(`/api/getMealDetails/${meal.idMeal}`).then(r => r.json())
                    );
                    const details = await Promise.all(detailPromises);
                    const fullMeals = details.map(d => d.meals && d.meals[0]).filter(Boolean);

                    // Faire l'intersection avec les résultats actuels
                    const fullMealIds = new Set(fullMeals.map(m => m.idMeal));
                    allMeals = allMeals.filter(m => fullMealIds.has(m.idMeal));
                    // Ajouter les détails complets (strArea, etc)
                    allMeals = allMeals.map(meal => {
                        const full = fullMeals.find(f => f.idMeal === meal.idMeal);
                        return full || meal;
                    });
                }
            }

            if (allMeals.length === 0) {
                resultsArea.innerHTML = '<div class="alert alert-warning w-100 text-center">Aucune recette trouvée.</div>';
                paginationContainer.style.display = 'none';
                return;
            }

            // Dédupliquer les résultats et stocker en mémoire
            allMealsData = Array.from(new Map(allMeals.map(m => [m.idMeal, m])).values());

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