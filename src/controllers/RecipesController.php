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

    /**
     * Ajouter une nouvelle recette
     */
    public function ajouter()
    {
        // 1. Sécurité : Vérifier qu'on est connecté
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // 2. Traitement du formulaire
        if (!empty($_POST)) {
            // On vérifie que les champs obligatoires sont remplis
            if (!empty($_POST['title']) && !empty($_POST['description']) && !empty($_POST['ingredients']) && !empty($_POST['instructions'])) {
                
                // Nettoyage de sécurité (contre les failles XSS)
                $title = strip_tags($_POST['title']);
                $description = strip_tags($_POST['description']);
                $instructions = strip_tags($_POST['instructions']);
                
                // Transformation des ingrédients : "Oeufs, Farine" -> ["Oeufs", "Farine"] (en JSON)
                $ingredientsArray = explode(',', $_POST['ingredients']);
                $ingredientsArray = array_map('trim', $ingredientsArray);
                $ingredientsJson = json_encode($ingredientsArray);

                // 3. Sauvegarde dans la base de données
                $sql = "INSERT INTO recipes (title, description, ingredients, instructions, user_id) VALUES (?, ?, ?, ?, ?)";
                $db = \App\Core\Db::getInstance();
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $title, 
                    $description, 
                    $ingredientsJson, 
                    $instructions, 
                    $_SESSION['user']['id']
                ]);

                // 4. Redirection vers la liste de tes recettes
                header('Location: /recipes');
                exit;
            } else {
                $erreur = "Veuillez remplir tous les champs obligatoires.";
            }
        }

        // 5. Affichage de la vue
        $this->render('recipes/ajouter', [
            'erreur' => $erreur ?? null,
            'titre' => 'Créer une recette'
        ]);
    }
}