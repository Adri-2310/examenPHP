<?php
/**
 * Page de détail d'une recette TheMealDB
 * Affiche tous les détails: ingrédients, instructions, vidéo, etc.
 *
 * Variables disponibles:
 * - $recette: Objet avec les données de la recette API
 * - $titre: Titre de la page
 */
?>

<div class="container my-5">

    <!-- BOUTON RETOUR -->
    <div class="mb-4">
        <a href="/favorites" class="btn btn-outline-secondary btn-sm">
            ⬅ Retour à mes favoris
        </a>
    </div>

    <!-- TITRE ET BADGES -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-3"><?= $recette->title ?></h1>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-primary"><?= $recette->category ?></span>
                <span class="badge bg-info"><?= $recette->area ?></span>
                <span class="badge bg-secondary">Recette externe</span>
            </div>
        </div>
    </div>

    <!-- IMAGE PRINCIPALE -->
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <?php if (!empty($recette->image_url)): ?>
                <img src="<?= $recette->image_url ?>"
                     alt="<?= $recette->title ?>"
                     class="img-fluid rounded shadow"
                     loading="eager"
                     style="max-height: 450px; object-fit: cover; width: 100%;">
            <?php else: ?>
                <div class="bg-light rounded p-5 text-center">
                    <p class="text-muted">📷 Image non disponible</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- CONTENU PRINCIPAL: INGRÉDIENTS + PRÉPARATION -->
    <div class="row">

        <!-- INGRÉDIENTS (COLONNE GAUCHE) -->
        <div class="col-md-5 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📋 Ingrédients</h5>
                </div>
                <div class="card-body">
                    <?php
                        // Décoder les ingrédients depuis le JSON
                        $ingredients_json = is_string($recette->ingredients)
                            ? json_decode($recette->ingredients, true)
                            : $recette->ingredients;

                        if (is_array($ingredients_json) && !empty($ingredients_json)):
                    ?>
                        <ul class="list-unstyled">
                            <?php foreach ($ingredients_json as $index => $ingredient): ?>
                                <li class="mb-2 pb-2 <?php echo ($index !== count($ingredients_json) - 1) ? 'border-bottom' : ''; ?>">
                                    <strong><?= htmlspecialchars($ingredient['name'] ?? '') ?></strong>
                                    <?php if (!empty($ingredient['qty'])): ?>
                                        <br>
                                        <small class="text-muted">
                                            📏 <?= htmlspecialchars($ingredient['qty'] ?? '') ?>
                                        </small>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">❌ Aucun ingrédient disponible</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- INSTRUCTIONS (COLONNE DROITE) -->
        <div class="col-md-7 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">👨‍🍳 Préparation</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recette->instructions)): ?>
                        <div class="instructions-text">
                            <?= nl2br(htmlspecialchars($recette->instructions)) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">❌ Aucune instruction disponible</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>


</div>

<!-- STYLES PERSONNALISÉS -->
<style>
    .instructions-text {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        white-space: pre-wrap;
        word-wrap: break-word;
        line-height: 1.8;
        font-size: 0.95rem;
        border-left: 4px solid #28a745;
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }

    .card-header {
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .badge {
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .btn-outline-info:hover,
    .btn-outline-secondary:hover {
        transform: translateY(-1px);
    }

    ul li {
        transition: background-color 0.2s ease;
        padding-left: 0.5rem;
    }

    ul li:hover {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .instructions-text {
            padding: 1rem;
            font-size: 0.9rem;
        }

        .col-md-6,
        .col-md-5,
        .col-md-7 {
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 1.75rem;
        }

        .card-header h5 {
            font-size: 1.1rem;
        }
    }

    /* Dark mode support (optionnel) */
    @media (prefers-color-scheme: dark) {
        .instructions-text {
            background-color: rgba(248, 249, 250, 0.1);
            border-left-color: #20c997;
        }

        ul li:hover {
            background-color: rgba(248, 249, 250, 0.1);
        }
    }
</style>
