<?php
namespace App\Controllers;

use App\Core\Controller;

class ApiController extends Controller
{
    /**
     * Afficher la page de recherche d'inspiration
     */
    public function index()
    {
        // Sécurité : Être connecté pour chercher et sauvegarder
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        // On appelle le nouveau dossier de vue
        $this->render('api/index', ['titre' => 'Inspiration API']);
    }
}