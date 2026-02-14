<?php
/**
 * Nom du fichier : FavoritesController.php
 *
 * Description :
 * Contrôleur gérant les recettes favorites provenant de l'API TheMealDB.
 * Permet aux utilisateurs de sauvegarder, afficher et supprimer leurs favoris.
 *
 * Fonctionnalités principales :
 * - Affichage de la liste des favoris de l'utilisateur
 * - Ajout d'une recette API aux favoris (avec vérification de doublons)
 * - Suppression d'un favori
 *
 * Sécurité :
 * - Vérification de connexion utilisateur
 * - Contrôle de propriété (user_id) avant suppression
 * - Prévention des doublons avec exists()
 * - Requêtes préparées
 *
 * @package    App\Controllers
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\FavoritesModel;

class FavoritesController extends Controller
{
    /**
     * Affiche la page "Mes Favoris" avec toutes les recettes sauvegardées
     *
     * Cette méthode récupère et affiche tous les favoris de l'utilisateur connecté.
     * Page accessible uniquement aux utilisateurs authentifiés.
     *
     * @return void Affiche la vue favorites/index.php
     *
     * @security Redirection vers /users/login si non connecté
     */
    public function index()
    {
        // Vérification de la connexion utilisateur
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // Récupération des favoris de l'utilisateur
        $favModel = new FavoritesModel();
        $favoris = $favModel->findAllByUserId($_SESSION['user']['id']);

        // Affichage de la vue
        $this->render('favorites/index', ['favoris' => $favoris, 'titre' => 'Mes Favoris']);
    }

    /**
     * Ajoute une recette API aux favoris de l'utilisateur
     *
     * Cette méthode traite l'ajout d'une recette provenant de TheMealDB
     * dans la liste des favoris personnels de l'utilisateur.
     *
     * Données reçues via POST :
     * - id_api : Identifiant de la recette dans TheMealDB
     * - titre : Nom de la recette
     * - image_url : URL de l'image de la recette
     *
     * Processus :
     * 1. Vérification d'existence pour éviter les doublons
     * 2. Insertion en base de données
     * 3. Redirection vers /favorites
     *
     * @return void Redirige vers /favorites
     *
     * @security Vérification de connexion (exit si non connecté)
     * @security Vérification de doublons avec exists()
     * @security Requête préparée contre injection SQL
     */
    public function add()
    {
        // Vérification de la connexion (exit silencieux pour appel AJAX)
        if (!isset($_SESSION['user'])) exit;

        // ===== TRAITEMENT DE L'AJOUT =====
        if (!empty($_POST)) {
            // Validation du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    // ON AFFICHE LES DEUX VALEURS POUR COMPARER
                    $recu = $_POST['csrf_token'] ?? 'VIDE';
                    $attendu = $_SESSION['csrf_token'] ?? 'VIDE';
                    die("Erreur de sécurité : Token Reçu [$recu] vs Token Attendu [$attendu]");
                }
            }

            $favModel = new FavoritesModel();

            // Vérification de doublons : évite d'ajouter 2 fois la même recette
            if (!$favModel->exists($_SESSION['user']['id'], $_POST['id_api'])) {
                // SÉCURITÉ : Nettoyage et validation des données provenant de l'API externe
                $titre = strip_tags($_POST['titre']); // Supprime les balises HTML/JavaScript

                // Validation stricte de l'URL de l'image
                $image_url = filter_var($_POST['image_url'], FILTER_VALIDATE_URL);
                if ($image_url === false) {
                    die("Erreur : URL d'image invalide");
                }

                // Insertion du favori en base de données avec données validées
                $sql = "INSERT INTO favorites (user_id, id_api, titre, image_url) VALUES (?, ?, ?, ?)";
                $db = \App\Core\Db::getInstance();
                $stmt = $db->prepare($sql);
                $stmt->execute([$_SESSION['user']['id'], $_POST['id_api'], $titre, $image_url]);

                // message success
                $_SESSION['toasts'][] = [
                    'type' => 'success',
                    'message' => 'Recette ajoutée à vos favoris !'
                ];
            } else {
                // message recette déjà en favori
                $_SESSION['toasts'][] = [
                    'type' => 'info',
                    'message' => 'Cette recette est déjà dans vos favoris.'
                ];
            }

            // Redirection vers la page des favoris
            header('Location: /favorites');
            exit;
        }
    }

    /**
     * Supprime un favori de la liste de l'utilisateur
     *
     * Cette méthode supprime définitivement un favori.
     *
     * Sécurité renforcée :
     * - Double condition : id ET user_id
     * - Un utilisateur ne peut supprimer que ses propres favoris
     *
     * @param int $id Identifiant du favori à supprimer
     * @return void Redirige vers /favorites
     *
     * @security Vérification de connexion (exit si non connecté)
     * @security Clause AND user_id (un utilisateur ne peut supprimer que ses favoris)
     * @security Requête préparée contre injection SQL
     */
    public function delete($id)
    {
        // Vérification de la connexion (exit silencieux)
        if (!isset($_SESSION['user'])) exit;

        // Validation du token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Erreur de sécurité : Token CSRF invalide");
        }

        // Suppression avec double vérification : id ET user_id
        // Cela empêche un utilisateur de supprimer les favoris d'un autre
        $sql = "DELETE FROM favorites WHERE id = ? AND user_id = ?";
        $db = \App\Core\Db::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute([$id, $_SESSION['user']['id']]);

        // message de succès
        $_SESSION['toasts'][] = [
            'type' => 'success',
            'message' => 'Recette supprimée de vos favoris !'
        ];

        // Redirection vers la page des favoris
        header('Location: /favorites');
        exit;
    }
}