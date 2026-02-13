<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>❤️ Mes Favoris (Internet)</h1>
        <a href="/recipes/api" class="btn btn-outline-danger">🔍 Chercher l'inspiration</a>
    </div>

    <?php if(empty($favoris)): ?>
        <div class="alert alert-info">
            Vous n'avez pas encore de favoris. <a href="/recipes/api">Allez chercher de l'inspiration !</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($favoris as $fav): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-danger">
                        <img src="<?= $fav->image_url ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($fav->titre) ?></h5>
                        </div>
                        
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <a href="https://www.themealdb.com/meal/<?= $fav->id_api ?>" target="_blank" class="btn btn-sm btn-info text-white">Voir la recette ↗</a>
                            
                            <a href="/favorites/delete/<?= $fav->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Retirer des favoris ?')">🗑️ Retirer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>