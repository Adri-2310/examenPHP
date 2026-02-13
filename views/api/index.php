<div class="container mt-4 mb-5">
    <div class="text-center mb-4">
        <h1>🌍 Inspiration du Web</h1>
        <p class="text-muted">Cherchez une recette (en anglais, ex: Chicken, Pasta, Cake...) et ajoutez-la à vos favoris !</p>
    </div>

    <div class="input-group mb-4 shadow-sm">
        <input type="text" id="search-input" class="form-control form-control-lg" placeholder="Ex: Pizza, Beef, Chocolate...">
        <button class="btn btn-primary btn-lg" id="search-btn">🔍 Rechercher</button>
    </div>

    <div class="row" id="results-area"></div>
</div>

<script>
    const searchBtn = document.getElementById('search-btn');
    const searchInput = document.getElementById('search-input');
    const resultsArea = document.getElementById('results-area');

    // Écouteur sur le bouton de recherche
    searchBtn.addEventListener('click', async () => {
        const query = searchInput.value;
        if(!query) return;

        resultsArea.innerHTML = '<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div><p>Recherche en cours...</p></div>';

        try {
            // Appel à l'API TheMealDB
            const response = await fetch(`https://www.themealdb.com/api/json/v1/1/search.php?s=${query}`);
            const data = await response.json();

            resultsArea.innerHTML = '';

            if (data.meals) {
                data.meals.forEach(meal => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4 mb-4';
                    
                    // Création de la carte avec le NOUVEAU formulaire vers /favorites/add
                    col.innerHTML = `
                        <div class="card h-100 shadow-sm border-0 bg-light">
                            <img src="${meal.strMealThumb}" class="card-img-top" alt="${meal.strMeal}" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary">${meal.strMeal}</h5>
                                <span class="badge bg-secondary mb-2 align-self-start">${meal.strCategory}</span>
                                <p class="card-text small text-muted flex-grow-1">${meal.strInstructions.substring(0, 100)}...</p>
                                
                                <form action="/favorites/add" method="POST" class="mt-auto">
                                    <input type="hidden" name="id_api" value="${meal.idMeal}">
                                    <input type="hidden" name="titre" value="${meal.strMeal}">
                                    <input type="hidden" name="image_url" value="${meal.strMealThumb}">
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
                resultsArea.innerHTML = '<div class="alert alert-warning w-100 text-center">Aucune recette trouvée. Essayez un autre mot en anglais !</div>';
            }
        } catch (error) {
            console.error(error);
            resultsArea.innerHTML = '<div class="alert alert-danger w-100 text-center">Erreur de connexion à l\'API.</div>';
        }
    });

    // Bonus : Permettre de chercher en appuyant sur la touche "Entrée"
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            searchBtn.click();
        }
    });
</script>