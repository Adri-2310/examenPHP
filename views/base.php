<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titre ?? 'Mon Site de Recettes' ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="/">🍽️ Marmiton-Exam</a>
                <div class="navbar-nav">
                    <a class="nav-link" href="/">Accueil</a>
                    <a class="nav-link" href="/recettes">Les Recettes</a>
                    <?php if(isset($_SESSION['user'])): ?>
                        <a class="nav-link" href="/users/logout">Déconnexion</a>
                    <?php else: ?>
                        <a class="nav-link" href="/users/login">Connexion</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container my-4">
        <?= $contenu ?>
    </main>

    <footer class="bg-light text-center py-3 mt-auto">
        <p>&copy; 2026 - Projet Examen PHP</p>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>