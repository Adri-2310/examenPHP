<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\RecipesModel;

class RecipesController extends Controller
{
    /**
     * Afficher MES recettes personnelles
     */
    public function index()
    {
        // Sécurité : Être connecté
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // Récupérer uniquement mes créations
        $recipesModel = new RecipesModel();
        $mesCreations = $recipesModel->findAllByUserId($_SESSION['user']['id']);

        // On envoie à la vue dédiée
        $this->render('recipes/index', [
            'mesCreations' => $mesCreations,
            'titre' => 'Mes Propres Recettes'
        ]);
    }

    /**
     * Supprimer une de MES recettes
     */
    public function delete($id)
    {
        // Sécurité : Être connecté
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // On supprime uniquement si l'ID correspond ET que c'est le bon utilisateur
        $sql = "DELETE FROM recipes WHERE id = ? AND user_id = ?";
        $db = \App\Core\Db::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute([$id, $_SESSION['user']['id']]);

        // Redirection vers mes recettes
        header('Location: /recipes');
        exit;
    }
}