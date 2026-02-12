<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\RecipesModel;

class MainController extends Controller
{
    public function index()
    {
        // 1. On instancie le modèle
        $model = new RecipesModel();

        // 2. On récupère les recettes (avec l'auteur si possible, sinon findAll())
        // Si tu n'as pas encore créé d'utilisateurs, utilise findAll() simple pour tester
        // $recettes = $model->findAll(); 
        
        // Utilisons la version simple pour commencer si ta table est vide ou sans jointure parfaite
        $recettes = $model->findAll(); 

        // 3. On envoie à la vue
        $this->render('main/index', [
            'recettes' => $recettes,
            'titre' => 'Accueil - Marmiton Exam'
        ]);
    }
}