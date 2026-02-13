<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\FavoritesModel;

class FavoritesController extends Controller
{
    /**
     * Afficher la page "Mes Favoris"
     */
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        $favModel = new FavoritesModel();
        $favoris = $favModel->findAllByUserId($_SESSION['user']['id']);

        $this->render('favorites/index', ['favoris' => $favoris, 'titre' => 'Mes Favoris']);
    }

    /**
     * Ajouter aux favoris depuis l'API
     */
    public function add()
    {
        if (!isset($_SESSION['user'])) exit;

        if (!empty($_POST)) {
            $favModel = new FavoritesModel();
            
            // On vérifie que la recette n'est pas déjà dans les favoris
            if (!$favModel->exists($_SESSION['user']['id'], $_POST['id_api'])) {
                $sql = "INSERT INTO favorites (user_id, id_api, titre, image_url) VALUES (?, ?, ?, ?)";
                $db = \App\Core\Db::getInstance();
                $stmt = $db->prepare($sql);
                $stmt->execute([$_SESSION['user']['id'], $_POST['id_api'], $_POST['titre'], $_POST['image_url']]);
            }
            header('Location: /favorites');
            exit;
        }
    }

    /**
     * Supprimer un favori
     */
    public function delete($id)
    {
        if (!isset($_SESSION['user'])) exit;
        
        $sql = "DELETE FROM favorites WHERE id = ? AND user_id = ?";
        $db = \App\Core\Db::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute([$id, $_SESSION['user']['id']]);
        
        header('Location: /favorites');
        exit;
    }
}