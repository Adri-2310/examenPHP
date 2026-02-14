<?php
/**
 * Vue : recipes/edit.php
 *
 * Description : Formulaire de modification d'une recette existante
 * Affiche un formulaire pré-rempli avec les données de la recette.
 *
 * Variables attendues :
 * @var object $recette         Objet recette à modifier (de RecipesModel::find)
 * @var string $ingredientsList Ingrédients au format CSV pour le champ input (préparé dans le contrôleur)
 * @var string|null $erreur     Message d'erreur de validation (optionnel)
 * @var string $titre           Titre de la page
 *
 * Variables de session requises :
 * @var array $_SESSION['user']   Utilisateur connecté (vérification faite dans le contrôleur)
 *
 * Sécurité :
 * - Vérification de propriété (user_id) faite dans RecipesController::edit()
 * - htmlspecialchars() sur toutes les données affichées
 *
 * Traitement :
 * - Soumission vers RecipesController::edit($id) (même URL en POST)
 * - Redirection vers /recipes/lire/{id} en cas de succès
 *
 * @package    Views\Recipes
 * @created    2026
 */
?>
<div class="container mt-4 mb-5">
    <h1>✏️ Modifier la recette</h1>
    <a href="/recipes/lire/<?= $recette->id ?>" class="btn btn-outline-secondary mb-3">⬅ Annuler</a>

    <?php if(isset($erreur)): ?>
        <div class="alert alert-danger"><?= $erreur ?></div>
        <script>
            Notifications.error('<?= addslashes($erreur) ?>');
        </script>
    <?php endif; ?>

    <div class="card shadow-sm p-4 mt-2 border-warning">
        <!-- Formulaire pré-rempli avec les données existantes -->
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Titre de la recette</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($recette->title) ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Courte description</label>
                <textarea class="form-control" id="description" name="description" rows="2" required><?= htmlspecialchars($recette->description) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="ingredients" class="form-label">Ingrédients (séparés par des virgules)</label>
                <input type="text" class="form-control" id="ingredients" name="ingredients" value="<?= htmlspecialchars($ingredientsList) ?>" required>
                <div class="form-text text-primary">⚠️ Conservez bien la séparation par des virgules.</div>
            </div>

            <div class="mb-3">
                <label for="instructions" class="form-label">Étapes de préparation</label>
                <textarea class="form-control" id="instructions" name="instructions" rows="6" required><?= htmlspecialchars($recette->instructions) ?></textarea>
            </div>

            <button type="submit" class="btn btn-warning w-100 fw-bold">💾 Enregistrer les modifications</button>
        </form>
    </div>
</div>