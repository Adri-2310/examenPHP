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
use App\Core\ErrorHandler;
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

        try {
            // Récupération uniquement des recettes créées par l'utilisateur connecté
            $recipesModel = new RecipesModel();
            $mesCreations = $recipesModel->findAllByUserId($_SESSION['user']['id']);

            // Affichage de la vue avec les recettes
            $this->render('recipes/index', [
                'mesCreations' => $mesCreations,
                'titre' => 'Mes Propres Recettes'
            ]);
        } catch (\PDOException $e) {
            // Log l'erreur avec contexte
            ErrorHandler::logDatabaseError($e, 'chargement de vos recettes', [
                'action' => 'recipes/index',
                'method' => 'findAllByUserId'
            ]);

            // Redirige avec message d'erreur
            header('Location: /');
            exit;
        }
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
            // ==========================================
            // VÉRIFICATION DE LA SÉCURITÉ CSRF
            // ==========================================
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                // Si le jeton est absent ou faux, on bloque tout !
                die("Erreur de sécurité : Requête non autorisée (Token CSRF invalide).");
            }
            // ==========================================

            // Validation des champs obligatoires (nouveau format d'ingrédients)
            if (!empty($_POST['title']) && !empty($_POST['description']) && isset($_POST['ingredients']) && !empty($_POST['instructions'])) {

                // 1. Nettoyage des données utilisateur (protection XSS)
                $title = strip_tags($_POST['title']);
                $description = strip_tags($_POST['description']);
                $instructions = strip_tags($_POST['instructions']);

                // 2. Transformation des ingrédients en JSON (nouveau format avec quantités)
                // Nouveau format : ingredients[name][] et ingredients[qty][]
                // Exemple : {"name": "Tomate", "qty": "500g"}
                $ingredientsArray = [];

                if (isset($_POST['ingredients']['name']) && isset($_POST['ingredients']['qty'])) {
                    $names = $_POST['ingredients']['name'];
                    $quantities = $_POST['ingredients']['qty'];

                    // Vérifier que les deux tableaux ont la même longueur
                    if (is_array($names) && is_array($quantities) && count($names) === count($quantities)) {
                        foreach ($names as $index => $name) {
                            $cleanName = strip_tags(trim($name));
                            $cleanQty = strip_tags(trim($quantities[$index]));

                            // Vérifier que le nom n'est pas vide
                            if (!empty($cleanName)) {
                                $ingredientsArray[] = [
                                    'name' => $cleanName,
                                    'qty' => $cleanQty
                                ];
                            }
                        }
                    }
                }

                // Si aucun ingrédient valide, on rejette
                if (empty($ingredientsArray)) {
                    $erreur = "Vous devez ajouter au moins un ingrédient.";
                }

                $ingredientsJson = json_encode($ingredientsArray);

                // ===== GESTION DE L'UPLOAD D'IMAGE =====
                $image_url = null; // Par défaut : aucune image

                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

                    // 3.1. Validation de la taille du fichier (limite à 5 MB)
                    $maxSize = 5 * 1024 * 1024; // 5 MB
                    if ($_FILES['image']['size'] > $maxSize) {
                        $erreur = "L'image ne doit pas dépasser 5 MB";
                    } else {
                        try {
                            // 3.2. Vérification du type MIME réel (sécurité renforcée)
                            $finfo = new \finfo(FILEINFO_MIME_TYPE);
                            $mimeType = $finfo->file($_FILES['image']['tmp_name']);
                            $mimes_autorises = ['image/jpeg', 'image/png', 'image/webp'];

                            if (!in_array($mimeType, $mimes_autorises)) {
                                $erreur = "Type de fichier non autorisé. Formats acceptés : JPG, PNG, WEBP";
                            } else {
                                // 3.3. Extraction et validation de l'extension
                                $extension = strtolower(pathinfo(basename($_FILES['image']['name']), PATHINFO_EXTENSION));
                                $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp'];

                                if (in_array($extension, $extensions_autorisees)) {

                                    // 3.4. Chemin absolu vers le dossier d'upload
                                    // dirname(__DIR__, 2) remonte de 2 niveaux : controllers → src → racine
                                    $dossierUpload = dirname(__DIR__, 2) . '/public/uploads/';

                                    // 3.5. Création du dossier avec permissions sécurisées (755 au lieu de 777)
                                    if (!is_dir($dossierUpload)) {
                                        mkdir($dossierUpload, 0755, true);
                                    }

                                    // 3.6. Génération d'un nom unique pour éviter les collisions
                                    $nomUnique = uniqid() . '.' . $extension;
                                    $cheminComplet = $dossierUpload . $nomUnique;

                                    // 3.7. Déplacement du fichier temporaire vers le dossier final
                                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $cheminComplet)) {
                                        // Erreur de déplacement du fichier
                                        ErrorHandler::logFileError(
                                            "Impossible de déplacer le fichier: {$_FILES['image']['tmp_name']} → {$cheminComplet}",
                                            'upload image',
                                            ['action' => 'recipes/ajouter']
                                        );
                                        $erreur = "❌ Erreur lors du téléchargement de l'image";
                                    } else {
                                        // Stockage du chemin relatif (pour l'affichage HTML)
                                        $image_url = '/uploads/' . $nomUnique;
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Erreur lors de la vérification du MIME
                            ErrorHandler::logFileError(
                                $e->getMessage(),
                                'vérification image MIME',
                                ['action' => 'recipes/ajouter']
                            );
                            $erreur = "❌ Erreur lors de la vérification de l'image";
                        }
                    }
                }
                // ===== FIN UPLOAD =====

                // 4. Insertion en base de données avec requête préparée
                if (!isset($erreur)) {
                    try {
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

                        // 5. message de succès
                        $_SESSION['toasts'][] = [
                            'type' => 'success',
                            'message' => '✅ Recette créée avec succès !'
                        ];

                        // 6. Redirection vers la liste des recettes
                        header('Location: /recipes');
                        exit;
                    } catch (\PDOException $e) {
                        // Erreur lors de l'insertion
                        ErrorHandler::logDatabaseError($e, 'création de recette', [
                            'action' => 'recipes/ajouter',
                            'method' => 'INSERT'
                        ]);

                        // Si upload d'image a réussi, le nettoyer
                        if ($image_url) {
                            $cheminFichier = dirname(__DIR__, 2) . '/public' . $image_url;
                            @unlink($cheminFichier);
                        }

                        $erreur = "❌ Erreur lors de la création de la recette";
                    }
                }
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
        // Validation de l'ID (doit être numérique)
        if (!is_numeric($id) || $id <= 0) {
            ErrorHandler::logValidationError('id', 'ID recette invalide (non numérique)', '❌ Recette non trouvée');
            header('Location: /recipes');
            exit;
        }

        try {
            $recipesModel = new RecipesModel();
            $recette = $recipesModel->find($id);

            // Redirection si la recette n'existe pas
            if (!$recette) {
                ErrorHandler::logValidationError('id', 'Recette introuvable (ID: ' . $id . ')');
                header('Location: /recipes');
                exit;
            }

            // Affichage de la vue de détail
            $this->render('recipes/lire', [
                'recette' => $recette,
                'titre' => $recette->title
            ]);
        } catch (\PDOException $e) {
            // Erreur BD
            ErrorHandler::logDatabaseError($e, 'lecture recette (ID: ' . $id . ')', [
                'action' => 'recipes/lire',
                'method' => 'find'
            ]);

            header('Location: /recipes');
            exit;
        }
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

        // Validation de l'ID
        if (!is_numeric($id) || $id <= 0) {
            ErrorHandler::logValidationError('id', 'ID recette invalide (non numérique)', '❌ Recette non trouvée');
            header('Location: /recipes');
            exit;
        }

        try {
            $recipesModel = new RecipesModel();
            $recette = $recipesModel->find($id);

            // Vérification de propriété : la recette doit appartenir à l'utilisateur connecté
            if (!$recette) {
                ErrorHandler::logValidationError('id', 'Recette introuvable (ID: ' . $id . ')');
                header('Location: /recipes');
                exit;
            }

            if ($recette->user_id !== $_SESSION['user']['id']) {
                ErrorHandler::logAccessDenied('recipes/' . $id . '/edit', 'Utilisateur n\'est pas propriétaire de la recette');
                header('Location: /recipes');
                exit;
            }

            // ===== TRAITEMENT DU FORMULAIRE DE MODIFICATION =====
            if (!empty($_POST)) {
                // Validation du token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    die("Erreur de sécurité : Token CSRF invalide");
                }

                if (!empty($_POST['title']) && !empty($_POST['description']) && isset($_POST['ingredients']) && !empty($_POST['instructions'])) {

                    // 1. Nettoyage des données (protection XSS)
                    $title = strip_tags($_POST['title']);
                    $description = strip_tags($_POST['description']);
                    $instructions = strip_tags($_POST['instructions']);

                    // 2. Transformation des ingrédients en JSON (nouveau format avec quantités)
                    $ingredientsArray = [];

                    if (isset($_POST['ingredients']['name']) && isset($_POST['ingredients']['qty'])) {
                        $names = $_POST['ingredients']['name'];
                        $quantities = $_POST['ingredients']['qty'];

                        if (is_array($names) && is_array($quantities) && count($names) === count($quantities)) {
                            foreach ($names as $index => $name) {
                                $cleanName = strip_tags(trim($name));
                                $cleanQty = strip_tags(trim($quantities[$index]));

                                if (!empty($cleanName)) {
                                    $ingredientsArray[] = [
                                        'name' => $cleanName,
                                        'qty' => $cleanQty
                                    ];
                                }
                            }
                        }
                    }

                    // Si aucun ingrédient valide, on rejette
                    if (empty($ingredientsArray)) {
                        $erreur = "Vous devez ajouter au moins un ingrédient.";
                    }

                    if (!isset($erreur)) {
                        $ingredientsJson = json_encode($ingredientsArray);

                        // 3. Mise à jour en base de données avec requête préparée
                        try {
                            $sql = "UPDATE recipes SET title = ?, description = ?, ingredients = ?, instructions = ? WHERE id = ?";
                            $db = \App\Core\Db::getInstance();
                            $stmt = $db->prepare($sql);
                            $stmt->execute([$title, $description, $ingredientsJson, $instructions, $id]);

                            // 4. message de succès
                            $_SESSION['toasts'][] = [
                                'type' => 'success',
                                'message' => '✅ Recette modifiée avec succès !'
                            ];

                            // 5. Redirection vers la page de détail de la recette
                            header('Location: /recipes/lire/' . $id);
                            exit;
                        } catch (\PDOException $e) {
                            // Erreur lors de la mise à jour
                            ErrorHandler::logDatabaseError($e, 'modification recette (ID: ' . $id . ')', [
                                'action' => 'recipes/edit',
                                'method' => 'UPDATE'
                            ]);
                            $erreur = "❌ Erreur lors de la modification";
                        }
                    }
                }
            }

            // ===== AFFICHAGE DU FORMULAIRE PRÉ-REMPLI =====
            // Les ingrédients sont maintenant au format JSON avec name et qty
            // Pas besoin de conversion spéciale, on les passe directement à la vue
            // La vue parse le JSON et pré-remplit les inputs

            // Affichage du formulaire de modification
            $this->render('recipes/edit', [
                'recette' => $recette,
                'titre' => 'Modifier : ' . $recette->title,
                'erreur' => $erreur ?? null
            ]);
        } catch (\PDOException $e) {
            // Erreur BD lors du chargement
            ErrorHandler::logDatabaseError($e, 'chargement formulaire modification (ID: ' . $id . ')', [
                'action' => 'recipes/edit',
                'method' => 'find'
            ]);

            header('Location: /recipes');
            exit;
        }
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

        // Validation de l'ID
        if (!is_numeric($id) || $id <= 0) {
            ErrorHandler::logValidationError('id', 'ID recette invalide (non numérique)', '❌ Recette non trouvée');
            header('Location: /recipes');
            exit;
        }

        // ==========================================
        // VÉRIFICATION CSRF POUR LA SUPPRESSION (POST)
        // ==========================================
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Erreur de sécurité : Token CSRF invalide");
        }
        // ==========================================

        try {
            // 2. Récupération de la recette pour vérifier la propriété et l'image
            $recipesModel = new \App\Models\RecipesModel();
            $recette = $recipesModel->find($id);

            // 3. Vérification de propriété : la recette doit appartenir à l'utilisateur connecté
            if (!$recette) {
                ErrorHandler::logValidationError('id', 'Recette introuvable (ID: ' . $id . ')');
                header('Location: /recipes');
                exit;
            }

            if ($recette->user_id !== $_SESSION['user']['id']) {
                ErrorHandler::logAccessDenied('recipes/' . $id . '/delete', 'Utilisateur n\'est pas propriétaire de la recette');
                header('Location: /recipes');
                exit;
            }

            // ===== ÉTAPE A : SUPPRESSION DU FICHIER IMAGE =====
            if (!empty($recette->image_url)) {
                // Reconstruction du chemin absolu vers le fichier
                // Exemple : /public/uploads/abc123.jpg
                $cheminFichier = dirname(__DIR__, 2) . '/public' . $recette->image_url;

                // Suppression physique du fichier si existant
                if (file_exists($cheminFichier)) {
                    if (!unlink($cheminFichier)) {
                        // Erreur lors de la suppression du fichier (on continue quand même)
                        ErrorHandler::logFileError(
                            "Impossible de supprimer le fichier: {$cheminFichier}",
                            'suppression image',
                            ['action' => 'recipes/delete', 'recette_id' => $id]
                        );
                    }
                }
            }
            // ===== FIN SUPPRESSION IMAGE =====

            // ===== ÉTAPE B : SUPPRESSION DE L'ENREGISTREMENT EN BDD =====
            try {
                $sql = "DELETE FROM recipes WHERE id = ?";
                $db = \App\Core\Db::getInstance();
                $stmt = $db->prepare($sql);
                $stmt->execute([$id]);

                // ===== ÉTAPE C : message DE SUCCÈS =====
                $_SESSION['toasts'][] = [
                    'type' => 'success',
                    'message' => '✅ Recette supprimée avec succès !'
                ];
            } catch (\PDOException $e) {
                // Erreur lors de la suppression en BD
                ErrorHandler::logDatabaseError($e, 'suppression recette (ID: ' . $id . ')', [
                    'action' => 'recipes/delete',
                    'method' => 'DELETE'
                ]);

                // Message générique à l'utilisateur
                $_SESSION['toasts'][] = [
                    'type' => 'error',
                    'message' => '❌ Erreur lors de la suppression de la recette'
                ];
            }
        } catch (\PDOException $e) {
            // Erreur BD lors du chargement
            ErrorHandler::logDatabaseError($e, 'chargement recette pour suppression (ID: ' . $id . ')', [
                'action' => 'recipes/delete',
                'method' => 'find'
            ]);

            $_SESSION['toasts'][] = [
                'type' => 'error',
                'message' => '❌ Erreur lors du traitement'
            ];
        }

        // 4. Redirection vers la liste des recettes
        header('Location: /recipes');
        exit;
    }
}