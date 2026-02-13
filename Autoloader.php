<?php
/**
 * Nom du fichier : Autoloader.php
 *
 * Description :
 * Système de chargement automatique des classes (autoloading PSR-4 simplifié).
 * Permet de charger automatiquement les fichiers de classes sans require manuel.
 *
 * Fonctionnalités principales :
 * - Enregistrement de la fonction d'autoloading
 * - Transformation namespace → chemin fichier
 * - Chargement automatique depuis le dossier /src/
 *
 * Pattern utilisé : PSR-4 Autoloading Standard (simplifié)
 * Namespace racine : App\ → /src/
 *
 * Exemple de transformation :
 * App\Core\Model → /src/Core/Model.php
 * App\Controllers\RecipesController → /src/Controllers/RecipesController.php
 *
 * @package    App
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App;

class Autoloader
{
    /**
     * Enregistre la fonction d'autoloading auprès de PHP
     *
     * Cette méthode doit être appelée une seule fois au démarrage de l'application.
     * Elle utilise spl_autoload_register() pour enregistrer la méthode autoload()
     * comme gestionnaire automatique de chargement de classes.
     *
     * Exemple d'utilisation :
     * ```php
     * require_once 'Autoloader.php';
     * App\Autoloader::register();
     * ```
     *
     * @return void
     */
    static function register()
    {
        spl_autoload_register([
            __CLASS__,
            'autoload'
        ]);
    }

    /**
     * Charge automatiquement une classe en fonction de son namespace
     *
     * Cette méthode est appelée automatiquement par PHP lorsqu'une classe inexistante
     * est référencée. Elle transforme le namespace complet en chemin de fichier.
     *
     * Processus de transformation (exemple avec App\Core\Model) :
     * 1. "App\Core\Model" → retire "App\" → "Core\Model"
     * 2. "Core\Model" → remplace \ par / → "Core/Model"
     * 3. "Core/Model" → ajoute /src/ et .php → "/src/Core/Model.php"
     *
     * Exemple d'utilisation automatique :
     * ```php
     * $model = new App\Core\Model();  // Charge automatiquement /src/Core/Model.php
     * ```
     *
     * @param string $class Nom complet de la classe avec namespace (FQCN)
     * @return void
     *
     * @note Le double str_replace() est nécessaire pour :
     *       1. Retirer le namespace racine (App\)
     *       2. Convertir les séparateurs de namespace (\) en séparateurs de chemin (/)
     */
    static function autoload($class)
    {
        // 1. Retrait du namespace racine "App\"
        // Exemple : "App\Core\Model" devient "Core\Model"
        $class = str_replace(__NAMESPACE__ . '\\', '', $class);

        // 2. Conversion des antislashes (namespace) en slashes (chemin fichier)
        // Exemple : "Core\Model" devient "Core/Model"
        $class = str_replace('\\', '/', $class);

        // 3. Construction du chemin absolu vers le fichier
        // Exemple : __DIR__ . '/src/' . 'Core/Model' . '.php'
        $fichier = __DIR__ . '/src/' . $class . '.php';

        // 4. Chargement du fichier si existant
        if (file_exists($fichier)) {
            require_once $fichier;
        }
    }
}