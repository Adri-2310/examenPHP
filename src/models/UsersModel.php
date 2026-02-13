<?php
/**
 * Nom du fichier : UsersModel.php
 *
 * Description :
 * Modèle représentant les utilisateurs de l'application.
 * Gère les opérations CRUD sur la table "users" et l'authentification.
 *
 * Fonctionnalités principales :
 * - Recherche d'utilisateur par email (login)
 * - Création d'utilisateur avec hashing de mot de passe
 * - Gestion des rôles utilisateurs
 *
 * Table associée : users
 * Relations : Possède plusieurs recettes (recipes) et favoris (favorites)
 *
 * Sécurité :
 * - Mots de passe hashés avec password_hash() (bcrypt ou argon2id)
 * - Rôles stockés au format JSON
 *
 * @package    App\Models
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Models;

use App\Core\Model;

class UsersModel extends Model
{
    /** @var int Identifiant unique de l'utilisateur */
    protected $id;

    /** @var string Adresse email (identifiant de connexion) */
    protected $email;

    /** @var string Mot de passe hashé */
    protected $password;

    /** @var string Nom complet de l'utilisateur */
    protected $nom;

    /** @var string Rôles de l'utilisateur au format JSON (ex: ["ROLE_USER"]) */
    protected $role;

    /**
     * Constructeur - Définit le nom de la table
     */
    public function __construct()
    {
        $this->table = 'users';
    }

    /**
     * Récupère un utilisateur par son adresse email
     *
     * Cette méthode est principalement utilisée lors de l'authentification
     * pour vérifier si un email existe dans la base de données.
     *
     * Exemple d'utilisation :
     * ```php
     * $user = $usersModel->findOneByEmail($_POST['email']);
     * if ($user && password_verify($_POST['password'], $user->password)) {
     *     // Connexion réussie
     * }
     * ```
     *
     * @param string $email Adresse email à rechercher
     * @return object|false L'utilisateur trouvé ou false si inexistant
     *
     * @security Requête préparée contre injection SQL
     */
    public function findOneByEmail(string $email)
    {
        return $this->requete("SELECT * FROM {$this->table} WHERE email = ?", [$email])->fetch();
    }

    /**
     * Crée un nouvel utilisateur dans la base de données
     *
     * Cette méthode enregistre un nouvel utilisateur avec :
     * - Un mot de passe déjà hashé (doit être hashé AVANT l'appel à cette méthode)
     * - Un rôle par défaut ROLE_USER au format JSON
     *
     * Exemple d'utilisation :
     * ```php
     * $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
     * $usersModel->createUser($_POST['email'], $hashedPassword, $_POST['nom']);
     * ```
     *
     * @param string $email Adresse email de l'utilisateur
     * @param string $password Mot de passe DÉJÀ HASHÉ (avec password_hash)
     * @param string $nom Nom complet de l'utilisateur
     * @return \PDOStatement Résultat de l'insertion
     *
     * @security Le mot de passe doit être hashé avec password_hash() AVANT l'appel
     * @security Requête préparée contre injection SQL
     * @note Le rôle par défaut est ['ROLE_USER'] stocké au format JSON
     */
    public function createUser(string $email, string $password, string $nom)
    {
        return $this->requete(
            "INSERT INTO {$this->table} (email, password, nom, role) VALUES (?, ?, ?, ?)",
            [$email, $password, $nom, json_encode(['ROLE_USER'])]
        );
    }
}