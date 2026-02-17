<?php
/**
 * Vue : errors/403.php
 *
 * Description : Page d'erreur 403 - Accès refusé
 * Affiche un message convivial quand un utilisateur n'a pas les permissions nécessaires
 *
 * Variables attendues :
 * @var string $titre   Titre de la page (défaut: 'Accès refusé')
 * @var string $reason  Raison de l'accès refusé (optionnel)
 * @var string $resource Ressource concernée (optionnel)
 *
 * @package    Views\Errors
 * @created    2026
 */
?>

<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh;">
    <!-- Icône 403 -->
    <div class="text-center mb-5">
        <h1 class="display-1 fw-bold text-warning">403</h1>
        <p class="fs-3 fw-semibold">Accès refusé</p>
    </div>

    <!-- Message d'erreur -->
    <div class="alert alert-warning d-flex align-items-center" role="alert" style="max-width: 500px;">
        <span class="me-2" style="font-size: 1.5rem;">🚫</span>
        <div>
            <strong>Vous n'avez pas la permission d'accéder à cette ressource.</strong>
            <br><small class="text-muted">
                <?php if(isset($reason)): ?>
                    <?= htmlspecialchars($reason) ?>
                <?php else: ?>
                    Seuls les utilisateurs autorisés peuvent accéder à cette page.
                <?php endif; ?>
            </small>
            <?php if(isset($resource)): ?>
                <br><small class="text-muted">
                    Ressource : <code><?= htmlspecialchars($resource) ?></code>
                </small>
            <?php endif; ?>
        </div>
    </div>

    <!-- Explications détaillées -->
    <div class="mt-4 card" style="max-width: 500px; border: 1px solid var(--bs-border-color);">
        <div class="card-body">
            <h5 class="card-title">Raisons possibles :</h5>
            <ul class="card-text mb-0 small">
                <li>Vous n'êtes pas connecté à votre compte</li>
                <li>Votre compte n'a pas les permissions nécessaires</li>
                <li>La ressource appartient à un autre utilisateur</li>
                <li>Votre session a expiré</li>
            </ul>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="mt-4 d-flex gap-3 flex-wrap justify-content-center">
        <?php if(isset($_SESSION['user'])): ?>
            <!-- Utilisateur connecté : Retour à l'accueil -->
            <a href="/" class="btn btn-primary btn-lg">
                <span>🏠</span> Retour à l'accueil
            </a>
            <a href="/contact/contact" class="btn btn-outline-secondary btn-lg">
                <span>📧</span> Nous contacter
            </a>
        <?php else: ?>
            <!-- Utilisateur non connecté : Invitation à se connecter -->
            <a href="/users/login" class="btn btn-primary btn-lg">
                <span>🔐</span> Se connecter
            </a>
            <a href="/" class="btn btn-outline-secondary btn-lg">
                <span>🏠</span> Accueil
            </a>
        <?php endif; ?>
    </div>

    <!-- Information additionnelle -->
    <div class="mt-5 text-center text-muted small">
        <p class="mb-3">
            Si vous pensez qu'il s'agit d'une erreur, vous pouvez nous contacter.
        </p>
        <div class="btn-group" role="group">
            <?php if(isset($_SESSION['user'])): ?>
                <a href="/recipes" class="btn btn-sm btn-outline-dark">Mes Recettes</a>
                <a href="/favorites" class="btn btn-sm btn-outline-dark">Mes Favoris</a>
            <?php else: ?>
                <a href="/users/login" class="btn btn-sm btn-outline-dark">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</div>
