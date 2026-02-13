<div class="container mt-4 mb-5">
    <a href="/recipes" class="btn btn-outline-secondary mb-4">⬅ Retour à mes recettes</a>
    
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white p-4">
            <h1 class="mb-0">👨‍🍳 <?= htmlspecialchars($recette->title) ?></h1>
            <p class="mt-2 mb-0 opacity-75">
                Créée le <?= date('d/m/Y', strtotime($recette->created_at)) ?>
            </p>
        </div>

        <?php if(!empty($recette->image_url)): ?>
            <img src="<?= $recette->image_url ?>" class="img-fluid w-100" style="max-height: 400px; object-fit: cover;" alt="Photo">
        <?php endif; ?>
        
        <div class="card-body p-4">
            <h4 class="text-primary mb-3">À propos de cette recette</h4>
            <p class="lead text-muted"><?= nl2br(htmlspecialchars($recette->description)) ?></p>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="text-primary mb-3">🛒 Ingrédients</h4>
                    <ul class="list-group list-group-flush shadow-sm">
                        <?php 
                        // Tes ingrédients sont stockés en JSON dans la BDD, on les retransforme en tableau (array)
                        $ingredients = json_decode($recette->ingredients, true);
                        
                        // Si ça a bien marché et que c'est un tableau, on les liste
                        if ($ingredients && is_array($ingredients)):
                            foreach($ingredients as $ingredient): 
                        ?>
                                <li class="list-group-item bg-light border-0 mb-1 rounded">
                                    ✅ <?= htmlspecialchars(trim($ingredient)) ?>
                                </li>
                        <?php 
                            endforeach; 
                        else:
                            // Sécurité au cas où ce ne serait pas du JSON (vieilles données)
                        ?>
                            <li class="list-group-item"><?= htmlspecialchars($recette->ingredients) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-md-8">
                    <h4 class="text-primary mb-3">🍳 Préparation</h4>
                    <div class="p-4 bg-light rounded shadow-sm" style="white-space: pre-wrap; line-height: 1.8;">
<?= htmlspecialchars($recette->instructions) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if(isset($_SESSION['user']) && $_SESSION['user']['id'] == $recette->user_id): ?>
            <div class="card-footer bg-white text-end p-3 border-top-0">
                <a href="/recipes/edit/<?= $recette->id ?>" class="btn btn-warning text-dark fw-bold px-4">✏️ Modifier</a>
                <a href="/recipes/delete/<?= $recette->id ?>" class="btn btn-danger px-4" onclick="return confirm('Es-tu sûr de vouloir supprimer cette recette définitivement ?')">🗑️ Supprimer</a>
            </div>
        <?php endif; ?>
    </div>
</div>