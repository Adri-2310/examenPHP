<div class="container mt-4">
    <h1>👨‍🍳 Trouver l'inspiration</h1>
    <p>Cherchez une recette sur le web et ajoutez-la à vos favoris !</p>

    <div class="input-group mb-4">
        <input type="text" id="search-input" class="form-control" placeholder="Ex: Pasta, Chicken, Cake...">
        <button class="btn btn-primary" id="search-btn">Rechercher</button>
    </div>

    <div class="row" id="results-area"></div>
</div>

<form id="import-form" method="POST" action="/recipes/import" style="display: none;">
    <input type="hidden" name="title" id="form-title">
    <input type="hidden" name="image" id="form-image">
    <input type="hidden" name="description" id="form-desc"> <input type="hidden" name="instructions" id="form-inst">
    <input type="hidden" name="ingredients" id="form-ingr">
</form>

<script>
    const searchBtn = document.getElementById('search-btn');
    const searchInput = document.getElementById('search-input');
    const resultsArea = document.getElementById('results-area');

    // Fonction pour rechercher sur l'API
    searchBtn.addEventListener('click', async () => {
        const query = searchInput.value;
        if(!query) return;

        resultsArea.innerHTML = '<div class="text-center">Chargement...</div>';

        try {
            // Requete AJAX vers l'API (Exigence JS validée !)
            const response = await fetch(`https://www.themealdb.com/api/json/v1/1/search.php?s=${query}`);
            const data = await response.json();

            resultsArea.innerHTML = '';

            if (data.meals) {
                data.meals.forEach(meal => {
                    // On prépare les ingrédients
                    let ingredients = [];
                    for(let i=1; i<=20; i++) {
                        if(meal[`strIngredient${i}`]) {
                            ingredients.push(meal[`strIngredient${i}`]);
                        }
                    }

                    // Création de la carte HTML
                    const col = document.createElement('div');
                    col.className = 'col-md-4 mb-4';
                    col.innerHTML = `
                        <div class="card h-100 shadow-sm">
                            <img src="${meal.strMealThumb}" class="card-img-top" alt="${meal.strMeal}">
                            <div class="card-body">
                                <h5 class="card-title">${meal.strMeal}</h5>
                                <p class="badge bg-secondary">${meal.strCategory}</p>
                                <p class="card-text small">${meal.strInstructions.substring(0, 100)}...</p>
                                <button class="btn btn-success w-100 import-btn">
                                    ❤️ Ajouter à mes recettes
                                </button>
                            </div>
                        </div>
                    `;

                    // Gestion du clic sur "Ajouter"
                    const btn = col.querySelector('.import-btn');
                    btn.addEventListener('click', () => {
                        document.getElementById('form-title').value = meal.strMeal;
                        document.getElementById('form-image').value = meal.strMealThumb;
                        document.getElementById('form-desc').value = "Catégorie : " + meal.strCategory;
                        document.getElementById('form-inst').value = meal.strInstructions;
                        document.getElementById('form-ingr').value = JSON.stringify(ingredients); // On envoie en JSON direct
                        
                        // On soumet le formulaire caché
                        document.getElementById('import-form').submit();
                    });

                    resultsArea.appendChild(col);
                });
            } else {
                resultsArea.innerHTML = '<div class="alert alert-warning">Aucune recette trouvée. Essayez en anglais (ex: Pizza)</div>';
            }
        } catch (error) {
            console.error(error);
            resultsArea.innerHTML = '<div class="alert alert-danger">Erreur de connexion à l\'API</div>';
        }
    });
</script>