<?php
/**
 * Nom du fichier : MainController.php
 *
 * Description :
 * Contrôleur principal gérant la page d'accueil de l'application.
 * Affiche les favoris de l'utilisateur et 3 recettes aléatoires de l'API TheMealDB.
 *
 * Fonctionnalités principales :
 * - Affichage de la page d'accueil
 * - Récupération des favoris utilisateur (si connecté)
 * - Appel à l'API TheMealDB pour obtenir des recettes aléatoires
 *
 * API externe : TheMealDB (https://www.themealdb.com/api.php)
 *
 * @package    App\Controllers
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\FavoritesModel;

class MainController extends Controller
{
    /**
     * Affiche la page d'accueil de l'application
     *
     * Cette méthode charge et affiche :
     * - Les favoris de l'utilisateur connecté (si authentifié)
     * - 3 recettes aléatoires provenant de l'API TheMealDB
     *
     * Processus :
     * 1. Récupération des favoris si l'utilisateur est connecté
     * 2. Appel à l'API TheMealDB 3 fois pour obtenir des recettes différentes
     * 3. Rendu de la vue avec les données récupérées
     *
     * @return void Affiche la vue main/index.php
     *
     * @note L'opérateur @ devant file_get_contents supprime les warnings si l'API est indisponible
     */
    public function index()
    {
        $favoris = [];
        $randomRecipes = [];

        // ===== ÉTAPE 1 : GESTION DES FAVORIS (Si connecté) =====
        if (isset($_SESSION['user'])) {
            $favModel = new FavoritesModel();
            $favoris = $favModel->findAllByUserId($_SESSION['user']['id']);
        }

        // ===== ÉTAPE 2 : RÉCUPÉRATION DE 3 RECETTES ALÉATOIRES (Via API) =====
        // Appel de l'API TheMealDB pour obtenir 3 recettes différentes
        // Endpoint : GET https://www.themealdb.com/api/json/v1/1/random.php
        for ($i = 0; $i < 3; $i++) {
            // Appel HTTP côté serveur (PHP) pour récupérer une recette aléatoire
            // L'opérateur @ supprime les warnings en cas d'échec de connexion
            $json = @file_get_contents('https://www.themealdb.com/api/json/v1/1/random.php');

            if ($json) {
                // Décodage du JSON en tableau associatif
                $data = json_decode($json, true);

                // Vérification de la présence de la recette dans la réponse
                if (isset($data['meals'][0])) {
                    $randomRecipes[] = $data['meals'][0];
                }
            }
        }

        // ===== ÉTAPE 3 : ENVOI DES DONNÉES À LA VUE =====
        $this->render('main/index', [
            'favoris' => $favoris,
            'randomRecipes' => $randomRecipes,
            'titre' => 'Accueil - Marmiton Exam'
        ]);
    }
}