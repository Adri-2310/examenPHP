<?php
/**
 * Vue : base.php
 *
 * Description : Template principal de l'application (layout)
 * Inclut le header et footer via des composants séparés.
 * Toutes les autres vues sont chargées dans la section $contenu.
 *
 * Variables attendues :
 * @var string $contenu   Contenu HTML généré par la vue enfant (via ob_get_clean dans Controller)
 * @var string $titre     Titre de la page pour la balise <title> (défaut: 'Mon Site de Recettes')
 *
 * Structure :
 * 1. include components/header.php (head + navbar)
 * 2. Main : Zone de contenu dynamique ($contenu)
 * 3. include components/footer.php (footer + scripts)
 *
 * @package    Views
 * @created    2026
 */

// Inclure le header (head + navbar)
include __DIR__ . '/components/header.php';
?>

    <!-- Zone de contenu dynamique (injectée par les vues enfants) -->
    <main class="container my-4">
        <?= $contenu ?>
    </main>

<?php
// Inclure le footer (copyright + scripts)
include __DIR__ . '/components/footer.php';
?>