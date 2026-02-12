<?php
namespace App\Models;

use App\Core\Model;

class UsersModel extends Model
{
    protected $id;
    protected $email;
    protected $password;
    protected $nom;
    protected $role;

    public function __construct()
    {
        $this->table = 'users';
    }

    // Récupérer un user par son email
    public function findOneByEmail(string $email)
    {
        return $this->requete("SELECT * FROM {$this->table} WHERE email = ?", [$email])->fetch();
    }

    
    // Crée un nouvel utilisateur
    public function createUser(string $email, string $password, string $nom)
    {
        return $this->requete(
            "INSERT INTO {$this->table} (email, password, nom, role) VALUES (?, ?, ?, ?)", 
            [$email, $password, $nom, json_encode(['ROLE_USER'])]
        );
    }
}