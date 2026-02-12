<?php
namespace App\Models;

use App\Core\Model;

class FavoritesModel extends Model
{
    protected $id;
    protected $user_id;
    protected $id_api;
    protected $titre;
    protected $image_url;
    protected $note;

    public function __construct()
    {
        $this->table = 'favorites';
    }

    // Trouver tous les favoris d'un utilisateur spécifique
    public function findAllByUserId(int $userId)
    {
        return $this->requete("SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC", [$userId])->fetchAll();
    }

    // Vérifier si une recette est déjà en favori
    public function exists(int $userId, string $apiId)
    {
        return $this->requete("SELECT id FROM {$this->table} WHERE user_id = ? AND id_api = ?", [$userId, $apiId])->fetch();
    }
}