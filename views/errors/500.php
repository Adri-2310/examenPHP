<?php
/**
 * Vue : errors/500.php
 *
 * Description : Page d'erreur 500 - Erreur serveur interne
 * Affiche un message convivial pour les erreurs serveur inattendues
 *
 * Variables attendues :
 * @var string $titre       Titre de la page (défaut: 'Erreur serveur')
 * @var string $message     Message d'erreur personnalisé (optionnel)
 * @var bool   $showDetails Afficher les détails techniques (optionnel, défaut: false)
 *
 * @package    Views\Errors
 * @created    2026
 */
?>

<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh;">
    <!-- Icône 500 -->
    <div class="text-center mb-5">
        <h1 class="display-1 fw-bold text-danger">500</h1>
        <p class="fs-3 fw-semibold">Erreur serveur interne</p>
    </div>

    <!-- Message d'erreur -->
    <div class="alert alert-danger d-flex align-items-center" role="alert" style="max-width: 500px;">
        <span class="me-2" style="font-size: 1.5rem;">🔥</span>
        <div>
            <strong>Une erreur interne s'est produite.</strong>
            <br><small class="text-muted">
                <?php if(isset($message)): ?>
                    <?= htmlspecialchars($message) ?>
                <?php else: ?>
                    Nos équipes techniques ont été notifiées. Veuillez réessayer ultérieurement.
                <?php endif; ?>
            </small>
        </div>
    </div>

    <!-- Détails techniques (optionnel, généralement en développement) -->
    <?php if(isset($showDetails) && $showDetails): ?>
        <div class="mt-4" style="max-width: 600px;">
            <details class="alert alert-dark">
                <summary class="cursor-pointer fw-semibold">Détails techniques (mode développement)</summary>
                <pre class="mt-3 text-start" style="font-size: 0.85rem; background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;"><code><?= htmlspecialchars($showDetails) ?></code></pre>
            </details>
        </div>
    <?php endif; ?>

    <!-- Boutons d'action -->
    <div class="mt-4 d-flex gap-3 flex-wrap justify-content-center">
        <a href="/" class="btn btn-primary btn-lg">
            <span>🏠</span> Retour à l'accueil
        </a>
        <button onclick="location.reload()" class="btn btn-outline-secondary btn-lg">
            <span>🔄</span> Réessayer
        </button>
    </div>

    <!-- Information additionnelle -->
    <div class="mt-5 text-center text-muted small">
        <p class="mb-2">
            <strong>Que s'est-il passé ?</strong>
        </p>
        <p>
            Une erreur inattenue s'est produite lors du traitement de votre requête.
            <br>Nos équipes techniques ont été notifiées et travaillent à la résolution du problème.
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
