<?php
/**
 * Nom du fichier : FavoritesModel.php
 *
 * Description :
 * Modèle représentant les recettes favorites des utilisateurs.
 * Gère les opérations CRUD sur la table "favorites" pour sauvegarder
 * les recettes favorites provenant de l'API TheMealDB.
 *
 * Fonctionnalités principales :
 * - Récupération des favoris d'un utilisateur
 * - Vérification d'existence d'un favori (éviter doublons)
 * - Sauvegarde de recettes externes (API)
 *
 * Table associée : favorites
 * Relations : Appartient à un utilisateur (user_id → users.id)
 *
 * @package    App\Models
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Models;

use App\Core\Model;
use App\Core\ErrorHandler;

class FavoritesModel extends Model
{
    /** @var int Identifiant unique du favori */
    protected $id;

    /** @var int Identifiant de l'utilisateur propriétaire */
    protected $user_id;

    /** @var string Identifiant de la recette dans l'API TheMealDB */
    protected $id_api;

    /** @var string Titre de la recette favorite */
    protected $titre;

    /** @var string URL de l'image de la recette */
    protected $image_url;

    /**
     * Constructeur - Définit le nom de la table
     */
    public function __construct()
    {
        $this->table = 'favorites';
    }

    /**
     * Récupère tous les favoris d'un utilisateur spécifique
     *
     * Cette méthode retourne la liste complète des recettes favorites
     * d'un utilisateur, triées de la plus récemment ajoutée à la plus ancienne.
     *
     * Exemple d'utilisation :
     * ```php
     * $mesFavoris = $favoritesModel->findAllByUserId($_SESSION['user']['id']);
     * ```
     *
     * @param int $userId Identifiant de l'utilisateur
     * @return array Tableau des recettes favorites de l'utilisateur
     *
     * @security Requête préparée contre injection SQL
     */
    public function findAllByUserId(int $userId)
    {
        try {
            return $this->requete("SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC", [$userId])->fetchAll();
        } catch (\PDOException $e) {
            ErrorHandler::logDatabaseError($e, 'FavoritesModel::findAllByUserId()', [
                'model' => 'FavoritesModel',
                'method' => 'findAllByUserId',
                'user_id' => $userId
            ]);
            throw $e;
        }
    }

    /**
     * Vérifie si une recette API est déjà en favori pour un utilisateur
     *
     * Cette méthode implémente une logique métier importante : éviter les doublons.
     * Avant d'ajouter un favori, on vérifie que la combinaison (user_id, id_api)
     * n'existe pas déjà dans la base de données.
     *
     * Exemple d'utilisation :
     * ```php
     * if ($favoritesModel->exists($userId, $recetteApiId)) {
     *     echo "Cette recette est déjà dans vos favoris";
     * } else {
     *     // Ajouter aux favoris
     * }
     * ```
     *
     * @param int $userId Identifiant de l'utilisateur
     * @param string $apiId Identifiant de la recette dans l'API TheMealDB
     * @return object|false L'enregistrement si existant, false sinon
     *
     * @security Requête préparée contre injection SQL
     * @note Retourne uniquement l'ID pour optimiser les performances
     */
    public function exists(int $userId, string $apiId)
    {
        try {
            return $this->requete("SELECT id FROM {$this->table} WHERE user_id = ? AND id_api = ?", [$userId, $apiId])->fetch();
        } catch (\PDOException $e) {
            ErrorHandler::logDatabaseError($e, 'FavoritesModel::exists()', [
                'model' => 'FavoritesModel',
                'method' => 'exists',
                'user_id' => $userId,
                'id_api' => $apiId
            ]);
            throw $e;
        }
    }
}