<?php
/**
 * Vue : errors/maintenance.php
 *
 * Description : Page de maintenance
 * Affiche un message convivial quand le site est en maintenance
 *
 * Variables attendues :
 * @var string $titre           Titre de la page (défaut: 'Maintenance en cours')
 * @var string $message         Message de maintenance personnalisé (optionnel)
 * @var string $estimatedTime   Durée estimée de la maintenance (optionnel)
 * @var string $contactEmail    Email de contact (optionnel)
 *
 * @package    Views\Errors
 * @created    2026
 */
?>

<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh;">
    <!-- Icône Maintenance -->
    <div class="text-center mb-5">
        <h1 style="font-size: 4rem;">🔧</h1>
        <p class="fs-3 fw-semibold">Maintenance en cours</p>
    </div>

    <!-- Message de maintenance -->
    <div class="alert alert-info d-flex align-items-center" role="alert" style="max-width: 550px;">
        <span class="me-2" style="font-size: 1.5rem;">ℹ️</span>
        <div>
            <strong>Nous améliorons notre plateforme pour vous.</strong>
            <br><small class="text-muted">
                <?php if(isset($message)): ?>
                    <?= htmlspecialchars($message) ?>
                <?php else: ?>
                    Le site sera de retour très bientôt avec de nouvelles améliorations !
                <?php endif; ?>
            </small>
        </div>
    </div>

    <!-- Durée estimée -->
    <?php if(isset($estimatedTime)): ?>
        <div class="mt-4 card bg-light" style="max-width: 500px; border: 1px solid var(--bs-border-color);">
            <div class="card-body text-center">
                <h6 class="card-title mb-3">⏱️ Durée estimée</h6>
                <p class="card-text fw-semibold mb-0">
                    <?= htmlspecialchars($estimatedTime) ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Détails de la maintenance -->
    <div class="mt-4 card" style="max-width: 500px; border: 1px solid var(--bs-border-color);">
        <div class="card-body">
            <h5 class="card-title">Ce que nous faisons :</h5>
            <ul class="card-text mb-0 small">
                <li>Amélioration des performances</li>
                <li>Ajout de nouvelles fonctionnalités</li>
                <li>Mise à jour de sécurité</li>
                <li>Maintenance des serveurs</li>
            </ul>
        </div>
    </div>

    <!-- Email de notification -->
    <div class="mt-4 text-center small">
        <p class="text-muted">
            Nous vous enverrons un email quand le site sera de retour en ligne.
        </p>
        <?php if(isset($contactEmail)): ?>
            <p class="text-muted">
                Contact : <a href="mailto:<?= htmlspecialchars($contactEmail) ?>">
                    <?= htmlspecialchars($contactEmail) ?>
                </a>
            </p>
        <?php endif; ?>
    </div>

    <!-- Boutons d'action -->
    <div class="mt-4 d-flex gap-3 flex-wrap justify-content-center">
        <button onclick="location.reload()" class="btn btn-primary btn-lg">
            <span>🔄</span> Vérifier à nouveau
        </button>
        <a href="#" onclick="history.back(); return false;" class="btn btn-outline-secondary btn-lg">
            <span>⬅️</span> Page précédente
        </a>
    </div>

    <!-- Information additionnelle -->
    <div class="mt-5 text-center text-muted small">
        <p class="mb-3">
            <strong>Merci de votre patience ! 🙏</strong>
        </p>
        <p>
            Pendant ce temps, vous pouvez nous suivre sur nos réseaux sociaux pour les mises à jour ou nous contacter directement.
        </p>
        <p class="mt-3">
            Options :
        </p>
        <div class="btn-group" role="group">
            <a href="/contact/contact" class="btn btn-sm btn-outline-dark">Nous contacter</a>
            <button onclick="window.open('https://twitter.com', '_blank')" class="btn btn-sm btn-outline-dark">Twitter</button>
        </div>
    </div>

    <!-- Statistique de maintenance -->
    <div class="mt-5 text-center">
        <small class="text-muted">
            Dernière vérification : <span id="lastCheck" class="fw-semibold">à l'instant</span>
        </small>
    </div>
</div>

<script>
    // Mettre à jour l'heure de dernière vérification
    document.addEventListener('DOMContentLoaded', function() {
        const lastCheckEl = document.getElementById('lastCheck');

        setInterval(function() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('fr-FR');
            lastCheckEl.textContent = 'à ' + timeStr;
        }, 1000);
    });
</script>
