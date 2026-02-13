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
     * Ajouter une nouvelle recette
     */
    public function ajouter()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        if (!empty($_POST)) {
            if (!empty($_POST['title']) && !empty($_POST['description']) && !empty($_POST['ingredients']) && !empty($_POST['instructions'])) {
                
                $title = strip_tags($_POST['title']);
                $description = strip_tags($_POST['description']);
                $instructions = strip_tags($_POST['instructions']);
                
                $ingredientsArray = explode(',', $_POST['ingredients']);
                $ingredientsArray = array_map('trim', $ingredientsArray);
                $ingredientsJson = json_encode($ingredientsArray);

                // ==========================================
                // GESTION DE L'UPLOAD DE L'IMAGE (CORRIGÉE)
                // ==========================================
                $image_url = null; // Par défaut, pas d'image
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    
                    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp'];
                    
                    if (in_array($extension, $extensions_autorisees)) {
                        
                        // 1. Chemin absolu vers le dossier public/uploads
                        $dossierUpload = dirname(__DIR__, 2) . '/public/uploads/';
                        
                        // 2. Création automatique du dossier s'il n'existe pas
                        if (!is_dir($dossierUpload)) {
                            mkdir($dossierUpload, 0777, true);
                        }

                        $nomUnique = uniqid() . '.' . $extension;
                        $cheminComplet = $dossierUpload . $nomUnique;
                        
                        // 3. Déplacement du fichier
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $cheminComplet)) {
                            $image_url = '/uploads/' . $nomUnique;
                        }
                    }
                }
                // ==========================================

                $sql = "INSERT INTO recipes (title, description, ingredients, instructions, user_id, image_url) VALUES (?, ?, ?, ?, ?, ?)";
                $db = \App\Core\Db::getInstance();
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $title, 
                    $description, 
                    $ingredientsJson, 
                    $instructions, 
                    $_SESSION['user']['id'],
                    $image_url
                ]);

                header('Location: /recipes');
                exit;
            } else {
                $erreur = "Veuillez remplir tous les champs obligatoires.";
            }
        }

        $this->render('recipes/ajouter', [
            'erreur' => $erreur ?? null,
            'titre' => 'Créer une recette'
        ]);
    }

    /**
     * Voir une recette en détail (Lecture)
     */
    public function lire($id)
    {
        $recipesModel = new RecipesModel();
        $recette = $recipesModel->find($id);

        if (!$recette) {
            header('Location: /recipes');
            exit;
        }

        $this->render('recipes/lire', [
            'recette' => $recette,
            'titre' => $recette->title
        ]);
    }

    /**
     * Modifier une recette existante
     */
    public function edit($id)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        $recipesModel = new RecipesModel();
        $recette = $recipesModel->find($id);

        // On vérifie que la recette appartient bien à l'utilisateur connecté
        if (!$recette || $recette->user_id !== $_SESSION['user']['id']) {
            header('Location: /recipes');
            exit;
        }

        if (!empty($_POST)) {
            if (!empty($_POST['title']) && !empty($_POST['description']) && !empty($_POST['ingredients']) && !empty($_POST['instructions'])) {
                
                $title = strip_tags($_POST['title']);
                $description = strip_tags($_POST['description']);
                $instructions = strip_tags($_POST['instructions']);
                
                $ingredientsArray = explode(',', $_POST['ingredients']);
                $ingredientsArray = array_map('trim', $ingredientsArray);
                $ingredientsJson = json_encode($ingredientsArray);

                $sql = "UPDATE recipes SET title = ?, description = ?, ingredients = ?, instructions = ? WHERE id = ?";
                $db = \App\Core\Db::getInstance();
                $stmt = $db->prepare($sql);
                $stmt->execute([$title, $description, $ingredientsJson, $instructions, $id]);

                header('Location: /recipes/lire/' . $id);
                exit;
            }
        }

        // Préparation des ingrédients pour l'affichage
        $ingredientsList = '';
        $ingArr = json_decode($recette->ingredients, true);
        if (is_array($ingArr)) {
            $ingredientsList = implode(', ', $ingArr);
        } else {
            $ingredientsList = $recette->ingredients;
        }

        $this->render('recipes/edit', [
            'recette' => $recette,
            'ingredientsList' => $ingredientsList,
            'titre' => 'Modifier : ' . $recette->title
        ]);
    }

    /**
     * Supprimer une de MES recettes
     */
    public function delete($id)
    {
        // 1. Sécurité : Être connecté
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // 2. On récupère d'abord la recette pour savoir si elle avait une image
        $recipesModel = new \App\Models\RecipesModel();
        $recette = $recipesModel->find($id);

        // 3. On vérifie que la recette existe et appartient bien à l'utilisateur
        if ($recette && $recette->user_id === $_SESSION['user']['id']) {
            
            // ==========================================
            // DESTRUCTION DE L'IMAGE PHYSIQUE
            // ==========================================
            if (!empty($recette->image_url)) {
                // On recrée le chemin exact vers le fichier sur l'ordinateur
                $cheminFichier = dirname(__DIR__, 2) . '/public' . $recette->image_url;
                
                // Si le fichier existe vraiment, on l'efface avec unlink()
                if (file_exists($cheminFichier)) {
                    unlink($cheminFichier);
                }
            }
            // ==========================================

            // 4. On supprime maintenant la ligne dans la base de données
            $sql = "DELETE FROM recipes WHERE id = ?";
            $db = \App\Core\Db::getInstance();
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
        }

        // 5. Redirection vers la liste
        header('Location: /recipes');
        exit;
    }
}