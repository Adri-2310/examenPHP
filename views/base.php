<?php
/**
 * Vue : base.php
 *
 * Description : Template principal de l'application (layout)
 * Inclut le header, la navigation, le footer et charge les assets externes.
 * Toutes les autres vues sont chargées dans la section $contenu.
 *
 * Variables attendues :
 * @var string $contenu   Contenu HTML généré par la vue enfant (via ob_get_clean dans Controller)
 * @var string $titre     Titre de la page pour la balise <title> (défaut: 'Mon Site de Recettes')
 *
 * Variables de session utilisées :
 * @var array|null $_SESSION['user']   Utilisateur connecté (affecte la navigation)
 *   - Si connecté : Affiche "Mes Recettes", "Mes Favoris", "Inspiration API", "Déconnexion"
 *   - Si non connecté : Affiche uniquement "Accueil", "Connexion"
 *
 * Assets externes :
 * - Bootstrap 5.3.0 CSS (CDN jsdelivr)
 * - Bootstrap 5.3.0 JS Bundle (CDN jsdelivr)
 * - style.css local (/public/css/style.css)
 *
 * Structure :
 * 1. Header avec navigation conditionnelle (connecté/non connecté)
 * 2. Main : Zone de contenu dynamique ($contenu)
 * 3. Footer : Copyright
 *
 * @package    Views
 * @created    2026
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titre ?? 'Mon Site de Recettes' ?></title>

    <!-- Feuilles de style -->
    <link rel="stylesheet" href="/public/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header avec navigation -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="/">🍽️ Marmiton-Exam</a>
                <div class="navbar-nav">
                    <a class="nav-link" href="/">Accueil</a>

                <?php if(isset($_SESSION['user'])): ?>
                    <!-- Navigation pour utilisateurs connectés -->
                    <a class="nav-link text-primary" href="/recipes">👨‍🍳 Mes Recettes</a>
                    <a class="nav-link text-danger" href="/favorites">❤️ Mes Favoris</a>
                    <a class="nav-link text-success" href="/api">🌍 Inspiration API</a>

                    <a class="nav-link ms-3" href="/users/logout">Déconnexion</a>
                <?php else: ?>
                    <!-- Navigation pour visiteurs non connectés -->
                    <a class="nav-link" href="/users/login">Connexion</a>
                <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Zone de contenu dynamique (injectée par les vues enfants) -->
    <main class="container my-4">
        <?= $contenu ?>
    </main>

    <!-- Footer -->
    <footer class="bg-light text-center py-3 mt-auto">
        <p>&copy; 2026 - Projet Examen PHP</p>
    </footer>

    <!-- Scripts JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>