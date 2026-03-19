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
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Titre de la recette</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($recette->title) ?>" required>
            </div>

            <!-- Image actuelle et modification -->
            <div class="mb-3">
                <label class="form-label">Photo de la recette</label>

                <!-- Afficher l'image actuelle si elle existe -->
                <?php if($recette->image_url): ?>
                    <div class="mb-2">
                        <p class="text-muted small">Photo actuelle:</p>
                        <img id="current-image" src="<?= htmlspecialchars($recette->image_url) ?>" alt="Photo actuelle" style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover;">
                    </div>
                <?php endif; ?>

                <!-- Champ pour remplacer l'image -->
                <label class="form-label small text-muted">Changer la photo (optionnel):</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/png, image/jpeg, image/webp">

                <!-- Aperçu de la nouvelle image -->
                <div id="image-preview-container" style="display: none; margin-top: 1rem;">
                    <p class="text-muted small">Nouvel aperçu:</p>
                    <img id="image-preview" src="" alt="Aperçu de l'image" style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover;">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Courte description</label>
                <textarea class="form-control" id="description" name="description" rows="2" required><?= htmlspecialchars($recette->description) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Ingrédients</label>
                <div id="ingredients-wrapper" data-ingredients="<?= htmlspecialchars(json_encode(json_decode($recette->ingredients, true) ?: [])) ?>"></div>
                <button type="button" id="add-ingredient-btn" class="btn btn-secondary mt-2">+ Ajouter un ingrédient</button>
            </div>

            <div class="mb-3">
                <label for="instructions" class="form-label">Étapes de préparation</label>
                <textarea class="form-control" id="instructions" name="instructions" rows="6" required><?= htmlspecialchars($recette->instructions) ?></textarea>
            </div>

            <button type="submit" class="btn btn-warning w-100 fw-bold">💾 Enregistrer les modifications</button>
        </form>
    </div>
</div>

<!-- Script pour l'aperçu d'image en édition -->
<script>
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const imagePreviewContainer = document.getElementById('image-preview-container');

    /**
     * Affiche un aperçu de la nouvelle image sélectionnée
     */
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (file) {
            // Créer un URL local pour prévisualiser la nouvelle image
            const reader = new FileReader();

            reader.onload = function(event) {
                imagePreview.src = event.target.result;
                imagePreviewContainer.style.display = 'block';
            };

            // Lire le fichier comme URL de données
            reader.readAsDataURL(file);
        } else {
            // Cacher l'aperçu si aucun fichier n'est sélectionné
            imagePreviewContainer.style.display = 'none';
            imagePreview.src = '';
        }
    });
</script>