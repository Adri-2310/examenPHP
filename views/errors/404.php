<?php
/**
 * Vue : errors/404.php
 *
 * Description : Page d'erreur 404 - Page non trouvée
 * Affiche un message convivial avec un lien de retour à l'accueil
 *
 * Variables attendues :
 * @var string $titre   Titre de la page (défaut: 'Page non trouvée')
 * @var string $url     L'URL qui n'a pas pu être trouvée (optionnel)
 *
 * @package    Views\Errors
 * @created    2026
 */
?>

<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh;">
    <!-- Icône 404 -->
    <div class="text-center mb-5">
        <h1 class="display-1 fw-bold text-danger">404</h1>
        <p class="fs-3 fw-semibold">Oups ! Page non trouvée</p>
    </div>

    <!-- Message d'erreur -->
    <div class="alert alert-warning d-flex align-items-center" role="alert" style="max-width: 500px;">
        <span class="me-2" style="font-size: 1.5rem;">⚠️</span>
        <div>
            <strong>La page que vous recherchez n'existe pas.</strong>
            <br><small class="text-muted">
                <?php if(isset($url)): ?>
                    URL demandée : <code><?= htmlspecialchars($url) ?></code>
                <?php else: ?>
                    Vérifiez l'URL ou retournez à l'accueil.
                <?php endif; ?>
            </small>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="mt-4 d-flex gap-3 flex-wrap justify-content-center">
        <a href="/" class="btn btn-primary btn-lg">
            <span>🏠</span> Retour à l'accueil
        </a>
        <button onclick="history.back()" class="btn btn-outline-secondary btn-lg">
            <span>⬅️</span> Page précédente
        </button>
    </div>

    <!-- Suggestion -->
    <div class="mt-5 text-center text-muted small">
        <p>
            Vous pouvez aussi explorer nos sections :
        </p>
        <div class="btn-group" role="group">
            <?php if(isset($_SESSION['user'])): ?>
                <a href="/recipes" class="btn btn-sm btn-outline-dark">Mes Recettes</a>
                <a href="/favorites" class="btn btn-sm btn-outline-dark">Favoris</a>
                <a href="/api" class="btn btn-sm btn-outline-dark">Inspiration</a>
            <?php else: ?>
                <a href="/users/login" class="btn btn-sm btn-outline-dark">Se connecter</a>
            <?php endif; ?>
        </div>
    </div>
</div>
