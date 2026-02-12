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
}