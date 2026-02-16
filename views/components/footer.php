<?php
/**
 * Composant : components/footer.php
 *
 * Description : Pied de page de l'application
 * Inclut le copyright et tous les scripts JavaScript
 *
 * @package    Views\Components
 * @created    2026
 */
?>
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
