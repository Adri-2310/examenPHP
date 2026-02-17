<?php
/**
 * Nom du fichier : Controller.php
 *
 * Description :
 * Classe de base pour tous les contrôleurs de l'application.
 * Implémente la méthode render() pour afficher les vues avec templating.
 *
 * Fonctionnalités principales :
 * - Rendu de vues avec variables automatiques
 * - Système de template avec layout principal (base.php)
 * - Utilisation du output buffering pour l'injection de contenu
 *
 * Implémentation :
 * - Tous les contrôleurs hériteront de cette classe
 * - Les vues sont situées dans /views/
 * - Le template par défaut est base.php
 *
 * @package    App\Core
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Core;

abstract class Controller
{
    /**
     * Affiche une vue avec données et template
     *
     * Cette méthode effectue le rendu d'une vue en passant les variables
     * extraites d'un tableau associatif. Elle utilise le output buffering
     * pour capturer le contenu de la vue et l'injecter dans le template.
     *
     * Processus de rendu :
     * 1. Extraction des données en variables locales
     * 2. Capture du contenu de la vue via output buffering
     * 3. Injection dans le template principal (header + contenu + footer)
     *
     * Exemple d'utilisation :
     * ```php
     * $this->render('recipes/lire', [
     *     'recette' => $recipe_object,
     *     'titre' => 'Ma recette'
     * ]);
     * ```
     *
     * @param string $fichier   Chemin de la vue relative (ex: 'main/index')
     * @param array $donnees    Données à passer à la vue (variables)
     * @param string $template  Fichier template parent (défaut: 'base')
     * @return void            Affiche le HTML rendu
     *
     * @note La variable $contenu est automatiquement disponible dans le template
     * @note Les données sont extraites en variables locales dans la portée de la vue
     */
    public function render(string $fichier, array $donnees = [], string $template = 'base')
    {
        // Extraction des données : chaque clé devient une variable
        // Exemple : ['titre' => 'Miam'] → variable locale $titre = 'Miam'
        extract($donnees);

        // Démarrage du buffering de sortie
        // Capture tout le HTML émis jusqu'à ob_get_clean()
        ob_start();

        // Inclusion de la vue
        // La vue a accès aux variables extraites et peut émettre du HTML
        require_once ROOT . '/views/' . $fichier . '.php';

        // Récupération et nettoyage du buffer
        // $contenu contiendra le HTML complètement rendu de la vue
        $contenu = ob_get_clean();

        // Inclusion du template principal
        // Le template a accès à $contenu et aux variables extraites
        // Généralement base.php qui inclut header.php et footer.php
        require_once ROOT . '/views/' . $template . '.php';
    }
}