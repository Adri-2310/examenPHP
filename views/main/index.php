<?php
/**
 * Vue : main/index.php
 *
 * Description : Page d'accueil de l'application
 * Affiche un message de bienvenue et les favoris de l'utilisateur connecté.
 *
 * Variables attendues :
 * @var array $favoris         Tableau des favoris de l'utilisateur (de FavoritesModel::findAllByUserId)
 * @var array $randomRecipes   Tableau de 3 recettes aléatoires de l'API TheMealDB
 * @var string $titre          Titre de la page
 *
 * Variables de session utilisées :
 * @var array|null $_SESSION['user']   Utilisateur connecté (affecte l'affichage)
 *
 * Affichage conditionnel :
 * - Si connecté : Bouton "Chercher une nouvelle recette" + Section favoris
 * - Si non connecté : Boutons "Se connecter" et "S'inscrire"
 *
 * Fonctionnalités :
 * - Message de bienvenue
 * - Navigation conditionnelle selon état de connexion
 * - Affichage des coups de cœur (favoris) si présents
 * - Affichage de 3 recettes aléatoires de l'API (section suivante dans le code)
 *
 * @package    Views\Main
 * @created    2026
 */
?>
<div class="text-center mb-5">
    <h1>Bienvenue sur Marmiton-Exam 👨‍🍳</h1>
    <p class="lead">Votre assistant culinaire personnel</p>

    <?php if(isset($_SESSION['user'])): ?>
        <!-- Utilisateur connecté : Bouton de recherche -->
        <a href="/api" class="btn btn-primary btn-lg mt-2">🔍 Chercher une nouvelle recette</a>

    <?php else: ?>
        <!-- Visiteur non connecté : Boutons d'authentification -->
        <div class="mt-4">
            <p class="text-muted">Connectez-vous !</p>
            <a href="/users/login" class="btn btn-outline-primary">Se connecter</a>
            <a href="/users/register" class="btn btn-outline-secondary">S'inscrire</a>
        </div>

    <?php endif; ?>
</div>

<!-- Section "Mes Coups de Cœur" (affichée uniquement si connecté ET si favoris présents) -->
<?php if(isset($_SESSION['user']) && !empty($favoris)): ?>
    <div class="mb-5">
        <h2 class="border-bottom pb-2 mb-4">❤️ Mes Coups de Cœur</h2>
        <div class="row">
            <?php foreach($favoris as $fav): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= $fav->image_url ?>" class="card-img-top" alt="<?= htmlspecialchars($fav->titre) ?>" loading="lazy">
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($fav->titre) ?></h5>
                            
                            <div class="d-flex justify-content-between mt-3">
                                <a href="/favorites" class="btn btn-sm btn-outline-secondary">Gérer</a>
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
                    <img src="<?= $recette['strMealThumb'] ?>" class="card-img-top" alt="<?= $recette['strMeal'] ?>" loading="lazy">
                    
                    <div class="card-body">
                        <h5 class="card-title"><?= $recette['strMeal'] ?></h5>
                        <p class="badge bg-info text-dark"><?= $recette['strCategory'] ?></p>
                        <p class="card-text small text-muted">
                            Origine : <?= $recette['strArea'] ?>
                        </p>
                        
                        <!-- Bouton favori AJAX (seulement si connecté) -->
                        <?php if(isset($_SESSION['user'])): ?>
                            <button type="button" class="btn-toggle-fav btn btn-success w-100 mt-3"
                                    data-id="<?= $recette['idMeal'] ?>"
                                    data-titre="<?= htmlspecialchars($recette['strMeal']) ?>"
                                    data-image="<?= $recette['strMealThumb'] ?>"
                                    data-csrf="<?= $_SESSION['csrf_token'] ?>">
                                ❤️ Ajouter aux favoris
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>