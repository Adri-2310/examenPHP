<div class="text-center mb-5">
    <h1>Bienvenue sur Marmiton-Exam 👨‍🍳</h1>
    <p class="lead">Découvrez nos dernières recettes délicieuses.</p>
</div>

<div class="row">
    <?php foreach($recettes as $recette): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="https://via.placeholder.com/300x200?text=Recette" class="card-img-top" alt="...">
                
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($recette->title) ?></h5>
                    <p class="card-text"><?= substr(htmlspecialchars($recette->description), 0, 100) ?>...</p>
                    <a href="/recettes/lire/<?= $recette->id ?>" class="btn btn-primary">Voir la recette</a>
                </div>
                <div class="card-footer text-muted">
                    <small>Publié le <?= date('d/m/Y', strtotime($recette->created_at)) ?></small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if(empty($recettes)): ?>
        <div class="alert alert-info">
            Aucune recette pour le moment. Allez dans la BDD pour en ajouter une manuellement !
        </div>
    <?php endif; ?>
</div>