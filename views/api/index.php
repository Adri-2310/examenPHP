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
    <div class="input-group mb-4 shadow-sm">
        <input type="text" id="search-input" class="form-control form-control-lg" placeholder="Ex: Pizza, Beef, Chocolate...">
        <button class="btn btn-primary btn-lg" id="search-btn">🔍 Rechercher</button>
    </div>

    <!-- Zone d'affichage des résultats (remplie par JavaScript) -->
    <div class="row" id="results-area"></div>
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
    // 1. ON DÉCLARE LE TOKEN EN HAUT, PROPREMENT
    // PHP va écrire la valeur ici une seule fois
    const csrfToken = "<?= $_SESSION['csrf_token'] ?>";

    const searchBtn = document.getElementById('search-btn');
    const searchInput = document.getElementById('search-input');
    const resultsArea = document.getElementById('results-area');

    searchBtn.addEventListener('click', async () => {
        const query = searchInput.value.trim();
        if(!query) return;

        resultsArea.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary"></div><p>Recherche...</p></div>';

        try {
            const response = await fetch(`https://www.themealdb.com/api/json/v1/1/search.php?s=${query}`);
            const data = await response.json();

            resultsArea.innerHTML = '';

            if (data.meals) {
                data.meals.forEach(meal => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4 mb-4';
                    
                    // 2. ON UTILISE LA VARIABLE JS ${csrfToken} (plus de PHP ici !)
                    col.innerHTML = `
                        <div class="card h-100 shadow-sm border-0 bg-light">
                            <img src="${meal.strMealThumb}" class="card-img-top" alt="${meal.strMeal}" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary">${escapeHtml(meal.strMeal)}</h5>
                                <span class="badge bg-secondary mb-2 align-self-start">${escapeHtml(meal.strCategory)}</span>
                                <p class="card-text small text-muted flex-grow-1">${escapeHtml(meal.strInstructions.substring(0, 100))}...</p>
                                
                                <form action="/favorites/add" method="POST" class="mt-auto">
                                    <input type="hidden" name="csrf_token" value="${csrfToken}">
                                    <input type="hidden" name="id_api" value="${escapeHtml(meal.idMeal)}">
                                    <input type="hidden" name="titre" value="${escapeHtml(meal.strMeal)}">
                                    <input type="hidden" name="image_url" value="${escapeHtml(meal.strMealThumb)}">
                                    <button type="submit" class="btn btn-danger w-100 shadow-sm">
                                        ❤️ Ajouter à mes favoris
                                    </button>
                                </form>
                            </div>
                        </div>
                    `;
                    resultsArea.appendChild(col);
                });
            } else {
                resultsArea.innerHTML = '<div class="alert alert-warning w-100 text-center">Aucune recette trouvée.</div>';
            }
        } catch (error) {
            console.error(error);
            resultsArea.innerHTML = '<div class="alert alert-danger w-100 text-center">Erreur API.</div>';
        }
    });

    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') searchBtn.click();
    });
</script>