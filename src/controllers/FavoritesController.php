<?php
/**
 * Nom du fichier : FavoritesController.php
 *
 * Description :
 * Contrôleur gérant les recettes favorites provenant de l'API TheMealDB.
 * Permet aux utilisateurs de sauvegarder, afficher et supprimer leurs favoris.
 *
 * Fonctionnalités principales :
 * - Affichage de la liste des favoris de l'utilisateur
 * - Ajout d'une recette API aux favoris (avec vérification de doublons)
 * - Suppression d'un favori
 *
 * Sécurité :
 * - Vérification de connexion utilisateur
 * - Contrôle de propriété (user_id) avant suppression
 * - Prévention des doublons avec exists()
 * - Requêtes préparées
 *
 * @package    App\Controllers
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ErrorHandler;
use App\Models\FavoritesModel;

class FavoritesController extends Controller
{
    /**
     * Affiche la page "Mes Favoris" avec toutes les recettes sauvegardées
     *
     * Cette méthode récupère et affiche tous les favoris de l'utilisateur connecté.
     * Page accessible uniquement aux utilisateurs authentifiés.
     *
     * @return void Affiche la vue favorites/index.php
     *
     * @security Redirection vers /users/login si non connecté
     */
    public function index()
    {
        // Vérification de la connexion utilisateur
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        try {
            // Récupération des favoris de l'utilisateur
            $favModel = new FavoritesModel();
            $favoris = $favModel->findAllByUserId($_SESSION['user']['id']);

            // Log succès
            ErrorHandler::log(
                "Liste des favoris chargée (" . count($favoris) . " recettes)",
                ErrorHandler::TYPE_INFO,
                null,
                ['action' => 'favorites/index', 'method' => 'findAllByUserId']
            );

            // Affichage de la vue
            $this->render('favorites/index', ['favoris' => $favoris, 'titre' => 'Mes Favoris']);
        } catch (\PDOException $e) {
            // Erreur BD
            ErrorHandler::logDatabaseError($e, 'chargement des favoris', [
                'action' => 'favorites/index',
                'method' => 'findAllByUserId'
            ]);

            header('Location: /');
            exit;
        }
    }

    /**
     * Ajoute une recette API aux favoris de l'utilisateur
     *
     * Cette méthode traite l'ajout d'une recette provenant de TheMealDB
     * dans la liste des favoris personnels de l'utilisateur.
     *
     * Données reçues via POST :
     * - id_api : Identifiant de la recette dans TheMealDB
     * - titre : Nom de la recette
     * - image_url : URL de l'image de la recette
     *
     * Processus :
     * 1. Vérification d'existence pour éviter les doublons
     * 2. Insertion en base de données
     * 3. Redirection vers /favorites
     *
     * @return void Redirige vers /favorites
     *
     * @security Vérification de connexion (exit si non connecté)
     * @security Vérification de doublons avec exists()
     * @security Requête préparée contre injection SQL
     */
    public function add()
    {
        // Vérification de la connexion (exit silencieux pour appel AJAX)
        if (!isset($_SESSION['user'])) exit;

        // ===== TRAITEMENT DE L'AJOUT =====
        if (!empty($_POST)) {
            // Validation du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                ErrorHandler::logAccessDenied('favorites/add', 'Token CSRF invalide');
                die("Erreur de sécurité : Token CSRF invalide");
            }

            try {
                $favModel = new FavoritesModel();

                // Vérification de doublons : évite d'ajouter 2 fois la même recette
                if (!$favModel->exists($_SESSION['user']['id'], $_POST['id_api'])) {
                    // SÉCURITÉ : Nettoyage et validation des données provenant de l'API externe
                    $titre = strip_tags($_POST['titre']); // Supprime les balises HTML/JavaScript

                    // Validation stricte de l'URL de l'image
                    $image_url = filter_var($_POST['image_url'], FILTER_VALIDATE_URL);
                    if ($image_url === false) {
                        ErrorHandler::logValidationError('image_url', 'URL image invalide', '❌ URL d\'image invalide');
                        die("Erreur : URL d'image invalide");
                    }

                    // Insertion du favori en base de données avec données validées
                    $sql = "INSERT INTO favorites (user_id, id_api, titre, image_url) VALUES (?, ?, ?, ?)";
                    $db = \App\Core\Db::getInstance();
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$_SESSION['user']['id'], $_POST['id_api'], $titre, $image_url]);

                    // Log succès
                    ErrorHandler::log(
                        "Favori ajouté: {$titre} (id_api: {$_POST['id_api']})",
                        ErrorHandler::TYPE_INFO,
                        null,
                        ['action' => 'favorites/add', 'method' => 'INSERT']
                    );

                    // message success
                    $_SESSION['toasts'][] = [
                        'type' => 'success',
                        'message' => '✅ Recette ajoutée à vos favoris !'
                    ];
                } else {
                    // message recette déjà en favori
                    ErrorHandler::logValidationError('id_api', 'Recette déjà en favori: ' . $_POST['id_api']);

                    $_SESSION['toasts'][] = [
                        'type' => 'info',
                        'message' => 'ℹ️ Cette recette est déjà dans vos favoris.'
                    ];
                }
            } catch (\PDOException $e) {
                // Erreur BD
                ErrorHandler::logDatabaseError($e, 'ajout favori', [
                    'action' => 'favorites/add',
                    'method' => 'INSERT',
                    'id_api' => $_POST['id_api'] ?? 'N/A'
                ]);

                $_SESSION['toasts'][] = [
                    'type' => 'error',
                    'message' => '❌ Erreur lors de l\'ajout du favori'
                ];
            }

            // Redirection vers la page des favoris
            header('Location: /favorites');
            exit;
        }
    }

    /**
     * Supprime un favori de la liste de l'utilisateur
     *
     * Cette méthode supprime définitivement un favori.
     *
     * Sécurité renforcée :
     * - Double condition : id ET user_id
     * - Un utilisateur ne peut supprimer que ses propres favoris
     *
     * @param int $id Identifiant du favori à supprimer
     * @return void Redirige vers /favorites
     *
     * @security Vérification de connexion (exit si non connecté)
     * @security Clause AND user_id (un utilisateur ne peut supprimer que ses favoris)
     * @security Requête préparée contre injection SQL
     */
    public function delete($id)
    {
        // Vérification de la connexion (exit silencieux)
        if (!isset($_SESSION['user'])) exit;

        // Validation de l'ID
        if (!is_numeric($id) || $id <= 0) {
            ErrorHandler::logValidationError('id', 'ID favori invalide (non numérique)');
            header('Location: /favorites');
            exit;
        }

        // Validation du token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            ErrorHandler::logAccessDenied('favorites/delete', 'Token CSRF invalide');
            die("Erreur de sécurité : Token CSRF invalide");
        }

        try {
            // Suppression avec double vérification : id ET user_id
            // Cela empêche un utilisateur de supprimer les favoris d'un autre
            $sql = "DELETE FROM favorites WHERE id = ? AND user_id = ?";
            $db = \App\Core\Db::getInstance();
            $stmt = $db->prepare($sql);
            $stmt->execute([$id, $_SESSION['user']['id']]);

            // Log succès
            ErrorHandler::log(
                "Favori supprimé (ID: {$id})",
                ErrorHandler::TYPE_INFO,
                null,
                ['action' => 'favorites/delete', 'method' => 'DELETE']
            );

            // message de succès
            $_SESSION['toasts'][] = [
                'type' => 'success',
                'message' => '✅ Recette supprimée de vos favoris !'
            ];
        } catch (\PDOException $e) {
            // Erreur BD
            ErrorHandler::logDatabaseError($e, 'suppression favori (ID: ' . $id . ')', [
                'action' => 'favorites/delete',
                'method' => 'DELETE'
            ]);

            $_SESSION['toasts'][] = [
                'type' => 'error',
                'message' => '❌ Erreur lors de la suppression'
            ];
        }

        // Redirection vers la page des favoris
        header('Location: /favorites');
        exit;
    }

    /**
     * Bascule le statut favori d'une recette (ajoute ou supprime)
     * Répond en JSON pour intégration AJAX
     *
     * @return void Envoie JSON et arrête l'exécution
     *
     * @security Vérification CSRF obligatoire
     * @security Utilisateur doit être connecté
     */
    public function toggle()
    {
        header('Content-Type: application/json');

        // Vérification connexion
        if (!isset($_SESSION['user'])) {
            ErrorHandler::logAccessDenied('favorites/toggle', 'Utilisateur non connecté');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Vous devez être connecté',
                'code' => 'NOT_LOGGED_IN'
            ]);
            exit;
        }

        // Vérification CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            ErrorHandler::logAccessDenied('favorites/toggle', 'Token CSRF invalide');
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => 'Token de sécurité invalide',
                'code' => 'CSRF_INVALID'
            ]);
            exit;
        }

        // Validation des données
        if (empty($_POST['id_api'])) {
            ErrorHandler::logValidationError('id_api', 'ID API manquant dans toggle');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID API manquant',
                'code' => 'MISSING_ID'
            ]);
            exit;
        }

        try {
            $favModel = new FavoritesModel();
            $userId = $_SESSION['user']['id'];
            $apiId = $_POST['id_api'];

            // Vérifier si le favori existe
            $favoriteExists = $favModel->exists($userId, $apiId);

            if ($favoriteExists) {
                // SUPPRESSION : le favori existe, on le supprime
                try {
                    $sql = "DELETE FROM favorites WHERE user_id = ? AND id_api = ?";
                    $db = \App\Core\Db::getInstance();
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$userId, $apiId]);

                    // Log succès
                    ErrorHandler::log(
                        "Favori supprimé via toggle (id_api: {$apiId})",
                        ErrorHandler::TYPE_INFO,
                        null,
                        ['action' => 'favorites/toggle', 'method' => 'DELETE']
                    );

                    http_response_code(200);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Recette supprimée de vos favoris',
                        'action' => 'removed',
                        'isFavorite' => false
                    ]);
                    exit;
                } catch (\PDOException $e) {
                    throw $e;
                }
            } else {
                // AJOUT : le favori n'existe pas, on l'ajoute

                // Validation des paramètres pour insertion
                if (empty($_POST['titre']) || empty($_POST['image_url'])) {
                    ErrorHandler::logValidationError('toggle_data', 'Titre ou image manquants');
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Titre ou image manquants',
                        'code' => 'MISSING_DATA'
                    ]);
                    exit;
                }

                // Nettoyage des données
                $titre = strip_tags($_POST['titre']);
                $image_url = filter_var($_POST['image_url'], FILTER_VALIDATE_URL);

                if ($image_url === false) {
                    ErrorHandler::logValidationError('image_url', 'URL image invalide dans toggle');
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'URL d\'image invalide',
                        'code' => 'INVALID_URL'
                    ]);
                    exit;
                }

                // Insertion
                try {
                    $sql = "INSERT INTO favorites (user_id, id_api, titre, image_url) VALUES (?, ?, ?, ?)";
                    $db = \App\Core\Db::getInstance();
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$userId, $apiId, $titre, $image_url]);

                    // Log succès
                    ErrorHandler::log(
                        "Favori ajouté via toggle: {$titre} (id_api: {$apiId})",
                        ErrorHandler::TYPE_INFO,
                        null,
                        ['action' => 'favorites/toggle', 'method' => 'INSERT']
                    );

                    http_response_code(201);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Recette ajoutée à vos favoris',
                        'action' => 'added',
                        'isFavorite' => true
                    ]);
                    exit;
                } catch (\PDOException $e) {
                    throw $e;
                }
            }
        } catch (\PDOException $e) {
            // Erreur BD
            ErrorHandler::logDatabaseError($e, 'toggle favori', [
                'action' => 'favorites/toggle',
                'id_api' => $_POST['id_api'] ?? 'N/A'
            ]);

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur - Veuillez réessayer',
                'code' => 'SERVER_ERROR'
            ]);
            exit;
        }
    }
}