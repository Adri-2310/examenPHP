<?php
/**
 * Nom du fichier : ApiController.php
 *
 * Description :
 * Contrôleur gérant l'intégration avec l'API externe TheMealDB.
 * Affiche l'interface de recherche et d'exploration des recettes de l'API.
 *
 * Fonctionnalités principales :
 * - Affichage de la page "Inspiration API"
 * - Interface de recherche de recettes externes
 * - Pont entre l'utilisateur et l'API TheMealDB
 *
 * API externe : TheMealDB (https://www.themealdb.com/api.php)
 * Les appels API se font principalement côté client (JavaScript) dans la vue.
 *
 * @package    App\Controllers
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Controllers;

use App\Core\Controller;

class ApiController extends Controller
{
    /**
     * Affiche la page de recherche d'inspiration de recettes via l'API
     *
     * Cette méthode affiche l'interface permettant aux utilisateurs
     * de rechercher des recettes dans l'API TheMealDB et de les ajouter à leurs favoris.
     *
     * Page accessible uniquement aux utilisateurs connectés (pour sauvegarder les favoris).
     *
     * @return void Affiche la vue api/index.php
     *
     * @security Redirection vers /users/login si non connecté
     *
     * @note Les appels API vers TheMealDB se font principalement en JavaScript
     *       côté client dans la vue api/index.php
     */
    public function index()
    {
        // Vérification de la connexion utilisateur
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // Affichage de l'interface de recherche API
        $this->render('api/index', ['titre' => 'Inspiration API']);
    }
}