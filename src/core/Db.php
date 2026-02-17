<?php
/**
 * Nom du fichier : Db.php
 *
 * Description :
 * Gestionnaire de connexion à la base de données MySQL.
 * Implémente le pattern Singleton pour garantir une seule instance de connexion.
 * Étend la classe PDO pour une gestion sécurisée des requêtes.
 *
 * Fonctionnalités principales :
 * - Singleton thread-safe
 * - Chargement des configuration depuis .env
 * - Gestion des erreurs PDO
 * - Configuration UTF-8 et mode FETCH_OBJ
 *
 * Configuration :
 * - DB_HOST : serveur MySQL (défaut: localhost)
 * - DB_USER : utilisateur MySQL (défaut: root)
 * - DB_PASS : mot de passe MySQL (défaut: vide)
 * - DB_NAME : nom base de données (défaut: examenphp)
 *
 * Fichier .env requis : /root/.env
 *
 * @package    App\Core
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Core;

use PDO;
use PDOException;

// Charger les variables d'environnement depuis le fichier .env
$env = parse_ini_file(__DIR__ . '/../../.env') ?: [];

class Db extends PDO
{
    /**
     * Instance unique de la classe (Singleton)
     * @var Db|null
     */
    private static $instance;

    /**
     * Serveur MySQL (défaut: localhost)
     * @var string
     */
    private static $dbhost;

    /**
     * Utilisateur MySQL (défaut: root)
     * @var string
     */
    private static $dbuser;

    /**
     * Mot de passe MySQL (défaut: vide)
     * @var string
     */
    private static $dbpass;

    /**
     * Nom de la base de données (défaut: examenphp)
     * @var string
     */
    private static $dbname;

    /**
     * Charge les paramètres de connexion depuis le fichier .env
     *
     * Cette méthode privée charge les variables d'environnement qui ont été
     * lues au début du fichier. Elle fournit des valeurs par défaut si les
     * variables ne sont pas définies.
     *
     * Valeurs par défaut (si .env absent) :
     * - DB_HOST : localhost
     * - DB_USER : root
     * - DB_PASS : (vide)
     * - DB_NAME : examenphp
     *
     * @return void
     *
     * @note Cette méthode utilise la variable globale $env
     */
    private static function loadConfig(): void
    {
        global $env;
        self::$dbhost = $env['DB_HOST'] ?? 'localhost';
        self::$dbuser = $env['DB_USER'] ?? 'root';
        self::$dbpass = $env['DB_PASS'] ?? '';
        self::$dbname = $env['DB_NAME'] ?? 'examenphp';
    }

    /**
     * Constructeur privé - Initialise la connexion PDO
     *
     * Ce constructeur est privé pour forcer l'utilisation du pattern Singleton
     * via getInstance(). Il initialise la connexion PDO avec les paramètres
     * chargés et configure les attributs PDO pour le bon fonctionnement.
     *
     * Configuration appliquée :
     * 1. MYSQL_ATTR_INIT_COMMAND : SET NAMES utf8 (encodage)
     * 2. ATTR_DEFAULT_FETCH_MODE : FETCH_OBJ (retourne des objets, pas tableaux)
     * 3. ATTR_ERRMODE : ERRMODE_EXCEPTION (lève des exceptions en cas d'erreur)
     *
     * @throws PDOException En cas d'échec de connexion
     *
     * @note Les erreurs sont loggées et un message générique est affiché à l'utilisateur
     */
    private function __construct()
    {
        // Construction du DSN (Data Source Name) de connexion MySQL
        // Format : mysql:dbname={nom_bd};host={serveur}
        $_dsn = 'mysql:dbname=' . self::$dbname . ';host=' . self::$dbhost;

        // Initialisation de la connexion PDO
        try {
            // Appel du constructeur parent (PDO)
            parent::__construct($_dsn, self::$dbuser, self::$dbpass);

            // Configuration de l'encodage UTF-8
            $this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');

            // Mode de récupération : retourner des objets (stdClass) au lieu de tableaux
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

            // Mode d'erreur : lever une exception en cas d'erreur SQL
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $e) {
            // Enregistrement de l'erreur technique dans les logs serveur
            error_log("Erreur de connexion DB : " . $e->getMessage());

            // Affichage d'un message générique à l'utilisateur (ne révèle pas les détails technique)
            die("Erreur de connexion à la base de données. Veuillez réessayer ultérieurement.");
        }
    }

    /**
     * Récupère l'instance unique de connexion (Singleton)
     *
     * Cette méthode implémente le pattern Singleton pour garantir qu'une seule
     * instance de connexion à la base de données est créée et réutilisée dans
     * toute l'application. Cela économise les ressources et évite les problèmes
     * de connexions multiples.
     *
     * Utilisation :
     * ```php
     * $db = Db::getInstance();
     * $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
     * ```
     *
     * @return self Instance unique de connexion PDO
     *
     * @note Lazy loading : la connexion n'est créée qu'au premier appel
     */
    public static function getInstance(): self
    {
        // Vérification : création initiale si null
        if(self::$instance === null){
            // Chargement des paramètres de configuration
            self::loadConfig();
            // Création de l'instance unique
            self::$instance = new self();
        }

        // Retour toujours de la même instance
        return self::$instance;
    }
}