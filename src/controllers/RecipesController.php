<?php
/**
 * Nom du fichier : RecipesController.php
 *
 * Description :
 * Contrôleur gérant toutes les opérations CRUD sur les recettes personnelles.
 * Permet aux utilisateurs de créer, lire, modifier et supprimer leurs propres recettes.
 *
 * Fonctionnalités principales :
 * - Affichage de la liste des recettes personnelles
 * - Ajout de nouvelles recettes avec upload d'image
 * - Lecture de recette en détail
 * - Modification de recettes existantes
 * - Suppression de recettes avec gestion de l'image associée
 *
 * Sécurité :
 * - Vérification de connexion utilisateur
 * - Contrôle de propriété (user_id) avant modification/suppression
 * - Nettoyage des données (strip_tags)
 * - Validation des extensions d'images
 * - Requêtes préparées
 *
 * @package    App\Controllers
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\RecipesModel;

class RecipesController extends Controller
{
    /**
     * Affiche la page "Mes Recettes" avec toutes les recettes créées par l'utilisateur
     *
     * Cette méthode récupère et affiche uniquement les recettes appartenant
     * à l'utilisateur actuellement connecté (filtrage par user_id).
     *
     * Page accessible uniquement aux utilisateurs connectés.
     *
     * @return void Affiche la vue recipes/index.php
     *
     * @security Redirection vers /users/login si non connecté
     * @security Filtre automatique par user_id (pas d'accès aux recettes des autres)
     */
    public function index()
    {
        // Vérification de la connexion utilisateur
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // Récupération uniquement des recettes créées par l'utilisateur connecté
        $recipesModel = new RecipesModel();
        $mesCreations = $recipesModel->findAllByUserId($_SESSION['user']['id']);

        // Affichage de la vue avec les recettes
        $this->render('recipes/index', [
            'mesCreations' => $mesCreations,
            'titre' => 'Mes Propres Recettes'
        ]);
    }

    /**
     * Affiche le formulaire et traite l'ajout d'une nouvelle recette
     *
     * Cette méthode gère à la fois l'affichage du formulaire de création
     * et le traitement de la soumission avec upload d'image optionnel.
     *
     * Processus de création :
     * 1. Validation des champs obligatoires (titre, description, ingrédients, instructions)
     * 2. Nettoyage des données (strip_tags pour éviter XSS)
     * 3. Transformation des ingrédients en JSON
     * 4. Upload d'image optionnel avec validation
     * 5. Insertion en base de données avec requête préparée
     *
     * @return void Affiche la vue recipes/ajouter.php ou redirige vers /recipes
     *
     * @security Vérification de connexion utilisateur
     * @security Nettoyage strip_tags() contre XSS
     * @security Validation des extensions d'images (jpg, jpeg, png, webp)
     * @security Nom de fichier unique (uniqid) pour éviter les collisions
     * @security Requête préparée contre injection SQL
     */
    public function ajouter()
    {
        // Vérification de la connexion utilisateur
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // ===== TRAITEMENT DU FORMULAIRE =====
        if (!empty($_POST)) {
            // Validation des champs obligatoires
            if (!empty($_POST['title']) && !empty($_POST['description']) && !empty($_POST['ingredients']) && !empty($_POST['instructions'])) {

                // 1. Nettoyage des données utilisateur (protection XSS)
                $title = strip_tags($_POST['title']);
                $description = strip_tags($_POST['description']);
                $instructions = strip_tags($_POST['instructions']);

                // 2. Transformation des ingrédients en JSON
                // "tomate, oignon, ail" → ["tomate", "oignon", "ail"]
                $ingredientsArray = explode(',', $_POST['ingredients']);
                $ingredientsArray = array_map('trim', $ingredientsArray);
                $ingredientsJson = json_encode($ingredientsArray);

                // ===== GESTION DE L'UPLOAD D'IMAGE =====
                $image_url = null; // Par défaut : aucune image

                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

                    // 3.1. Extraction et validation de l'extension
                    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp'];

                    if (in_array($extension, $extensions_autorisees)) {

                        // 3.2. Chemin absolu vers le dossier d'upload
                        // dirname(__DIR__, 2) remonte de 2 niveaux : controllers → src → racine
                        $dossierUpload = dirname(__DIR__, 2) . '/public/uploads/';

                        // 3.3. Création du dossier si inexistant
                        if (!is_dir($dossierUpload)) {
                            mkdir($dossierUpload, 0777, true);
                        }

                        // 3.4. Génération d'un nom unique pour éviter les collisions
                        $nomUnique = uniqid() . '.' . $extension;
                        $cheminComplet = $dossierUpload . $nomUnique;

                        // 3.5. Déplacement du fichier temporaire vers le dossier final
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $cheminComplet)) {
                            // Stockage du chemin relatif (pour l'affichage HTML)
                            $image_url = '/uploads/' . $nomUnique;
                        }
                    }
                }
                // ===== FIN UPLOAD =====

                // 4. Insertion en base de données avec requête préparée
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

                // 5. Redirection vers la liste des recettes
                header('Location: /recipes');
                exit;
            } else {
                $erreur = "Veuillez remplir tous les champs obligatoires.";
            }
        }

        // Affichage du formulaire de création
        $this->render('recipes/ajouter', [
            'erreur' => $erreur ?? null,
            'titre' => 'Créer une recette'
        ]);
    }

    /**
     * Affiche une recette en détail (lecture seule)
     *
     * Cette méthode récupère et affiche toutes les informations d'une recette
     * spécifique. La page est accessible à tous (même non connectés).
     *
     * @param int $id Identifiant de la recette à afficher
     * @return void Affiche la vue recipes/lire.php ou redirige si inexistante
     *
     * @note Accessible sans connexion (lecture publique)
     */
    public function lire($id)
    {
        $recipesModel = new RecipesModel();
        $recette = $recipesModel->find($id);

        // Redirection si la recette n'existe pas
        if (!$recette) {
            header('Location: /recipes');
            exit;
        }

        // Affichage de la vue de détail
        $this->render('recipes/lire', [
            'recette' => $recette,
            'titre' => $recette->title
        ]);
    }

    /**
     * Affiche le formulaire et traite la modification d'une recette existante
     *
     * Cette méthode gère à la fois l'affichage du formulaire pré-rempli
     * et le traitement de la soumission des modifications.
     *
     * Sécurité renforcée :
     * - Vérification de la connexion
     * - Vérification de la propriété (user_id)
     * - Seul le créateur peut modifier sa recette
     *
     * @param int $id Identifiant de la recette à modifier
     * @return void Affiche la vue recipes/edit.php ou redirige
     *
     * @security Vérification user_id (un utilisateur ne peut modifier que ses propres recettes)
     * @security Nettoyage strip_tags() contre XSS
     * @security Requête préparée contre injection SQL
     */
    public function edit($id)
    {
        // Vérification de la connexion utilisateur
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        $recipesModel = new RecipesModel();
        $recette = $recipesModel->find($id);

        // Vérification de propriété : la recette doit appartenir à l'utilisateur connecté
        if (!$recette || $recette->user_id !== $_SESSION['user']['id']) {
            header('Location: /recipes');
            exit;
        }

        // ===== TRAITEMENT DU FORMULAIRE DE MODIFICATION =====
        if (!empty($_POST)) {
            if (!empty($_POST['title']) && !empty($_POST['description']) && !empty($_POST['ingredients']) && !empty($_POST['instructions'])) {

                // 1. Nettoyage des données (protection XSS)
                $title = strip_tags($_POST['title']);
                $description = strip_tags($_POST['description']);
                $instructions = strip_tags($_POST['instructions']);

                // 2. Transformation des ingrédients en JSON
                $ingredientsArray = explode(',', $_POST['ingredients']);
                $ingredientsArray = array_map('trim', $ingredientsArray);
                $ingredientsJson = json_encode($ingredientsArray);

                // 3. Mise à jour en base de données avec requête préparée
                $sql = "UPDATE recipes SET title = ?, description = ?, ingredients = ?, instructions = ? WHERE id = ?";
                $db = \App\Core\Db::getInstance();
                $stmt = $db->prepare($sql);
                $stmt->execute([$title, $description, $ingredientsJson, $instructions, $id]);

                // 4. Redirection vers la page de détail de la recette
                header('Location: /recipes/lire/' . $id);
                exit;
            }
        }

        // ===== AFFICHAGE DU FORMULAIRE PRÉ-REMPLI =====
        // Conversion des ingrédients JSON en chaîne de caractères pour l'affichage
        // ["tomate", "oignon"] → "tomate, oignon"
        $ingredientsList = '';
        $ingArr = json_decode($recette->ingredients, true);
        if (is_array($ingArr)) {
            $ingredientsList = implode(', ', $ingArr);
        } else {
            // Fallback si les données ne sont pas au format JSON
            $ingredientsList = $recette->ingredients;
        }

        // Affichage du formulaire de modification
        $this->render('recipes/edit', [
            'recette' => $recette,
            'ingredientsList' => $ingredientsList,
            'titre' => 'Modifier : ' . $recette->title
        ]);
    }

    /**
     * Supprime une recette et son image associée
     *
     * Cette méthode effectue une suppression complète en 2 étapes :
     * 1. Suppression du fichier image physique (si existant)
     * 2. Suppression de l'enregistrement en base de données
     *
     * Sécurité renforcée :
     * - Vérification de la connexion
     * - Vérification de la propriété (user_id)
     * - Seul le créateur peut supprimer sa recette
     *
     * @param int $id Identifiant de la recette à supprimer
     * @return void Redirige vers /recipes
     *
     * @security Vérification user_id (un utilisateur ne peut supprimer que ses propres recettes)
     * @security Requête préparée contre injection SQL
     * @note Suppression cascade : image physique + enregistrement BDD
     */
    public function delete($id)
    {
        // 1. Vérification de la connexion utilisateur
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // 2. Récupération de la recette pour vérifier la propriété et l'image
        $recipesModel = new \App\Models\RecipesModel();
        $recette = $recipesModel->find($id);

        // 3. Vérification de propriété : la recette doit appartenir à l'utilisateur connecté
        if ($recette && $recette->user_id === $_SESSION['user']['id']) {

            // ===== ÉTAPE A : SUPPRESSION DU FICHIER IMAGE =====
            if (!empty($recette->image_url)) {
                // Reconstruction du chemin absolu vers le fichier
                // Exemple : /public/uploads/abc123.jpg
                $cheminFichier = dirname(__DIR__, 2) . '/public' . $recette->image_url;

                // Suppression physique du fichier si existant
                if (file_exists($cheminFichier)) {
                    unlink($cheminFichier);
                }
            }
            // ===== FIN SUPPRESSION IMAGE =====

            // ===== ÉTAPE B : SUPPRESSION DE L'ENREGISTREMENT EN BDD =====
            $sql = "DELETE FROM recipes WHERE id = ?";
            $db = \App\Core\Db::getInstance();
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
        }

        // 4. Redirection vers la liste des recettes
        header('Location: /recipes');
        exit;
    }
}