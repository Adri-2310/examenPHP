<?php
/**
 * Nom du fichier : RecipesModel.php
 *
 * Description :
 * Modèle représentant les recettes de cuisine.
 * Gère les opérations CRUD sur la table "recipes" et les jointures avec la table users.
 *
 * Fonctionnalités principales :
 * - Récupération des recettes avec informations d'auteur (jointure SQL)
 * - Filtrage des recettes par utilisateur
 * - Héritage des méthodes CRUD de base (find, findAll, create)
 *
 * Table associée : recipes
 * Relations : Appartient à un utilisateur (user_id → users.id)
 *
 * @package    App\Models
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Models;

use App\Core\Model;

class RecipesModel extends Model
{
    /** @var int Identifiant unique de la recette */
    protected $id;

    /** @var string Titre de la recette */
    protected $title;

    /** @var string Description courte de la recette */
    protected $description;

    /** @var string Liste des ingrédients nécessaires */
    protected $ingredients;

    /** @var string Étapes de préparation de la recette */
    protected $instructions;

    /** @var int Identifiant de l'utilisateur créateur */
    protected $user_id;

    /** @var string Date de création de la recette */
    protected $created_at;

    /**
     * Constructeur - Définit le nom de la table
     */
    public function __construct()
    {
        $this->table = 'recipes';
    }

    /**
     * Récupère toutes les recettes avec les informations de l'auteur
     *
     * Cette méthode effectue une jointure SQL (INNER JOIN) entre les tables
     * recipes et users pour enrichir chaque recette avec le nom de son auteur.
     *
     * Requête SQL exécutée :
     * SELECT r.*, u.nom as author_name
     * FROM recipes r
     * INNER JOIN users u ON r.user_id = u.id
     * ORDER BY created_at DESC
     *
     * Exemple de résultat :
     * ```php
     * [
     *   ['id' => 1, 'title' => 'Tarte aux pommes', 'author_name' => 'Chef Philippe'],
     *   ['id' => 2, 'title' => 'Soupe à l\'oignon', 'author_name' => 'Marie Dupont']
     * ]
     * ```
     *
     * @return array Tableau d'objets avec toutes les colonnes de recipes + author_name
     *
     * @note Les recettes sont triées de la plus récente à la plus ancienne
     */
    public function findAllWithAuthor()
    {
        return $this->requete("
            SELECT r.*, u.nom as author_name
            FROM {$this->table} r
            INNER JOIN users u ON r.user_id = u.id
            ORDER BY created_at DESC
        ")->fetchAll();
    }

    /**
     * Récupère toutes les recettes créées par un utilisateur spécifique
     *
     * Cette méthode filtre les recettes par user_id pour n'afficher que
     * celles appartenant à un utilisateur donné (ex: page "Mes Recettes").
     *
     * Exemple d'utilisation :
     * ```php
     * $mesRecettes = $recipesModel->findAllByUserId($_SESSION['user']['id']);
     * ```
     *
     * @param int $userId Identifiant de l'utilisateur
     * @return array Tableau des recettes de l'utilisateur, triées par date décroissante
     *
     * @security Requête préparée contre injection SQL
     */
    public function findAllByUserId(int $userId)
    {
        return $this->requete("SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC", [$userId])->fetchAll();
    }
}