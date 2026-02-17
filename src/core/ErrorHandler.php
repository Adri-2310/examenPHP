<?php
namespace App\Core;

/**
 * Classe ErrorHandler - Gestion centralisée des erreurs
 *
 * Description:
 * Gère le logging et l'affichage des erreurs de manière centralisée.
 * Enregistre les erreurs avec contexte dans un fichier log.
 * Affiche des messages génériques aux utilisateurs (sécurité).
 *
 * Fonctionnalités:
 * 1. Logging avec contexte (utilisateur, action, moment, fichier)
 * 2. Messages génériques pour l'utilisateur
 * 3. Messages détaillés pour le développeur (logs)
 * 4. Session-based error storage (pour affichage dans les vues)
 *
 * @package    App\Core
 * @author     Projet Examen PHP
 */
class ErrorHandler
{
    // Chemin du fichier de log
    private static $logFile = ROOT . '/logs/errors.log';

    // Types d'erreur
    const TYPE_ERROR = 'ERROR';
    const TYPE_WARNING = 'WARNING';
    const TYPE_INFO = 'INFO';

    /**
     * Enregistre une erreur dans le fichier de log avec contexte complet
     *
     * @param string $errorMessage  Le message d'erreur détaillé
     * @param string $type          Type d'erreur (ERROR, WARNING, INFO)
     * @param string $userMessage   Message à afficher à l'utilisateur (généralisé)
     * @param array $context        Contexte additionnel (fichier, ligne, action)
     * @return void
     */
    public static function log(
        string $errorMessage,
        string $type = self::TYPE_ERROR,
        ?string $userMessage = null,
        array $context = []
    ): void {
        // Créer le dossier logs s'il n'existe pas
        $logsDir = dirname(self::$logFile);
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0755, true);
        }

        // Construire le message de log avec contexte
        $logEntry = self::buildLogEntry($errorMessage, $type, $context);

        // Écrire dans le fichier de log
        @file_put_contents(self::$logFile, $logEntry . PHP_EOL, FILE_APPEND);

        // Si un message utilisateur est fourni, le stocker en session
        if ($userMessage) {
            self::storeUserMessage($userMessage, $type);
        }
    }

    /**
     * Construit une entrée de log formatée avec contexte
     *
     * @param string $errorMessage  Le message d'erreur
     * @param string $type          Type d'erreur
     * @param array $context        Contexte additionnel
     * @return string              Entrée de log formatée
     */
    private static function buildLogEntry(
        string $errorMessage,
        string $type,
        array $context
    ): string {
        // Date et heure
        $timestamp = date('Y-m-d H:i:s');

        // Récupérer le contexte utilisateur
        $userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 'anonymous';
        $userEmail = isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : 'N/A';

        // Récupérer l'action actuelle (depuis le contexte ou $_REQUEST)
        $action = $context['action'] ?? $_REQUEST['controller'] ?? 'unknown';
        $action .= '/' . ($context['method'] ?? $_REQUEST['action'] ?? 'unknown');

        // Récupérer le fichier et la ligne (depuis le contexte)
        $file = $context['file'] ?? 'N/A';
        $line = $context['line'] ?? 'N/A';

        // Construire le message structuré
        $logEntry = "[{$timestamp}] {$type}";
        $logEntry .= " - User: {$userEmail} (ID: {$userId})";
        $logEntry .= " - Action: {$action}";
        $logEntry .= " - File: {$file}:{$line}";
        $logEntry .= " - Message: {$errorMessage}";

        return $logEntry;
    }

    /**
     * Stocke un message utilisateur en session pour l'affichage dans les vues
     * Utilise le système de toast notifications
     *
     * @param string $message  Le message à afficher
     * @param string $type     Type de message (ERROR, WARNING, INFO)
     * @return void
     */
    private static function storeUserMessage(string $message, string $type): void
    {
        // Initialiser le tableau de toasts s'il n'existe pas
        if (!isset($_SESSION['toasts'])) {
            $_SESSION['toasts'] = [];
        }

        // Mapper le type d'erreur au type de toast
        $toastType = self::mapErrorTypeToToastType($type);

        // Ajouter le toast
        $_SESSION['toasts'][] = [
            'message' => $message,
            'type' => $toastType
        ];
    }

    /**
     * Mappe les types d'erreur ErrorHandler aux types de toast
     *
     * @param string $errorType  Type d'erreur (ERROR, WARNING, INFO)
     * @return string           Type de toast (error, warning, info, success)
     */
    private static function mapErrorTypeToToastType(string $errorType): string
    {
        switch ($errorType) {
            case self::TYPE_ERROR:
                return 'error';
            case self::TYPE_WARNING:
                return 'warning';
            case self::TYPE_INFO:
                return 'info';
            default:
                return 'error';
        }
    }

    /**
     * Enregistre une erreur de base de données
     * Logge l'erreur détaillée, affiche un message générique
     *
     * @param Exception $exception  L'exception levée
     * @param string $userAction    Ce que l'utilisateur essayait de faire
     * @param array $context        Contexte additionnel
     * @return void
     */
    public static function logDatabaseError(
        \Exception $exception,
        string $userAction = 'Opération base de données',
        array $context = []
    ): void {
        // Message détaillé pour le développeur
        $errorMessage = $exception->getMessage();

        // Message générique pour l'utilisateur
        $userMessage = "❌ Une erreur est survenue lors de : {$userAction}. Veuillez réessayer.";

        // Ajouter des détails au contexte
        $context['file'] = $exception->getFile();
        $context['line'] = $exception->getLine();
        $context['type'] = 'Database';

        // Enregistrer l'erreur
        self::log($errorMessage, self::TYPE_ERROR, $userMessage, $context);
    }

    /**
     * Enregistre une erreur d'API externe
     * Logge l'erreur détaillée, affiche un message générique
     *
     * @param Exception $exception  L'exception levée
     * @param string $apiName       Nom de l'API (ex: 'TheMealDB')
     * @param array $context        Contexte additionnel
     * @return void
     */
    public static function logApiError(
        \Exception $exception,
        string $apiName = 'API externe',
        array $context = []
    ): void {
        // Message détaillé pour le développeur
        $errorMessage = "API {$apiName}: " . $exception->getMessage();

        // Message générique pour l'utilisateur
        $userMessage = "❌ Le service {$apiName} est actuellement indisponible. Veuillez réessayer plus tard.";

        // Ajouter des détails au contexte
        $context['file'] = $exception->getFile();
        $context['line'] = $exception->getLine();
        $context['type'] = 'API';

        // Enregistrer l'erreur
        self::log($errorMessage, self::TYPE_ERROR, $userMessage, $context);
    }

    /**
     * Enregistre une erreur de fichier
     * Logge l'erreur détaillée, affiche un message générique
     *
     * @param string $errorMessage  Message d'erreur
     * @param string $operation     Opération effectuée (ex: 'upload', 'delete')
     * @param array $context        Contexte additionnel
     * @return void
     */
    public static function logFileError(
        string $errorMessage,
        string $operation = 'Opération fichier',
        array $context = []
    ): void {
        // Message générique pour l'utilisateur
        $userMessage = "❌ Une erreur est survenue lors du {$operation}. Veuillez réessayer.";

        // Ajouter des détails au contexte
        $context['type'] = 'File';
        $context['operation'] = $operation;

        // Enregistrer l'erreur
        self::log($errorMessage, self::TYPE_ERROR, $userMessage, $context);
    }

    /**
     * Enregistre une erreur de validation
     * Pour les cas où la validation échoue
     *
     * @param string $field         Champ qui a échoué la validation
     * @param string $reason        Raison de l'échec
     * @param string $userMessage   Message spécifique pour l'utilisateur
     * @return void
     */
    public static function logValidationError(
        string $field,
        string $reason,
        ?string $userMessage = null
    ): void {
        $errorMessage = "Validation failed for field '{$field}': {$reason}";

        if (!$userMessage) {
            $userMessage = "❌ Les données fournies ne sont pas valides. Veuillez vérifier et réessayer.";
        }

        $context = [
            'type' => 'Validation',
            'field' => $field
        ];

        self::log($errorMessage, self::TYPE_WARNING, $userMessage, $context);
    }

    /**
     * Enregistre une erreur d'autorisation/accès
     * Pour les cas où l'utilisateur n'a pas le droit d'accéder
     *
     * @param string $resource      Ressource auquel on essaie d'accéder
     * @param string $reason        Raison du refus
     * @return void
     */
    public static function logAccessDenied(
        string $resource,
        string $reason = 'Accès non autorisé'
    ): void {
        $errorMessage = "Access denied to {$resource}: {$reason}";
        $userMessage = "❌ Vous n'avez pas la permission d'accéder à cette ressource.";

        $context = [
            'type' => 'Security',
            'resource' => $resource
        ];

        self::log($errorMessage, self::TYPE_WARNING, $userMessage, $context);
    }

    /**
     * Retourne le chemin du fichier de log
     *
     * @return string  Chemin complet du fichier de log
     */
    public static function getLogFilePath(): string
    {
        return self::$logFile;
    }

    /**
     * Retourne le nombre de lignes du fichier de log
     *
     * @return int  Nombre de lignes
     */
    public static function getLogLineCount(): int
    {
        if (!file_exists(self::$logFile)) {
            return 0;
        }

        return count(file(self::$logFile, FILE_SKIP_EMPTY_LINES));
    }

    /**
     * Retourne les dernières erreurs du fichier de log
     *
     * @param int $lines  Nombre de lignes à retourner
     * @return array      Tableau des lignes de log
     */
    public static function getLastErrors(int $lines = 10): array
    {
        if (!file_exists(self::$logFile)) {
            return [];
        }

        $allLines = file(self::$logFile, FILE_SKIP_EMPTY_LINES);
        return array_slice($allLines, -$lines);
    }

    /**
     * Affiche une page d'erreur personnalisée et termine l'exécution
     *
     * @param int $statusCode   Code HTTP (404, 500, 403, etc.)
     * @param array $variables  Variables à passer à la vue d'erreur
     * @return void             Exit
     */
    public static function displayErrorPage(int $statusCode, array $variables = []): void
    {
        // Définir le code de réponse HTTP
        http_response_code($statusCode);

        // Déterminer le fichier de vue basé sur le code d'erreur
        $errorPages = [
            404 => 'errors/404',
            403 => 'errors/403',
            500 => 'errors/500',
            'database' => 'errors/database',
            'maintenance' => 'errors/maintenance'
        ];

        $viewFile = $errorPages[$statusCode] ?? 'errors/500';
        $viewPath = ROOT . '/views/' . $viewFile . '.php';

        // Titre par défaut si non fourni
        if (!isset($variables['titre'])) {
            $variables['titre'] = match ($statusCode) {
                404 => 'Page non trouvée',
                403 => 'Accès refusé',
                500 => 'Erreur serveur',
                default => 'Erreur'
            };
        }

        // Vérifier que le fichier de vue existe
        if (!file_exists($viewPath)) {
            http_response_code(500);
            die('<h1>Erreur serveur</h1><p>Page d\'erreur introuvable.</p>');
        }

        // Charger la vue d'erreur avec les variables fournies
        include $viewPath;
        exit;
    }

    /**
     * Affiche la page d'erreur de base de données avec layout complet
     *
     * @param Exception $exception  L'exception levée
     * @param string $action        Action qui a échouée
     * @param bool $showDetails     Afficher les détails techniques
     * @return void                 Exit
     */
    public static function displayDatabaseErrorPage(\Exception $exception, string $action = 'Opération', bool $showDetails = false): void
    {
        http_response_code(500);

        $variables = [
            'titre' => 'Erreur base de données',
            'message' => "Une erreur est survenue lors de : {$action}",
            'action' => $action
        ];

        // Ajouter les détails techniques en développement (si showDetails = true)
        if ($showDetails) {
            $variables['showDetails'] = $exception->getMessage() . "\n\nFichier: " . $exception->getFile() . "\nLigne: " . $exception->getLine();
        }

        // Charger le contenu de la vue d'erreur database
        extract($variables);
        ob_start();
        include ROOT . '/views/errors/database.php';
        $contenu = ob_get_clean();

        // Inclure le layout complet
        include ROOT . '/views/base.php';
        exit;
    }

    /**
     * Affiche une page d'erreur personnalisée avec le layout complet
     *
     * @param int $statusCode   Code HTTP (404, 500, 403, etc.)
     * @param array $variables  Variables à passer à la vue d'erreur
     * @return void             Exit
     */
    public static function displayErrorPageWithLayout(int $statusCode, array $variables = []): void
    {
        // Définir le code de réponse HTTP
        http_response_code($statusCode);

        // Déterminer le fichier de vue basé sur le code d'erreur
        $errorPages = [
            404 => 'errors/404',
            403 => 'errors/403',
            500 => 'errors/500',
            'database' => 'errors/database',
            'maintenance' => 'errors/maintenance'
        ];

        $viewFile = $errorPages[$statusCode] ?? 'errors/500';
        $viewPath = ROOT . '/views/' . $viewFile . '.php';

        // Titre par défaut si non fourni
        if (!isset($variables['titre'])) {
            $variables['titre'] = match ($statusCode) {
                404 => 'Page non trouvée',
                403 => 'Accès refusé',
                500 => 'Erreur serveur',
                default => 'Erreur'
            };
        }

        // Vérifier que le fichier de vue existe
        if (!file_exists($viewPath)) {
            http_response_code(500);
            die('<h1>Erreur serveur</h1><p>Page d\'erreur introuvable.</p>');
        }

        // Charger le contenu de la vue d'erreur
        extract($variables);
        ob_start();
        include $viewPath;
        $contenu = ob_get_clean();

        // Inclure le layout complet
        $layoutPath = ROOT . '/views/base.php';
        include $layoutPath;
        exit;
    }
}
