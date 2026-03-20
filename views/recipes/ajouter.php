<?php
/**
 * Vue : recipes/ajouter.php
 *
 * Description : Formulaire de création d'une nouvelle recette personnelle
 * Permet à l'utilisateur de saisir titre, photo, description, ingrédients et instructions.
 *
 * Variables attendues :
 * @var string|null $erreur   Message d'erreur à afficher (si validation échoue)
 * @var string $titre         Titre de la page (défini dans RecipesController::ajouter)
 *
 * Variables de session requises :
 * @var array $_SESSION['user']   Utilisateur connecté (vérification faite dans le contrôleur)
 *
 * Fonctionnalités du formulaire :
 * - Upload d'image (optionnel) : formats acceptés jpg, jpeg, png, webp
 * - Saisie d'ingrédients avec format CSV (séparés par virgules)
 * - Tous les champs sont requis sauf l'image
 * - Attribut enctype="multipart/form-data" obligatoire pour l'upload
 *
 * Validation :
 * - Côté client : Attributs HTML5 required
 * - Côté serveur : Vérification dans RecipesController::ajouter()
 *
 * Traitement :
 * - Soumission vers RecipesController::ajouter() (même URL en POST)
 * - Redirection vers /recipes en cas de succès
 *
 * @package    Views\Recipes
 * @created    2026
 */
?>
<div class="container mt-4 mb-5">
    <h1>Ajouter une nouvelle recette 🍳</h1>
    <a href="/recipes" class="btn btn-outline-secondary mb-3">⬅ Retour</a>

    <!-- Affichage des erreurs de validation -->
    <?php if(isset($erreur)): ?>
        <div class="alert alert-danger"><?= $erreur ?></div>
        <script>
            Notifications.error('<?= addslashes($erreur) ?>');
        </script>
    <?php endif; ?>

    <div class="card shadow-sm p-4 mt-2">
        <!-- Formulaire avec support d'upload de fichiers -->
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Titre de la recette</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Ex: Gratin Dauphinois" required>
            </div>

            <!-- Upload d'image (optionnel) -->
            <!-- Formats acceptés : jpg, jpeg, png, webp (validés côté serveur) -->
            <div class="mb-3">
                <label for="image" class="form-label">Photo de la recette (Optionnel)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/png, image/jpeg, image/webp">

                <!-- Aperçu de l'image -->
                <div id="image-preview-container" style="display: none; margin-top: 1rem;">
                    <img id="image-preview" src="" alt="Aperçu de l'image" style="max-width: 100%; max-height: 300px; border-radius: 8px; object-fit: cover;">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Courte description</label>
                <textarea class="form-control" id="description" name="description" rows="2" placeholder="Ex: Un plat familial réconfortant..." required></textarea>
            </div>

            <!-- Ingrédients dynamiques (gérés par JavaScript) -->
            <div class="mb-3">
                <label class="form-label">Ingrédients</label>
                <div id="ingredients-wrapper"></div>
                <button type="button" id="add-ingredient-btn" class="btn btn-secondary mt-2">+ Ajouter un ingrédient</button>
            </div>

            <div class="mb-3">
                <label for="instructions" class="form-label">Étapes de préparation</label>
                <textarea class="form-control" id="instructions" name="instructions" rows="6" placeholder="1. Éplucher les pommes de terre..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">💾 Enregistrer ma recette</button>
        </form>
    </div>
</div>

<!-- Script pour l'aperçu d'image -->
<script>
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const imagePreviewContainer = document.getElementById('image-preview-container');

    /**
     * Affiche un aperçu de l'image sélectionnée
     */
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (file) {
            // Créer un URL local pour prévisualiser l'image
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