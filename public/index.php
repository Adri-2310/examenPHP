<?php
/**
 * Nom du fichier : public/index.php
 *
 * Description :
 * Point d'entrée unique de l'application (Front Controller).
 * Toutes les requêtes HTTP passent par ce fichier (via .htaccess ou configuration serveur).
 *
 * Fonctionnalités :
 * 1. Définition de la constante ROOT pour les chemins absolus
 * 2. Démarrage de la session PHP
 * 3. Génération du token CSRF (sécurité)
 * 4. Chargement de l'autoloader PSR-4
 * 5. Instanciation et démarrage du routeur MVC
 *
 * Sécurité :
 * - Token CSRF généré avec random_bytes(32) + bin2hex()
 * - Session démarrée pour l'authentification
 *
 * @package    Public
 * @author     Projet Examen PHP
 * @created    2026
 */

// ===== ÉTAPE 1 : DÉFINITION DU DOSSIER RACINE =====
// Permet d'utiliser des chemins absolus dans toute l'application
// dirname(__DIR__) remonte d'un niveau : /public → /
define('ROOT', dirname(__DIR__));

// ===== ÉTAPE 2 : DÉMARRAGE DE LA SESSION =====
// Obligatoire pour :
// - L'authentification utilisateur ($_SESSION['user'])
// - Le token CSRF ($_SESSION['csrf_token'])
session_start();

// ===== ÉTAPE 3 : GÉNÉRATION DU TOKEN CSRF =====
// Protection contre les attaques Cross-Site Request Forgery (CSRF)
// Le token est généré une seule fois par session et doit être inclus dans les formulaires sensibles
if (empty($_SESSION['csrf_token'])) {
    // random_bytes(32) : Génère 32 octets aléatoires cryptographiquement sûrs
    // bin2hex() : Convertit les octets en chaîne hexadécimale lisible (64 caractères)
    // Résultat : "a3f7e1c9d2b4... " (64 caractères hexadécimaux)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ===== ÉTAPE 4 : CHARGEMENT DE L'AUTOLOADER =====
// L'autoloader PSR-4 permet de charger automatiquement les classes
// sans avoir à faire des require_once manuels
require_once ROOT . '/Autoloader.php';
App\Autoloader::register();

// ===== ÉTAPE 5 : INSTANCIATION DU ROUTEUR =====
// La classe Main gère le routage HTTP et dispatche vers les contrôleurs
$app = new App\Core\Main();

// ===== ÉTAPE 6 : DÉMARRAGE DE L'APPLICATION =====
// Analyse l'URL, instancie le contrôleur et exécute l'action demandée
$app->start();