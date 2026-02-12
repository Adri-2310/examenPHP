<div class="text-center mb-5">
    <h1>Bienvenue sur Marmiton-Exam 👨‍🍳</h1>
    <p class="lead">Votre assistant culinaire personnel</p>
    
    <?php if(isset($_SESSION['user'])): ?>
        
        <a href="/recipes/api" class="btn btn-primary btn-lg mt-2">🔍 Chercher une nouvelle recette</a>
    
    <?php else: ?>
        
        <div class="mt-4">
            <p class="text-muted">Connectez-vous !</p>
            <a href="/users/login" class="btn btn-outline-primary">Se connecter</a>
            <a href="/users/register" class="btn btn-outline-secondary">S'inscrire</a>
        </div>

    <?php endif; ?>
</div>

<?php if(isset($_SESSION['user']) && !empty($favoris)): ?>
    <div class="mb-5">
        <h2 class="border-bottom pb-2 mb-4">❤️ Mes Coups de Cœur</h2>
        <div class="row">
            <?php foreach($favoris as $fav): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-danger shadow-sm">
                        <img src="<?= $fav->image_url ?>" class="card-img-top" alt="<?= htmlspecialchars($fav->titre) ?>" style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($fav->titre) ?></h5>
                            
                            <?php if(!empty($fav->note)): ?>
                                <div class="alert alert-warning py-1 small">
                                    📝 <?= htmlspecialchars($fav->note) ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small">Pas encore de note.</p>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between mt-3">
                                <a href="/favorites" class="btn btn-sm btn-outline-danger">Gérer</a>
                                <a href="https://www.themealdb.com/meal/<?= $fav->id_api ?>" target="_blank" class="btn btn-sm btn-link">Voir la recette ↗</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div>
    <h2 class="border-bottom pb-2 mb-4">🎲 Idées du jour (Au hasard)</h2>
    <div class="row">
        <?php foreach($randomRecipes as $recette): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= $recette['strMealThumb'] ?>" class="card-img-top" alt="<?= $recette['strMeal'] ?>">
                    
                    <div class="card-body">
                        <h5 class="card-title"><?= $recette['strMeal'] ?></h5>
                        <p class="badge bg-info text-dark"><?= $recette['strCategory'] ?></p>
                        <p class="card-text small text-muted">
                            Origine : <?= $recette['strArea'] ?>
                        </p>
                        
                        <form action="/favorites/add" method="POST" class="mt-3">
                            <input type="hidden" name="id_api" value="<?= $recette['idMeal'] ?>">
                            <input type="hidden" name="titre" value="<?= $recette['strMeal'] ?>">
                            <input type="hidden" name="image_url" value="<?= $recette['strMealThumb'] ?>">
                            
                            <?php if(isset($_SESSION['user'])): ?>
                                <button type="submit" class="btn btn-success w-100">
                                    ❤️ Ajouter aux favoris
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>