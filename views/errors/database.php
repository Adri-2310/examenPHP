<?php
/**
 * Vue : errors/database.php
 *
 * Description : Page d'erreur base de données
 * Affiche un message convivial quand une erreur de base de données se produit
 *
 * Variables attendues :
 * @var string $titre       Titre de la page (défaut: 'Erreur base de données')
 * @var string $message     Message d'erreur personnalisé (optionnel)
 * @var string $action      Action qui a échouée (optionnel)
 * @var bool   $showDetails Afficher les détails techniques (optionnel, défaut: false)
 *
 * @package    Views\Errors
 * @created    2026
 */
?>

<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh;">
    <!-- Icône Erreur DB -->
    <div class="text-center mb-5">
        <h1 class="display-1 fw-bold text-danger">⚠️</h1>
        <p class="fs-3 fw-semibold">Erreur de base de données</p>
    </div>

    <!-- Message d'erreur -->
    <div class="alert alert-danger d-flex align-items-center" role="alert" style="max-width: 500px;">
        <span class="me-2" style="font-size: 1.5rem;">🗄️</span>
        <div>
            <strong>La base de données est temporairement indisponible.</strong>
            <br><small class="text-muted">
                <?php if(isset($message)): ?>
                    <?= htmlspecialchars($message) ?>
                <?php else: ?>
                    Veuillez réessayer dans quelques instants.
                <?php endif; ?>
            </small>
            <?php if(isset($action)): ?>
                <br><small class="text-muted">
                    Action : <code><?= htmlspecialchars($action) ?></code>
                </small>
            <?php endif; ?>
        </div>
    </div>

    <!-- Détails techniques (optionnel) -->
    <?php if(isset($showDetails) && $showDetails): ?>
        <div class="mt-4" style="max-width: 600px;">
            <details class="alert alert-dark">
                <summary class="cursor-pointer fw-semibold">Détails techniques (mode développement)</summary>
                <pre class="mt-3 text-start" style="font-size: 0.85rem; background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;"><code><?= htmlspecialchars($showDetails) ?></code></pre>
            </details>
        </div>
    <?php endif; ?>

    <!-- Explications détaillées -->
    <div class="mt-4 card" style="max-width: 500px; border: 1px solid var(--bs-border-color);">
        <div class="card-body">
            <h5 class="card-title">Ce qui s'est passé :</h5>
            <ul class="card-text mb-0 small">
                <li>La base de données est actuellement hors ligne</li>
                <li>Une surcharge du serveur a affecté les requêtes</li>
                <li>La connexion à la base de données a échouée</li>
                <li>Une maintenance est en cours</li>
            </ul>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="mt-4 d-flex gap-3 flex-wrap justify-content-center">
        <button onclick="location.reload()" class="btn btn-primary btn-lg">
            <span>🔄</span> Réessayer
        </button>
        <a href="/" class="btn btn-outline-secondary btn-lg">
            <span>🏠</span> Retour à l'accueil
        </a>
    </div>

    <!-- Information additionnelle -->
    <div class="mt-5 text-center text-muted small">
        <p class="mb-3">
            <strong>Nous travaillons à la résolution du problème.</strong>
        </p>
        <p>
            Nos administrateurs système ont été notifiés et travaillent à rétablir le service aussi rapidement que possible.
        </p>
        <p class="mt-3">
            Vous pouvez :
        </p>
        <div class="btn-group" role="group">
            <a href="/" class="btn btn-sm btn-outline-dark">Accueil</a>
            <a href="/contact/contact" class="btn btn-sm btn-outline-dark">Nous contacter</a>
        </div>
    </div>
</div>
