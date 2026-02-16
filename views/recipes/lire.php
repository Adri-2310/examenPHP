<?php
/**
 * Vue : recipes/lire.php
 *
 * Description : Page de détail d'une recette (lecture seule)
 * Affiche toutes les informations d'une recette avec mise en page complète.
 *
 * Variables attendues :
 * @var object $recette   Objet recette récupéré depuis RecipesModel::find()
 *   - $recette->id (int) : Identifiant de la recette
 *   - $recette->title (string) : Titre de la recette
 *   - $recette->description (string) : Description courte
 *   - $recette->ingredients (string) : JSON array des ingrédients
 *   - $recette->instructions (string) : Étapes de préparation
 *   - $recette->image_url (string|null) : URL de l'image (optionnel)
 *   - $recette->user_id (int) : ID de l'utilisateur créateur
 *   - $recette->created_at (datetime) : Date de création
 * @var string $titre     Titre de la page (= $recette->title)
 *
 * Variables de session utilisées :
 * @var array|null $_SESSION['user']   Si connecté et propriétaire, affiche boutons Modifier/Supprimer
 *
 * Fonctionnalités :
 * - Affichage de l'image (si présente)
 * - Décodage JSON des ingrédients avec fallback
 * - Boutons de modification/suppression (uniquement pour le créateur)
 * - Confirmation JavaScript avant suppression
 * - Protection XSS avec htmlspecialchars() sur toutes les données
 *
 * Sécurité :
 * - htmlspecialchars() sur toutes les données utilisateur
 * - nl2br() pour préserver les retours à la ligne
 * - Vérification user_id avant affichage des boutons d'action
 *
 * @package    Views\Recipes
 * @created    2026
 */
?>
<div class="container mt-4 mb-5">
    <a href="/recipes" class="btn btn-outline-secondary mb-4">⬅ Retour à mes recettes</a>

    <div class="card shadow border-0">
        <!-- En-tête de la recette -->
        <div class="card-header bg-primary text-white p-4">
            <h1 class="mb-0">👨‍🍳 <?= htmlspecialchars($recette->title) ?></h1>
            <p class="mt-2 mb-0 opacity-75">
                Créée le <?= date('d/m/Y', strtotime($recette->created_at)) ?>
            </p>
        </div>

        <!-- Image de la recette (si présente) -->
        <?php if(!empty($recette->image_url)): ?>
            <img src="<?= $recette->image_url ?>" class="img-fluid w-100" style="max-height: 400px; object-fit: cover;" alt="Photo">
        <?php endif; ?>
        
        <div class="card-body p-4">
            <h4 class="text-primary mb-3">À propos de cette recette</h4>
            <p class="lead text-muted"><?= nl2br(htmlspecialchars($recette->description)) ?></p>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="text-primary mb-3">🛒 Ingrédients</h4>
                    <ul class="list-group list-group-flush shadow-sm">
                        <?php
                        // === DÉCODAGE JSON DES INGRÉDIENTS ===
                        // Les ingrédients sont stockés en JSON : ["tomate", "oignon", "ail"]
                        // On les transforme en tableau PHP pour les afficher
                        $ingredients = json_decode($recette->ingredients, true);

                        // Vérification du décodage et affichage de la liste
                        if ($ingredients && is_array($ingredients)):
                            foreach($ingredients as $ingredient):
                                // Gérer les deux formats : ancien (chaîne) et nouveau (tableau avec name/qty)
                                if (is_array($ingredient) && isset($ingredient['name'])):
                                    // Nouveau format : {"name": "...", "qty": "..."}
                                    $name = htmlspecialchars($ingredient['name']);
                                    $qty = htmlspecialchars($ingredient['qty']);
                                    $display = $name . ($qty ? " ($qty)" : "");
                                else:
                                    // Ancien format : chaîne simple
                                    $display = htmlspecialchars(trim($ingredient));
                                endif;
                        ?>
                                <li class="list-group-item bg-light border-0 mb-1 rounded">
                                    ✅ <?= $display ?>
                                </li>
                        <?php
                            endforeach;
                        else:
                            // Fallback : Si le format n'est pas JSON (anciennes données)
                            // On affiche la chaîne brute
                        ?>
                            <li class="list-group-item"><?= htmlspecialchars($recette->ingredients) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-md-8">
                    <h4 class="text-primary mb-3">🍳 Préparation</h4>
                    <div class="p-4 bg-light rounded shadow-sm" style="white-space: pre-wrap; line-height: 1.8;">
                    <?= htmlspecialchars($recette->instructions) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Boutons de modification/suppression (uniquement pour le créateur) -->
        <?php if(isset($_SESSION['user']) && $_SESSION['user']['id'] == $recette->user_id): ?>
            <div class="card-footer bg-white text-end p-3 border-top-0">
                <a href="/recipes/edit/<?= $recette->id ?>" class="btn btn-warning text-dark fw-bold px-4">✏️ Modifier</a>
                <form method="POST" action="/recipes/delete/<?= $recette->id ?>" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr ?');">🗑️</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>