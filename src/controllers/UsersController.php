<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UsersModel;

class UsersController extends Controller
{
    /**
     * Connexion des utilisateurs
     */
    public function login()
    {
        // 1. Si le formulaire est envoyé
        if (!empty($_POST)) {
            // 2. Vérification des champs
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                
                // 3. On cherche l'utilisateur en BDD
                $userModel = new UsersModel();
                $user = $userModel->findOneByEmail($_POST['email']);

                // 4. Si l'utilisateur existe et que le mot de passe correspond
                if ($user && password_verify($_POST['password'], $user->password)) {
                    
                    // 5. On crée la SESSION (Partie 05)
                    $_SESSION['user'] = [
                        'id' => $user->id,
                        'email' => $user->email,
                        'nom' => $user->nom,
                        'roles' => $user->role
                    ];

                    // 6. Redirection vers l'accueil
                    header('Location: /');
                    exit;
                } else {
                    // Mauvais identifiants
                    $erreur = "Identifiants incorrects";
                }
            }
        }

        // Affichage de la vue login
        $this->render('auth/login', [
            'erreur' => $erreur ?? null
        ]);
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        unset($_SESSION['user']);
        header('Location: /');
        exit;
    }

    /**
     * Inscription des utilisateurs
     */
    public function register()
    {
        // Si le formulaire est envoyé
        if (!empty($_POST)) {
            // Vérification basique
            if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['nom'])) {
                
                // On nettoie les entrées (Sécurité XSS)
                $email = strip_tags($_POST['email']);
                $nom = strip_tags($_POST['nom']);
                $password = $_POST['password'];

                // On vérifie si l'email existe déjà
                $userModel = new UsersModel();
                $existingUser = $userModel->findOneByEmail($email);

                if ($existingUser) {
                    $erreur = "Cet email est déjà utilisé";
                } else {
                    // On HASH le mot de passe (Sécurité Partie 06)
                    // "PASSWORD_ARGON2ID" est le standard actuel le plus fort
                    $hash = password_hash($password, PASSWORD_ARGON2ID);

                    // On enregistre
                    $userModel->createUser($email, $hash, $nom);

                    // Redirection
                    header('Location: /users/login');
                    exit;
                }
            } else {
                $erreur = "Le formulaire est incomplet";
            }
        }

        $this->render('auth/register', [
            'erreur' => $erreur ?? null
        ]);
    }
}