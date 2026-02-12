<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Inscription</h3>
                </div>
                <div class="card-body">
                    
                    <?php if(isset($erreur)): ?>
                        <div class="alert alert-danger">
                            <?= $erreur ?>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Votre Nom (ou Pseudo)</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
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