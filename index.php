<?php
/**
 * Nom du fichier : index.php
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
// __DIR__ pointe directement sur la racine du projet
define('ROOT', __DIR__);

// ===== ÉTAPE 1.5 : CHARGEMENT CENTRALISÉ DE LA CONFIGURATION =====
// Charge le .env dans $_ENV et définit les constantes d'environnement
// Permet une configuration unifiée et cohérente entre develop/main

// Charger le fichier .env dans $_ENV
$env = @parse_ini_file(ROOT . '/.env') ?: [];
foreach ($env as $key => $value) {
    $_ENV[$key] = $value;
}

// Charger la configuration par environnement (varie entre develop et main)
$appConfig = require ROOT . '/config/app.php';

// Définir les constantes d'environnement (utilisées partout dans l'app)
define('APP_ENV', $_ENV['APP_ENV'] ?? $appConfig['APP_ENV']);
define('APP_DEBUG', APP_ENV === 'development');

// Configuration PHP selon l'environnement
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ===== HEADERS DE SÉCURITÉ (différenciés par environnement) =====
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
// CSP moins restrictif pour production tout en gardant la sécurité
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:; connect-src 'self' https:;");
// HSTS seulement en production (HTTPS requis)
if (!APP_DEBUG) {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
}
header("Referrer-Policy: strict-origin-when-cross-origin");

// ===== ÉTAPE 2 : DÉMARRAGE DE LA SESSION =====
// Obligatoire pour :
// - L'authentification utilisateur ($_SESSION['user'])
// - Le token CSRF ($_SESSION['csrf_token'])
session_start(['cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true]);

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

// ===== ÉTAPE 4.5 : INITIALISATION DU GESTIONNAIRE D'ERREURS =====
// ErrorHandler gère le logging centralisé et l'affichage des erreurs
// Tous les controllers peuvent utiliser ErrorHandler::log() pour tracer les erreurs
use App\Core\ErrorHandler;

// ===== ÉTAPE 5 : INSTANCIATION DU ROUTEUR =====
// La classe Main gère le routage HTTP et dispatche vers les contrôleurs
$app = new App\Core\Main();

// ===== ÉTAPE 6 : DÉMARRAGE DE L'APPLICATION =====
// Analyse l'URL, instancie le contrôleur et exécute l'action demandée
$app->start();
