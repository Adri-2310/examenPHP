<?php
namespace App\Core;

abstract class Controller
{
    /**
     * Affiche une vue
     * @param string $fichier  Chemin de la vue (ex: 'main/index')
     * @param array $donnees   Données à passer à la vue (ex: ['recettes' => $liste])
     */
    public function render(string $fichier, array $donnees = [], string $template = 'base')
    {
        // On extrait le contenu des données
        // ex: ['titre' => 'Miam'] devient la variable $titre = 'Miam'
        extract($donnees);

        // On démarre le buffer de sortie
        // (ça veut dire : "Garde le HTML en mémoire, ne l'affiche pas tout de suite")
        ob_start();

        // On crée le chemin vers la vue
        require_once ROOT . '/views/' . $fichier . '.php';

        // On stocke le contenu de la vue dans $contenu
        $contenu = ob_get_clean();

        // On inclut le template principal (header + footer)
        require_once ROOT . '/views/' . $template . '.php';
    }
}