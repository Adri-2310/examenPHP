<?php
namespace App\Models;

use App\Core\Model;

class RecipesModel extends Model
{
    protected $id;
    protected $title;
    protected $description;
    protected $ingredients;
    protected $instructions;
    protected $user_id;
    protected $created_at;

    public function __construct()
    {
        $this->table = 'recipes';
    }

    /**
     * Récupère les recettes avec les infos de l'auteur (Jointure)
     * Utile pour afficher "Recette par Chef Philippe"
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
     * Récupère toutes les recettes d'un utilisateur
     */
    public function findAllByUserId(int $userId)
    {
        return $this->requete("SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC", [$userId])->fetchAll();
    }
}