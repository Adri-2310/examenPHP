<?php
/**
 * Vue : auth/login.php
 *
 * Description : Formulaire de connexion des utilisateurs
 * Permet à un utilisateur de se connecter avec email et mot de passe.
 *
 * Variables attendues :
 * @var string|null $erreur   Message d'erreur d'authentification (optionnel)
 *
 * Traitement :
 * - Soumission vers UsersController::login() (même URL en POST)
 * - Vérification email + password_verify() côté serveur
 * - Création de $_SESSION['user'] en cas de succès
 * - Redirection vers / en cas de succès
 *
 * Validation :
 * - Côté client : Champs required + type="email"
 * - Côté serveur : UsersController::login()
 *
 * @package    Views\Auth
 * @created    2026
 */
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Connexion</h3>
                </div>
                <div class="card-body">

                    <!-- Affichage des erreurs d'authentification -->
                    <?php if(isset($erreur)): ?>
                        <div class="alert alert-danger">
                            <?= $erreur ?>
                        </div>
                        <script>
                            Notifications.error('<?= addslashes($erreur) ?>');
                        </script>
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Se connecter</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Pas encore de compte ? <a href="/users/register">S'inscrire</a>
                </div>
            </div>
        </div>
    </div>
</div>