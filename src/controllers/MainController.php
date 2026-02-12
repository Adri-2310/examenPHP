<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\FavoritesModel;

class MainController extends Controller
{
    public function index()
    {
        $favoris = [];
        $randomRecipes = [];

        // 1. GESTION DES FAVORIS (Si connecté)
        if (isset($_SESSION['user'])) {
            $favModel = new FavoritesModel();
            $favoris = $favModel->findAllByUserId($_SESSION['user']['id']);
        }

        // 2. RECUPERATION DE 3 RECETTES AU HASARD (Via l'API)
        // On fait une boucle pour en récupérer 3 différentes
        for ($i = 0; $i < 3; $i++) {
            // On utilise file_get_contents pour appeler l'API depuis le PHP (Côté Serveur)
            $json = @file_get_contents('https://www.themealdb.com/api/json/v1/1/random.php');
            
            if ($json) {
                $data = json_decode($json, true);
                if (isset($data['meals'][0])) {
                    $randomRecipes[] = $data['meals'][0];
                }
            }
        }

        // 3. ENVOI A LA VUE
        $this->render('main/index', [
            'favoris' => $favoris,
            'randomRecipes' => $randomRecipes,
            'titre' => 'Accueil - Marmiton Exam'
        ]);
    }
}