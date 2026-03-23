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
use App\Core\ErrorHandler;
use App\Models\UsersModel;

class UsersController extends Controller
{
    /**
     * Affiche le formulaire de connexion et traite l'authentification
     *
     * Cette méthode gère le processus complet d'authentification avec protection anti-brute force :
     * 1. Vérification du rate limiting (5 tentatives échouées = blocage 15 min)
     * 2. Affichage du formulaire de connexion
     * 3. Vérification des identifiants
     * 4. Création de la session utilisateur
     *
     * Processus de vérification :
     * - Recherche de l'utilisateur par email
     * - Vérification du mot de passe avec password_verify()
     * - Création de la variable de session $_SESSION['user']
     * - Enregistrement des tentatives échouées par IP
     * - Avertissement si tentatives > 1 et < limite
     *
     * Rate Limiting :
     * - Maximum 5 tentatives échouées par IP
     * - Blocage de 15 minutes après dépassement
     * - Nettoyage automatique des tentatives > 15 minutes
     * - Affichage du temps restant avant déblocage
     *
     * @return void Affiche la vue auth/login.php ou redirige vers /
     *
     * @security Utilise password_verify() pour comparer le hash
     * @security Message d'erreur générique pour éviter l'énumération d'emails
     * @security Rate limiting par IP pour prévenir les attaques par force brute
     * @security Avertissements progressifs du nombre de tentatives restantes
     */
    public function login()
    {
        // ===== RATE LIMITING =====
        $ip = $_SERVER['REMOTE_ADDR'];
        $maxAttempts = 5;
        $lockoutTime = 15 * 60; // 15 minutes en secondes

        // Initialiser le compteur si nécessaire
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }

        // Nettoyer les anciennes tentatives (plus de 15 minutes)
        foreach ($_SESSION['login_attempts'] as $attemptIp => $data) {
            if (time() - $data['time'] > $lockoutTime) {
                unset($_SESSION['login_attempts'][$attemptIp]);
            }
        }

        // Vérifier si l'IP est bloquée
        if (isset($_SESSION['login_attempts'][$ip])) {
            $attempts = $_SESSION['login_attempts'][$ip]['count'];
            $firstAttempt = $_SESSION['login_attempts'][$ip]['time'];

            if ($attempts >= $maxAttempts && (time() - $firstAttempt) < $lockoutTime) {
                $remainingTime = ceil(($lockoutTime - (time() - $firstAttempt)) / 60);
                $erreur = "Trop de tentatives de connexion. Réessayez dans {$remainingTime} minute(s).";

                $this->render('auth/login', [
                    'erreur' => $erreur,
                    'titre' => 'Connexion'
                ]);
                return;
            }
            
            // Avertissement si plusieurs tentatives échouées mais pas encore bloqué
            if ($attempts > 0 && $attempts < $maxAttempts && (time() - $firstAttempt) < $lockoutTime) {
                $remainingAttempts = $maxAttempts - $attempts;
                $avertissement = "⚠️ Attention : {$remainingAttempts} tentative(s) restante(s) avant blocage du compte";
            }
        }
        // ===== FIN RATE LIMITING =====

        // ===== TRAITEMENT DU FORMULAIRE DE CONNEXION =====
        if (!empty($_POST)) {
            // Validation du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Erreur de sécurité : Token CSRF invalide");
            }

