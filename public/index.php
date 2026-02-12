<?php
// 1. Définir le dossier racine (pour les includes faciles)
define('ROOT', dirname(__DIR__));

// 2. Démarrer la session (OBLIGATOIRE pour Partie 05)
session_start();

// 3. Autoloader (BONUS Partie 09 & 10)
// Cela permet d'utiliser "use App\Controllers\MainController"
require_once ROOT . '/Autoloader.php'; 
App\Autoloader::register();

// 4. On instancie le Routeur (on va le créer juste après)
$app = new App\Core\Main();

// 5. On démarre l'application
$app->start();