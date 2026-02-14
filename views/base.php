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
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Toastify-js pour les notifications -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- Styles du thème -->
    <style>
    [data-theme="dark"] {
        --bs-body-bg: #1a1a1a;
        --bs-body-color: #ffffff;
    }
    [data-theme="dark"] .card {
        background-color: #2a2a2a;
        color: #ffffff;
    }
    [data-theme="dark"] .navbar {
        background-color: #2a2a2a !important;
    }
    </style>
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
                    <a class="nav-link ms-3" href="/contact/contact">📧 Contact</a>
                    <a class="nav-link ms-3" href="/users/logout">Déconnexion</a>
                <?php else: ?>
                    <!-- Navigation pour visiteurs non connectés -->
                    <a class="nav-link" href="/users/login">Connexion</a>
                <?php endif; ?>
                <!-- Bouton de toggle thème -->
                    <button id="theme-toggle" class="btn btn-outline-secondary ms-2" title="Changer de thème">
                        <span id="theme-icon">🌙</span>
                    </button>
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
    <!-- Classes JavaScript personnalisées -->
    <script src="/js/classes/ThemeToggle.js"></script>
    <script src="/js/main.js"></script>
    <!-- Bibliothèque Toastify-js -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="/js/notification.js"></script>

    <!-- Affichage des notifications Toast -->
    <script>
        <?php
        // Affichage des toasts stockés en session
        if (isset($_SESSION['toasts']) && is_array($_SESSION['toasts'])):
            foreach ($_SESSION['toasts'] as $toast):
                $type = $toast['type'] ?? 'info'; // success, error, info
                $message = addslashes($toast['message'] ?? '');
                echo "Notifications.{$type}('{$message}');\n";
            endforeach;
            // Suppression des toasts après affichage
            unset($_SESSION['toasts']);
        endif;
        ?>
    </script>
</body>
</html>