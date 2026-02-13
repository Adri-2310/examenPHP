<?php
/**
 * Nom du fichier : UsersController.php
 *
 * Description :
 * Contrôleur gérant l'authentification des utilisateurs.
 * Gère la connexion, la déconnexion et l'inscription des utilisateurs.
 *
 * Fonctionnalités principales :
 * - Connexion avec vérification du mot de passe hashé
 * - Déconnexion (destruction de la session)
 * - Inscription avec hashing du mot de passe
 *
 * Sécurité :
 * - Hashing des mots de passe avec PASSWORD_ARGON2ID
 * - Vérification avec password_verify()
 * - Nettoyage des données (strip_tags)
 * - Vérification d'unicité de l'email
 *
 * @package    App\Controllers
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UsersModel;

class UsersController extends Controller
{
    /**
     * Affiche le formulaire de connexion et traite l'authentification
     *
     * Cette méthode gère le processus complet d'authentification :
     * 1. Affichage du formulaire de connexion
     * 2. Vérification des identifiants
     * 3. Création de la session utilisateur
     *
     * Processus de vérification :
     * - Recherche de l'utilisateur par email
     * - Vérification du mot de passe avec password_verify()
     * - Création de la variable de session $_SESSION['user']
     *
     * @return void Affiche la vue auth/login.php ou redirige vers /
     *
     * @security Utilise password_verify() pour comparer le hash
     * @security Message d'erreur générique pour éviter l'énumération d'emails
     */
    public function login()
    {
        // ===== TRAITEMENT DU FORMULAIRE DE CONNEXION =====
        if (!empty($_POST)) {
            // Validation du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Erreur de sécurité : Token CSRF invalide");
            }

            // Validation des champs obligatoires
            if (!empty($_POST['email']) && !empty($_POST['password'])) {

                // 1. Recherche de l'utilisateur dans la base de données
                $userModel = new UsersModel();
                $user = $userModel->findOneByEmail($_POST['email']);

                // 2. Vérification du mot de passe
                // password_verify() compare le mot de passe en clair avec le hash stocké
                if ($user && password_verify($_POST['password'], $user->password)) {

                    // 3. Création de la session utilisateur
                    $_SESSION['user'] = [
                        'id' => $user->id,
                        'email' => $user->email,
                        'nom' => $user->nom,
                        'roles' => $user->role
                    ];

                    // 4. Redirection vers la page d'accueil
                    header('Location: /');
                    exit;
                } else {
                    // Identifiants incorrects (message générique pour la sécurité)
                    $erreur = "Identifiants incorrects";
                }
            }
        }

        // Affichage du formulaire de connexion
        $this->render('auth/login', [
            'erreur' => $erreur ?? null
        ]);
    }

    /**
     * Déconnecte l'utilisateur et détruit la session
     *
     * Cette méthode supprime les données de session de l'utilisateur
     * et le redirige vers la page d'accueil.
     *
     * @return void Redirige vers /
     */
    public function logout()
    {
        // Suppression de la variable de session utilisateur
        unset($_SESSION['user']);

        // Redirection vers la page d'accueil
        header('Location: /');
        exit;
    }

    /**
     * Affiche le formulaire d'inscription et traite l'enregistrement
     *
     * Cette méthode gère le processus complet d'inscription :
     * 1. Affichage du formulaire d'inscription
     * 2. Validation des données
     * 3. Vérification d'unicité de l'email
     * 4. Hashing du mot de passe avec PASSWORD_ARGON2ID
     * 5. Création du compte utilisateur
     *
     * Algorithme de hashing :
     * PASSWORD_ARGON2ID est l'algorithme le plus sécurisé actuellement (2026).
     * Il remplace bcrypt et offre une meilleure résistance aux attaques GPU.
     *
     * @return void Affiche la vue auth/register.php ou redirige vers /users/login
     *
     * @security Nettoyage strip_tags() contre XSS
     * @security Hashing PASSWORD_ARGON2ID (le plus fort disponible en PHP)
     * @security Vérification d'unicité de l'email (pas de doublons)
     */
    public function register()
    {
        // ===== TRAITEMENT DU FORMULAIRE D'INSCRIPTION =====
        if (!empty($_POST)) {
            // Validation du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Erreur de sécurité : Token CSRF invalide");
            }

            // Validation des champs obligatoires
            if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['nom'])) {

                // 1. Nettoyage des données utilisateur (protection XSS)
                $email = strip_tags($_POST['email']);
                $nom = strip_tags($_POST['nom']);
                $password = $_POST['password'];

                // 2. Vérification d'unicité de l'email
                $userModel = new UsersModel();
                $existingUser = $userModel->findOneByEmail($email);

                if ($existingUser) {
                    $erreur = "Cet email est déjà utilisé";
                } else {
                    // 3. Hashing du mot de passe avec l'algorithme le plus sécurisé
                    // PASSWORD_ARGON2ID : Standard recommandé en 2026
                    // Avantages : Résistant aux attaques GPU, mémoire-intensive
                    $hash = password_hash($password, PASSWORD_ARGON2ID);

                    // 4. Enregistrement de l'utilisateur
                    $userModel->createUser($email, $hash, $nom);

                    // 5. Redirection vers la page de connexion
                    header('Location: /users/login');
                    exit;
                }
            } else {
                $erreur = "Le formulaire est incomplet";
            }
        }

        // Affichage du formulaire d'inscription
        $this->render('auth/register', [
            'erreur' => $erreur ?? null
        ]);
    }
}