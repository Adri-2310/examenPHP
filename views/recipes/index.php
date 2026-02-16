<?php
/**
 * Vue : recipes/index.php
 *
 * Description : Page de liste des recettes personnelles de l'utilisateur
 * Affiche toutes les recettes créées par l'utilisateur avec barre de recherche client-side.
 *
 * Variables attendues :
 * @var array $mesCreations   Tableau d'objets recettes (de RecipesModel::findAllByUserId)
 * @var string $titre          Titre de la page
 *
 * Variables de session requises :
 * @var array $_SESSION['user']   Utilisateur connecté (vérification faite dans le contrôleur)
 *
 * Fonctionnalités :
 * - Affichage en grille (cards Bootstrap)
 * - Recherche en temps réel (JavaScript côté client)
 * - Boutons Voir, Modifier, Supprimer pour chaque recette
 * - Placeholder d'image si aucune image uploadée
 * - Message si aucune recette créée
 *
 * JavaScript inclus :
 * - Recherche en temps réel dans les titres de recettes (filtre les cards)
 *
 * @package    Views\Recipes
 * @created    2026
 */
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>👨‍🍳 Mes Propres Recettes</h1>
        <a href="/recipes/ajouter" class="btn btn-primary">➕ Créer une recette</a>
    </div>

    <!-- Barre de recherche client-side (JavaScript) -->
    <div class="mb-4">
        <input type="text" id="search-perso" class="form-control" placeholder="🔍 Rechercher dans mes recettes (ex: Gâteau)...">
    </div>

    <?php if(empty($mesCreations)): ?>
        <div class="alert alert-info">
            Vous n'avez pas encore créé de recette. Lancez-vous !
        </div>
    <?php else: ?>
        <div class="row" id="recipes-list">
            <?php foreach($mesCreations as $recette): ?>
                <div class="col-md-4 mb-4 recipe-card">
                    <div class="card h-100 shadow-sm">
                        <?php if(!empty($recette->image_url)): ?>
                            <img src="<?= $recette->image_url ?>" class="card-img-top" alt="Photo recette" loading="lazy">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="color: #999;">
                                📷 Pas d'image
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title recipe-title"><?= htmlspecialchars($recette->title) ?></h5>
                            <p class="card-text text-muted small"><?= substr(htmlspecialchars($recette->description), 0, 80) ?>...</p>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <a href="/recipes/lire/<?= $recette->id ?>" class="btn btn-sm btn-info text-white">Voir</a>
                            <div>
                                <a href="/recipes/edit/<?= $recette->id ?>" class="btn btn-sm btn-warning">✏️</a>

                                <form method="POST" action="/recipes/delete/<?= $recette->id ?>" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr ?');">🗑️</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('search-perso').addEventListener('keyup', function(e) {
    let recherche = e.target.value.toLowerCase();
    let cards = document.querySelectorAll('.recipe-card');
    
    cards.forEach(card => {
        let titre = card.querySelector('.recipe-title').textContent.toLowerCase();
        if(titre.includes(recherche)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>