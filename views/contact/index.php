<?php
/**
 * Vue : contact/index.php
 *
 * Description : Formulaire de contact
 * Permet aux utilisateurs de vous envoyer des messages.
 *
 * Variables attendues :
 * @var string|null $erreur   Message d'erreur de validation (optionnel)
 * @var string $titre         Titre de la page
 *
 * Validation :
 * - Côté client : Champs required + type="email"
 * - Côté serveur : ContactController::contact()
 *
 * @package    Views
 * @created    2026
 */
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-1">📧 Nous contacter</h1>
            <p class="text-muted mb-4">Avez-vous une question ou un commentaire ? N'hésitez pas à nous écrire !</p>

            <!-- Affichage des erreurs de validation -->
            <?php if(isset($erreur)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>⚠️ Erreur :</strong> <?= htmlspecialchars($erreur) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                <script>
                    Notifications.error('<?= addslashes($erreur) ?>');
                </script>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="/contact/contact">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="mb-3">
                            <label for="nom" class="form-label">Votre nom *</label>
                            <input type="text" class="form-control" id="nom" name="nom"
                                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                                   placeholder="Ex: Jean Dupont" required>
                            <small class="text-muted">Minimum 2 caractères</small>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Votre email *</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   placeholder="exemple@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="sujet" class="form-label">Sujet *</label>
                            <input type="text" class="form-control" id="sujet" name="sujet"
                                   value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>"
                                   placeholder="Ex: Question sur une recette" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control" id="message" name="message"
                                      rows="6" placeholder="Votre message ici..."
                                      required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                            <small class="text-muted d-block mt-1">Minimum 10 caractères</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                ✉️ Envoyer le message
                            </button>
                            <a href="/" class="btn btn-outline-secondary">⬅ Retour à l'accueil</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information de support -->
            <div class="alert alert-info mt-4">
                <strong>💡 Conseil :</strong> Soyez précis et détaillez votre question. Nous répondons généralement dans les 24 heures.
            </div>
        </div>
    </div>
</div>
