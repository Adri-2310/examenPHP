<div class="container mt-4 mb-5">
    <h1>Ajouter une nouvelle recette 🍳</h1>
    <a href="/recipes" class="btn btn-outline-secondary mb-3">⬅ Retour</a>
    
    <?php if(isset($erreur)): ?>
        <div class="alert alert-danger"><?= $erreur ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-4 mt-2">
        <form method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Titre de la recette</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Ex: Gratin Dauphinois" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Courte description</label>
                <textarea class="form-control" id="description" name="description" rows="2" placeholder="Ex: Un plat familial réconfortant..." required></textarea>
            </div>

            <div class="mb-3">
                <label for="ingredients" class="form-label">Ingrédients (séparés par des virgules)</label>
                <input type="text" class="form-control" id="ingredients" name="ingredients" placeholder="Ex: Pommes de terre, Crème, Ail, Beurre" required>
                <div class="form-text text-primary">⚠️ Séparez bien chaque ingrédient par une virgule.</div>
            </div>

            <div class="mb-3">
                <label for="instructions" class="form-label">Étapes de préparation</label>
                <textarea class="form-control" id="instructions" name="instructions" rows="6" placeholder="1. Éplucher les pommes de terre..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">💾 Enregistrer ma recette</button>
        </form>
    </div>
</div>