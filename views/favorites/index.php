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
        <a href="/api" class="btn btn-outline-danger">🔍 Chercher l'inspiration</a>
    </div>

    <?php if(empty($favoris)): ?>
        <div class="alert alert-info">
            Vous n'avez pas encore de favoris. <a href="/api">Allez chercher de l'inspiration !</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($favoris as $fav): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-danger">
                        <img src="<?= $fav->image_url ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= htmlspecialchars($fav->titre) ?>">
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($fav->titre) ?></h5>
                        </div>
                        
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <a href="https://www.themealdb.com/meal/<?= $fav->id_api ?>" target="_blank" class="btn btn-sm btn-info text-white">Voir la recette ↗</a>
                            
                            <a href="/favorites/delete/<?= $fav->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Retirer des favoris ?')">🗑️ Retirer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>