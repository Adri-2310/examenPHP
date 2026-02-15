<?php
/**
 * Vue : auth/register.php
 *
 * Description : Formulaire d'inscription des nouveaux utilisateurs
 * Permet de créer un nouveau compte avec nom, email et mot de passe.
 *
 * Variables attendues :
 * @var string|null $erreur   Message d'erreur d'inscription (email déjà utilisé, champs incomplets)
 *
 * Traitement :
 * - Soumission vers UsersController::register() (même URL en POST)
 * - Hashing du mot de passe avec PASSWORD_ARGON2ID
 * - Vérification d'unicité de l'email
 * - Création du compte avec rôle ROLE_USER par défaut
 * - Redirection vers /users/login en cas de succès
 *
 * Validation :
 * - Côté client : Champs required + type="email"
 * - Côté serveur : UsersController::register()
 *
 * @package    Views\Auth
 * @created    2026
 */
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Inscription</h3>
                </div>
                <div class="card-body">

                    <!-- Affichage des erreurs d'inscription -->
                    <?php if(isset($erreur)): ?>
                        <div class="alert alert-danger">
                            <?= $erreur ?>
                        </div>
                        <script>
                            Notifications.error('<?= addslashes($erreur) ?>');
                        </script>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Votre Nom (ou Pseudo)</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                            <div class="invalid-feedback d-block" style="display: none; color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">
                                Veuillez entrer un nom.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            <div class="invalid-feedback d-block" style="display: none; color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">
                                Veuillez entrer une adresse email valide.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback d-block" style="display: none; color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem;">
                                Le mot de passe doit contenir au moins 8 caractères.
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">S'inscrire</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Déjà un compte ? <a href="/users/login">Se connecter</a>
                </div>
            </div>
        </div>
    </div>
</div>