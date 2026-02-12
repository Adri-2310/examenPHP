<?php
namespace App;

class Autoloader
{
    static function register()
    {
        spl_autoload_register([
            __CLASS__,
            'autoload'
        ]);
    }

    static function autoload($class)
    {
        // On retire "App\" (notre namespace) du début
        $class = str_replace(__NAMESPACE__ . '\\', '', $class);
        
        // On remplace les antislashes par des slashes (pour le chemin fichier)
        $class = str_replace('\\', '/', $class);
        
        // On vérifie si le fichier existe dans /src/
        $fichier = __DIR__ . '/src/' . $class . '.php';
        
        if (file_exists($fichier)) {
            require_once $fichier;
        }
    }
}