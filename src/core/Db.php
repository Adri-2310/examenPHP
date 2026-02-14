<?php
namespace App\Core;

// On "étend" PDO, donc notre classe Db EST une connexion PDO
use PDO;
use PDOException;

// Charger les variables d'environnement depuis le fichier .env
$env = parse_ini_file(__DIR__ . '/../../.env') ?: [];

class Db extends PDO
{
    // Instance unique de la classe
    private static $instance;
    

    // Informations de connexion chargées depuis le fichier .env avec valeurs par défaut
    private static $dbhost;
    private static $dbuser;
    private static $dbpass;
    private static $dbname;
    
    /**
     * Charge les paramètres de connexion depuis le fichier .env
     *
     * @return void
     */
    private static function loadConfig(): void
    {
        global $env;
        self::$dbhost = $env['DB_HOST'] ?? 'localhost';
        self::$dbuser = $env['DB_USER'] ?? 'root';
        self::$dbpass = $env['DB_PASS'] ?? '';
        self::$dbname = $env['DB_NAME'] ?? 'examenphp';
    }

    private function __construct()
    {
        // DSN de connexion
        $_dsn = 'mysql:dbname=' . self::$dbname . ';host=' . self::$dbhost;

        // On appelle le constructeur de la classe PDO
        try {
            parent::__construct($_dsn, self::$dbuser, self::$dbpass);

            $this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
            $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); // On veut des objets, pas des tableaux
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if(self::$instance === null){
            self::loadConfig();
            self::$instance = new self();
        }
        return self::$instance;
    }
}