<?php
/**
 * Composant : components/header.php
 *
 * Description : En-tête de l'application (head + navbar)
 * Inclut tous les assets CSS et la navigation principale
 *
 * Variables attendues :
 * @var string $titre   Titre de la page (défaut: 'Mon Site de Recettes')
 *
 * Variables de session utilisées :
 * @var array|null $_SESSION['user']   Utilisateur connecté (affecte la navigation)
 *
 * @package    Views\Components
 * @created    2026
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titre ?? 'Mon Site de Recettes' ?></title>

    <!-- Favicon emoji -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='75' font-size='75'>🍽️</text></svg>">

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