            // Validation des champs obligatoires
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                
                // Validation de l'email avec un format strict
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    ErrorHandler::logValidationError('email', 'Format email invalide', '❌ Email invalide');
                    $erreur = "Email invalide";
                } else {
                    try {
                        // 1. Recherche de l'utilisateur dans la base de données
                        $userModel = new UsersModel();
                        $user = $userModel->findOneByEmail($_POST['email']);

                        // 2. Vérification du mot de passe
                        // password_verify() compare le mot de passe en clair avec le hash stocké
                        if ($user && password_verify($_POST['password'], $user->password)) {
                            session_regenerate_id(true);

                            // 3. Création de la session utilisateur
                            $_SESSION['user'] = [
                                'id' => $user->id,
                                'email' => $user->email,
                                'nom' => $user->nom,
                                'roles' => $user->role
                            ];

                            // 4. Notification de succès
                            $_SESSION['toasts'][] = [
                                'type' => 'success',
                                'message' => '✅ Bienvenue ' . htmlspecialchars($user->nom) . ' ! Connexion réussie.'
                            ];

                            // Login réussi : réinitialiser les tentatives
                            unset($_SESSION['login_attempts'][$ip]);

                            // Log la connexion réussie
                            ErrorHandler::log(
                                "Connexion réussie pour " . $user->email,
                                ErrorHandler::TYPE_INFO,
                                null,
                                ['action' => 'users/login', 'method' => 'success']
                            );

                            // 5. Redirection vers la page d'accueil
                            header('Location: /');
                            exit;
                        } else {
                            // Mot de passe incorrect ou utilisateur non trouvé

                            // ===== ENREGISTRER TENTATIVE ÉCHOUÉE =====
                            if (!isset($_SESSION['login_attempts'][$ip])) {
                                $_SESSION['login_attempts'][$ip] = [
                                    'count' => 1,
                                    'time' => time()
                                ];
                            } else {
                                $_SESSION['login_attempts'][$ip]['count']++;
                            }
                            // ===== FIN =====

                            // Log la tentative échouée (avec l'email, mais pas le mot de passe)
                            ErrorHandler::logValidationError(
                                'credentials',
                                'Email ou mot de passe incorrect (IP: ' . $ip . ')',
                                '❌ Identifiants incorrects'
                            );

                            $erreur = "Identifiants incorrects";
                        }
                    } catch (\PDOException $e) {
                        // Erreur lors de la recherche en BD
                        ErrorHandler::logDatabaseError($e, 'recherche utilisateur par email', [
                            'action' => 'users/login',
                            'method' => 'findOneByEmail'
                        ]);

                        // Affiche la page d'erreur database
                        ErrorHandler::displayDatabaseErrorPage($e, 'authentification');
                    }
                }
            }
        }

        // Affichage du formulaire de connexion
        $this->render('auth/login', [
            'erreur' => $erreur ?? null,
            'avertissement' => $avertissement ?? null,
            'titre' => 'Connexion'
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
        // Récupération du nom avant suppression de la session
        $userName = $_SESSION['user']['nom'] ?? 'utilisateur';

        // Suppression de la variable de session utilisateur
        unset($_SESSION['user']);

        // Notification de déconnexion
        $_SESSION['toasts'][] = [
            'type' => 'info',
            'message' => 'Vous avez été déconnecté avec succès. À bientôt ' . $userName . ' !'
        ];

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

                // 2. Validation de l'email et du mot de passe
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    ErrorHandler::logValidationError('email', 'Format email invalide lors de register', '❌ Email invalide');
                    $erreur = "Email invalide";
                } elseif (strlen($password) < 8) {
                    ErrorHandler::logValidationError('password', 'Mot de passe trop court (< 8 caractères)', '❌ Le mot de passe doit contenir au moins 8 caractères');
                    $erreur = "Le mot de passe doit contenir au moins 8 caractères";
                } else {
                    try {
                        // 3. Vérification d'unicité de l'email
                        $userModel = new UsersModel();
                        $existingUser = $userModel->findOneByEmail($email);

                        if ($existingUser) {
                            ErrorHandler::logValidationError('email', 'Email déjà utilisé: ' . $email, '❌ Cet email est déjà utilisé');
                            $erreur = "Cet email est déjà utilisé";
                        } else {
                            // 4. Hashing du mot de passe avec l'algorithme le plus sécurisé
                            // PASSWORD_ARGON2ID : Standard recommandé en 2026
                            // Avantages : Résistant aux attaques GPU, mémoire-intensive
                            $hash = password_hash($password, PASSWORD_ARGON2ID);

                            // 5. Enregistrement de l'utilisateur
                            try {
                                $userModel->createUser($email, $hash, $nom);

                                // 6. Notification de succès d'inscription
                                $_SESSION['toasts'][] = [
                                    'type' => 'success',
                                    'message' => '✅ Inscription réussie ! Vous pouvez maintenant vous connecter avec votre email.'
                                ];

                                // Log l'inscription réussie
                                ErrorHandler::log(
                                    "Nouvel utilisateur inscrit: {$email}",
                                    ErrorHandler::TYPE_INFO,
                                    null,
                                    ['action' => 'users/register', 'method' => 'createUser']
                                );

                                // 7. Redirection vers la page de connexion
                                header('Location: /users/login');
                                exit;
                            } catch (\PDOException $e) {
                                // Erreur lors de la création de l'utilisateur
                                ErrorHandler::logDatabaseError($e, 'création utilisateur (email: ' . $email . ')', [
                                    'action' => 'users/register',
                                    'method' => 'createUser'
                                ]);

                                // Affiche la page d'erreur database
                                ErrorHandler::displayDatabaseErrorPage($e, 'inscription');
                            }
                        }
                    } catch (\PDOException $e) {
                        // Erreur lors de la vérification d'unicité
                        ErrorHandler::logDatabaseError($e, 'vérification unicité email', [
                            'action' => 'users/register',
                            'method' => 'findOneByEmail'
                        ]);

                        // Affiche la page d'erreur database
                        ErrorHandler::displayDatabaseErrorPage($e, 'vérification email');
                    }
                }
            } else {
                ErrorHandler::logValidationError('form', 'Formulaire d\'inscription incomplet');
                $erreur = "Le formulaire est incomplet";
            }
        }

        // Affichage du formulaire d'inscription
        $this->render('auth/register', [
            'erreur' => $erreur ?? null
        ]);
    }

    /**
     * Affiche la page de profil de l'utilisateur connecté
     *
     * Cette méthode est réservée aux utilisateurs authentifiés.
     * Elle affiche les informations actuelles du compte (nom, email).
     *
     * @return void Affiche la vue users/profile.php ou redirige vers /users/login
     *
     * @security Redirection vers /users/login si non connecté
     */
    public function profile()
    {
        // Vérification de la connexion utilisateur
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        try {
            $userModel = new UsersModel();
            $user = $userModel->findById($_SESSION['user']['id']);

            if (!$user) {
                // L'utilisateur en session n'existe plus en BD
                unset($_SESSION['user']);
                header('Location: /users/login');
                exit;
            }

            $this->render('users/profile', [
                'user'  => $user,
                'titre' => 'Mon Profil'
            ]);
        } catch (\PDOException $e) {
            ErrorHandler::logDatabaseError($e, 'chargement profil utilisateur', [
                'action' => 'users/profile',
                'user_id' => $_SESSION['user']['id']
            ]);
            ErrorHandler::displayDatabaseErrorPage($e, 'profil');
        }
    }

    /**
     * Traite la mise à jour des informations de profil (nom et email)
     *
     * Processus :
     * 1. Vérification de l'authentification
     * 2. Validation CSRF
     * 3. Validation des données (email format, nom non vide)
     * 4. Vérification de l'unicité du nouvel email (si modifié)
     * 5. Mise à jour en base de données
     * 6. Mise à jour de la session
     *
     * @return void Redirige vers /users/profile
     *
     * @security Requête préparée contre injection SQL
     * @security Vérification CSRF
     * @security Vérification de propriété (user_id = session user id)
     */
    public function updateProfile()
    {
        // Vérification de la connexion utilisateur
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // Cette action n'accepte que les requêtes POST
        if (empty($_POST)) {
            header('Location: /users/profile');
            exit;
        }

        // Validation du token CSRF (déjà vérifiée par le routeur, sécurité en profondeur)
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Erreur de sécurité : Token CSRF invalide");
        }

        $userId = (int) $_SESSION['user']['id'];
        $nom    = strip_tags(trim($_POST['nom'] ?? ''));
        $email  = strip_tags(trim($_POST['email'] ?? ''));

        // Validation des champs
        if (empty($nom) || empty($email)) {
            $_SESSION['toasts'][] = [
                'type'    => 'error',
                'message' => 'Le nom et l\'email sont obligatoires.'
            ];
            header('Location: /users/profile');
            exit;
        }

        if (strlen($nom) < 3) {
            $_SESSION['toasts'][] = [
                'type'    => 'error',
                'message' => 'Le nom doit contenir au moins 3 caractères.'
            ];
            header('Location: /users/profile');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['toasts'][] = [
                'type'    => 'error',
                'message' => 'L\'adresse email n\'est pas valide.'
            ];
            header('Location: /users/profile');
            exit;
        }

        try {
            $userModel = new UsersModel();

            // Vérifier que le nouvel email n'est pas déjà utilisé par un autre compte
            $existingUser = $userModel->findOneByEmail($email);
            if ($existingUser && (int) $existingUser->id !== $userId) {
                $_SESSION['toasts'][] = [
                    'type'    => 'error',
                    'message' => 'Cet email est déjà utilisé par un autre compte.'
                ];
                header('Location: /users/profile');
                exit;
            }

            $userModel->updateProfile($userId, $email, $nom);

            // Mise à jour de la session avec les nouvelles informations
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['nom']   = $nom;

            $_SESSION['toasts'][] = [
                'type'    => 'success',
                'message' => '✅ Profil mis à jour avec succès !'
            ];

            ErrorHandler::log(
                "Profil mis à jour pour l'utilisateur ID: {$userId}",
                ErrorHandler::TYPE_INFO,
                null,
                ['action' => 'users/updateProfile']
            );

        } catch (\PDOException $e) {
            ErrorHandler::logDatabaseError($e, 'mise à jour profil utilisateur', [
                'action'  => 'users/updateProfile',
                'user_id' => $userId
            ]);
            $_SESSION['toasts'][] = [
                'type'    => 'error',
                'message' => 'Une erreur est survenue lors de la mise à jour. Veuillez réessayer.'
            ];
        }

        header('Location: /users/profile');
        exit;
    }

    /**
     * Traite le changement de mot de passe de l'utilisateur connecté
     *
     * Processus :
     * 1. Vérification de l'authentification
     * 2. Validation CSRF
     * 3. Vérification de l'ancien mot de passe
     * 4. Validation du nouveau mot de passe (longueur, confirmation)
     * 5. Hashing et mise à jour en base de données
     *
     * @return void Redirige vers /users/profile
     *
     * @security Vérification de l'ancien mot de passe obligatoire
     * @security Hashing PASSWORD_ARGON2ID (le plus fort disponible en PHP)
     * @security Vérification CSRF
     */
    public function changePassword()
    {
        // Vérification de la connexion utilisateur
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // Cette action n'accepte que les requêtes POST
        if (empty($_POST)) {
            header('Location: /users/profile');
            exit;
        }

        // Validation du token CSRF (déjà vérifiée par le routeur, sécurité en profondeur)
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Erreur de sécurité : Token CSRF invalide");
        }

        $userId          = (int) $_SESSION['user']['id'];
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation des champs obligatoires
        if (empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['toasts'][] = [
                'type'    => 'error',
                'message' => 'Tous les champs du mot de passe sont obligatoires.'
            ];
            header('Location: /users/profile');
            exit;
        }

        // Validation de la longueur minimale du nouveau mot de passe
        if (strlen($newPassword) < 8) {
            $_SESSION['toasts'][] = [
                'type'    => 'error',
                'message' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.'
            ];
            header('Location: /users/profile');
            exit;
        }

        // Vérification de la correspondance nouveau mot de passe / confirmation
        if ($newPassword !== $confirmPassword) {
            $_SESSION['toasts'][] = [
                'type'    => 'error',
                'message' => 'Le nouveau mot de passe et sa confirmation ne correspondent pas.'
            ];
            header('Location: /users/profile');
            exit;
        }

        try {
            $userModel = new UsersModel();
            $user = $userModel->findById($userId);

            if (!$user) {
                unset($_SESSION['user']);
                header('Location: /users/login');
                exit;
            }

            // Hashing du nouveau mot de passe avec l'algorithme le plus sécurisé
            $newHash = password_hash($newPassword, PASSWORD_ARGON2ID);
            $userModel->updatePassword($userId, $newHash);

            $_SESSION['toasts'][] = [
                'type'    => 'success',
                'message' => '✅ Mot de passe mis à jour avec succès !'
            ];

            ErrorHandler::log(
                "Mot de passe modifié pour l'utilisateur ID: {$userId}",
                ErrorHandler::TYPE_INFO,
                null,
                ['action' => 'users/changePassword']
            );

        } catch (\PDOException $e) {
            ErrorHandler::logDatabaseError($e, 'changement de mot de passe', [
                'action'  => 'users/changePassword',
                'user_id' => $userId
            ]);
            $_SESSION['toasts'][] = [
                'type'    => 'error',
                'message' => 'Une erreur est survenue lors du changement de mot de passe. Veuillez réessayer.'
            ];
        }

        header('Location: /users/profile');
        exit;
    }

    /**
     * Vérifie si un nom d'utilisateur existe déjà en base de données
     * Utilisé pour la validation client (AJAX) lors de l'inscription
     *
     * @return void Répond en JSON et arrête l'exécution
     *
     * @api POST /users/checkNom
     * @param string $_POST['nom'] Nom d'utilisateur à vérifier
     */
    public function checkNom()
    {
        header('Content-Type: application/json');

        // Vérifier que le nom est fourni
        if (!isset($_POST['nom']) || empty($_POST['nom'])) {
            ErrorHandler::logValidationError('nom', 'Nom non fourni dans checkNom');
            echo json_encode([
                'status' => 'error',
                'exists' => false,
                'message' => 'Nom non fourni'
            ]);
            exit;
        }

        $nom = strip_tags(trim($_POST['nom']));

        // Validation : le nom doit contenir au moins 3 caractères
        if (strlen($nom) < 3) {
            ErrorHandler::logValidationError('nom', 'Nom trop court (< 3 caractères): ' . $nom);
            echo json_encode([
                'status' => 'error',
                'exists' => false,
                'message' => 'Le nom doit contenir au moins 3 caractères'
            ]);
            exit;
        }

        try {
            // Vérifier en base de données
            $userModel = new UsersModel();
            $sql = "SELECT id FROM users WHERE nom = ?";
            $db = \App\Core\Db::getInstance();
            $stmt = $db->prepare($sql);
            $stmt->execute([$nom]);
            $user = $stmt->fetch();

            $exists = $user !== false;

            // Répondre en JSON
            echo json_encode([
                'status' => 'success',
                'exists' => $exists,
                'message' => $exists ? 'Ce nom est déjà utilisé' : 'Nom disponible'
            ]);
            exit;
        } catch (\PDOException $e) {
            // Erreur BD - répondre avec un code d'erreur JSON
            ErrorHandler::logDatabaseError($e, 'vérification unicité nom (nom: ' . $nom . ')', [
                'action' => 'users/checkNom',
                'method' => 'SELECT'
            ]);

            // Répondre avec un statut d'erreur (sans exposer détails technique)
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'exists' => false,
                'message' => 'Erreur serveur - Veuillez réessayer'
            ]);
            exit;
        }
    }
}