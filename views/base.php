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
    <!-- Bootstrap 5.3.0 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastify-js pour les notifications -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <!-- CSS personnalisé (inclut les thèmes light/dark et styles globaux) -->
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- Header avec navigation -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-center">
            <div class="container-fluid px-3 navbar-content">
                <!-- Logo + Bouton thème (à gauche) -->
                <div class="d-flex align-items-center gap-2">
                    <a class="navbar-brand fw-bold" href="/">🍽️ Marmiton-Exam</a>
                    <button id="theme-toggle" class="btn btn-outline-secondary" title="Changer de thème">
                        <span id="theme-icon">🌙</span>
                    </button>
                </div>

                <!-- Menu collapse centré (au milieu en desktop, collapsible en mobile) -->
                <div class="collapse navbar-collapse flex-grow-0" id="navbarNav">
                    <div class="navbar-nav navbar-center-items">
                        <a class="nav-link" href="/">Accueil</a>

                        <?php if(isset($_SESSION['user'])): ?>
                            <!-- Navigation pour utilisateurs connectés -->
                            <a class="nav-link" href="/recipes">👨‍🍳 Mes Recettes</a>
                            <a class="nav-link" href="/favorites">❤️ Mes Favoris</a>
                            <a class="nav-link" href="/api">🌍 Inspiration</a>
                            <a class="nav-link" href="/contact/contact">📧 Contact</a>
                            <a class="nav-link" href="/users/logout">Déconnexion</a>
                        <?php else: ?>
                            <!-- Navigation pour visiteurs non connectés -->
                            <a class="nav-link" href="/users/login">Connexion</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Burger (à droite en mobile seulement) -->
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
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
    <!-- Point d'entrée JavaScript principal -->
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