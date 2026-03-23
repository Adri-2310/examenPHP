<?php
/**
 * Vue : users/profile.php
 *
 * Description : Page de profil de l'utilisateur connecté
 * Permet à l'utilisateur de consulter et modifier ses informations personnelles
 * ainsi que de changer son mot de passe.
 *
 * Variables attendues :
 * @var object      $user   Objet utilisateur récupéré depuis la base de données
 * @var string      $titre  Titre de la page
 *
 * Variables de session requises :
 * @var array $_SESSION['user']   Utilisateur connecté (vérification faite dans le contrôleur)
 *
 * Formulaires :
 * - Formulaire 1 : Mise à jour du profil → UsersController::updateProfile()
 * - Formulaire 2 : Changement de mot de passe → UsersController::changePassword()
 *
 * Validation :
 * - Côté client  : Attributs HTML5 required + pattern
 * - Côté serveur : Vérifications dans le contrôleur
 *
 * Sécurité :
 * - CSRF token sur les deux formulaires
 * - Vérification de l'ancien mot de passe avant tout changement
 *
 * @package    Views\Users
 * @created    2026
 */
?>
<div class="container mt-4 mb-5">

    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="profile-avatar">
            <span class="display-4">👤</span>
        </div>
        <div>
            <h1 class="mb-0">Mon Profil</h1>
            <p class="text-muted mb-0">Gérez vos informations personnelles et votre sécurité</p>
        </div>
    </div>

    <div class="row g-4">

        <!-- ===== CARTE : INFORMATIONS DU PROFIL ===== -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">✏️ Informations du compte</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/users/updateProfile" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <!-- Champ Nom -->
                        <div class="mb-3">
                            <label for="nom" class="form-label fw-semibold">Nom d'utilisateur</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nom"
                                name="nom"
                                value="<?= htmlspecialchars($user->nom ?? '') ?>"
                                minlength="3"
                                maxlength="100"
                                required
                                placeholder="Votre nom d'affichage"
                            >
                            <div class="form-text">Minimum 3 caractères.</div>
                        </div>

                        <!-- Champ Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">Adresse email</label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= htmlspecialchars($user->email ?? '') ?>"
                                required
                                placeholder="votre@email.com"
                            >
                            <div class="form-text">Utilisée pour vous connecter.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                💾 Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ===== CARTE : CHANGEMENT DE MOT DE PASSE ===== -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">🔒 Changer le mot de passe</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/users/changePassword" id="password-form" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <!-- Nouveau mot de passe -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-semibold">Nouveau mot de passe</label>
                            <input
                                type="password"
                                class="form-control"
                                id="new_password"
                                name="new_password"
                                minlength="8"
                                required
                                placeholder="Minimum 8 caractères"
                                autocomplete="new-password"
                            >
                            <!-- Barre de force du mot de passe -->
                            <div class="mt-2" id="password-strength-container" style="display:none;">
                                <div class="progress" style="height: 6px;">
                                    <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small id="password-strength-label" class="form-text"></small>
                            </div>
                            <div class="form-text">Minimum 8 caractères.</div>
                        </div>

                        <!-- Confirmation du nouveau mot de passe -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label fw-semibold">Confirmer le nouveau mot de passe</label>
                            <input
                                type="password"
                                class="form-control"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="8"
                                required
                                placeholder="Répétez votre nouveau mot de passe"
                                autocomplete="new-password"
                            >
                            <div id="password-match-feedback" class="form-text"></div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning" id="submit-password-btn">
                                🔑 Mettre à jour le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div><!-- /.row -->

</div><!-- /.container -->

<script>
/**
 * Évalue la force d'un mot de passe et retourne un score de 0 à 4
 * @param {string} password
 * @returns {{ score: number, label: string, colorClass: string }}
 */
function evaluatePasswordStrength(password) {
    let score = 0;
    if (password.length >= 8)  score++;
    if (password.length >= 12) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    const levels = [
        { label: 'Très faible', colorClass: 'bg-danger',  width: '20%' },
        { label: 'Faible',      colorClass: 'bg-warning', width: '40%' },
        { label: 'Moyen',       colorClass: 'bg-info',    width: '60%' },
        { label: 'Fort',        colorClass: 'bg-primary', width: '80%' },
        { label: 'Très fort',   colorClass: 'bg-success', width: '100%' }
    ];

    return { ...levels[Math.min(score, 4)], score };
}

// ===== INDICATEUR DE FORCE DU MOT DE PASSE =====
const newPasswordInput   = document.getElementById('new_password');
const strengthContainer  = document.getElementById('password-strength-container');
const strengthBar        = document.getElementById('password-strength-bar');
const strengthLabel      = document.getElementById('password-strength-label');

newPasswordInput.addEventListener('input', function () {
    const password = this.value;

    if (password.length === 0) {
        strengthContainer.style.display = 'none';
        return;
    }

    strengthContainer.style.display = 'block';
    const result = evaluatePasswordStrength(password);

    // Réinitialiser les classes de couleur
    strengthBar.className = 'progress-bar ' + result.colorClass;
    strengthBar.style.width = result.width;
    strengthLabel.textContent = 'Force : ' + result.label;
    strengthLabel.className = 'form-text text-' + result.colorClass.replace('bg-', '');
});

// ===== VÉRIFICATION DE CORRESPONDANCE DES MOTS DE PASSE EN TEMPS RÉEL =====
const confirmPasswordInput = document.getElementById('confirm_password');
const matchFeedback        = document.getElementById('password-match-feedback');
const submitBtn            = document.getElementById('submit-password-btn');

function checkPasswordMatch() {
    const newPwd     = newPasswordInput.value;
    const confirmPwd = confirmPasswordInput.value;

    if (confirmPwd.length === 0) {
        matchFeedback.textContent = '';
        matchFeedback.className   = 'form-text';
        return;
    }

    if (newPwd === confirmPwd) {
        matchFeedback.textContent = '✅ Les mots de passe correspondent.';
        matchFeedback.className   = 'form-text text-success';
    } else {
        matchFeedback.textContent = '❌ Les mots de passe ne correspondent pas.';
        matchFeedback.className   = 'form-text text-danger';
    }
}

newPasswordInput.addEventListener('input', checkPasswordMatch);
confirmPasswordInput.addEventListener('input', checkPasswordMatch);

// ===== VALIDATION AVANT SOUMISSION DU FORMULAIRE MOT DE PASSE =====
document.getElementById('password-form').addEventListener('submit', function (e) {
    const newPwd     = newPasswordInput.value;
    const confirmPwd = confirmPasswordInput.value;

    if (newPwd !== confirmPwd) {
        e.preventDefault();
        if (typeof Notifications !== 'undefined') {
            Notifications.error('Les mots de passe ne correspondent pas.');
        }
        confirmPasswordInput.focus();
        return;
    }

    if (newPwd.length < 8) {
        e.preventDefault();
        if (typeof Notifications !== 'undefined') {
            Notifications.error('Le nouveau mot de passe doit contenir au moins 8 caractères.');
        }
        newPasswordInput.focus();
    }
});
</script>
