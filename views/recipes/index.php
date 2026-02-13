<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>👨‍🍳 Mes Propres Recettes</h1>
        <a href="/recipes/ajouter" class="btn btn-primary">➕ Créer une recette</a>
    </div>

    <div class="mb-4">
        <input type="text" id="search-perso" class="form-control" placeholder="🔍 Rechercher dans mes recettes (ex: Gâteau)...">
    </div>

    <?php if(empty($mesCreations)): ?>
        <div class="alert alert-info">
            Vous n'avez pas encore créé de recette. Lancez-vous !
        </div>
    <?php else: ?>
        <div class="row" id="recipes-list">
            <?php foreach($mesCreations as $recette): ?>
                <div class="col-md-4 mb-4 recipe-card">
                    <div class="card h-100 shadow-sm border-primary">
                        <div class="card-body">
                            <h5 class="card-title recipe-title"><?= htmlspecialchars($recette->title) ?></h5>
                            <p class="card-text text-muted small"><?= substr(htmlspecialchars($recette->description), 0, 80) ?>...</p>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <a href="/recipes/lire/<?= $recette->id ?>" class="btn btn-sm btn-info text-white">Voir</a>
                            <div>
                                <a href="/recipes/edit/<?= $recette->id ?>" class="btn btn-sm btn-warning">✏️</a>
                                
                                <a href="/recipes/delete/<?= $recette->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette recette définitivement ?');">🗑️</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('search-perso').addEventListener('keyup', function(e) {
    let recherche = e.target.value.toLowerCase();
    let cards = document.querySelectorAll('.recipe-card');
    
    cards.forEach(card => {
        let titre = card.querySelector('.recipe-title').textContent.toLowerCase();
        if(titre.includes(recherche)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>