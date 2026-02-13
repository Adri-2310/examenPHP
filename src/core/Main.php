<?php
/**
 * Nom du fichier : Main.php
 *
 * Description :
 * Routeur principal de l'application MVC.
 * Analyse l'URL demandée et dispatche vers le contrôleur et l'action appropriés.
 *
 * Fonctionnalités principales :
 * - Gestion du trailing slash (redirection 301)
 * - Parsing de l'URL pour extraire contrôleur/action/paramètres
 * - Instanciation dynamique des contrôleurs
 * - Appel dynamique des méthodes avec paramètres
 * - Page d'accueil par défaut (MainController::index)
 *
 * Pattern utilisé : Front Controller
 *
 * Format d'URL supporté :
 * /?url=controleur/action/param1/param2/...
 *
 * Exemples :
 * /?url=recipes/lire/5          → RecipesController->lire(5)
 * /?url=favorites/ajouter/3     → FavoritesController->ajouter(3)
 * /                             → MainController->index()
 *
 * Sécurité :
 * - Validation de l'existence du contrôleur et de la méthode
 * - Code 404 si contrôleur/action inexistant
 *
 * @package    App\Core
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Core;

class Main
{
    /**
     * Démarre le routeur et dispatche la requête HTTP
     *
     * Cette méthode est le point d'entrée unique de toutes les requêtes HTTP.
     * Elle analyse l'URL, instancie le contrôleur approprié et exécute l'action demandée.
     *
     * Processus de routage (exemple avec /?url=recipes/lire/5) :
     * 1. Normalisation de l'URL (suppression trailing slash)
     * 2. Parsing : ["recipes", "lire", "5"]
     * 3. Contrôleur : "recipes" → App\Controllers\RecipesController
     * 4. Action : "lire"
     * 5. Paramètres : [5]
     * 6. Exécution : RecipesController->lire(5)
     *
     * @return void
     *
     * @security Vérifie l'existence du contrôleur et de la méthode avant exécution
     */
    public function start()
    {
        // ===== ÉTAPE 1 : NORMALISATION DE L'URL =====
        // Récupération de l'URI complète (ex: /recettes/liste ou /recettes/liste/)
        $uri = $_SERVER['REQUEST_URI'];
        
        // Gestion du "trailing slash" : /recettes/liste/ → /recettes/liste
        // Améliore le SEO (évite le duplicate content) et normalise les URLs
        if(!empty($uri) && $uri != '/' && $uri[-1] === '/'){
            // Suppression du slash final
            $uri = substr($uri, 0, -1);

            // Redirection permanente (301) vers l'URL normalisée
            http_response_code(301);
            header('Location: '.$uri);
            exit;
        }

        // ===== ÉTAPE 2 : PARSING DE L'URL =====
        // Extraction des paramètres depuis l'URL (?url=controleur/action/param1/param2)
        $params = [];
        if(isset($_GET['url'])){
            // Découpage de l'URL en segments
            // Exemple : "recipes/lire/5" → ["recipes", "lire", "5"]
            $params = explode('/', $_GET['url']);
        }

        if(!empty($params) && $params[0] != ""){
            // ===== ÉTAPE 3 : DISPATCH VERS UN CONTRÔLEUR SPÉCIFIQUE =====

            // 3.1. Construction du nom complet du contrôleur (FQCN)
            // Exemple : "recipes" → "\\App\\Controllers\\RecipesController"
            // ucfirst() met la première lettre en majuscule
            // array_shift() extrait et retire le premier élément du tableau
            $controller = '\\App\\Controllers\\'.ucfirst(array_shift($params)).'Controller';

            // 3.2. Instanciation dynamique du contrôleur
            // Équivalent à : $controller = new RecipesController();
            $controller = new $controller();

            // 3.3. Détermination de l'action (méthode) à exécuter
            // Si présent dans l'URL, on l'utilise ; sinon on utilise "index" par défaut
            // Exemple : ["lire", "5"] → "lire" (et il reste ["5"])
            $action = (isset($params[0])) ? array_shift($params) : 'index';

            // 3.4. Vérification de l'existence de la méthode et exécution
            if(method_exists($controller, $action)){
                // Exécution avec ou sans paramètres supplémentaires
                // call_user_func_array() permet de passer un nombre variable d'arguments
                // Exemple : call_user_func_array([RecipesController, "lire"], [5])
                //           équivaut à RecipesController->lire(5)
                (isset($params[0])) ? call_user_func_array([$controller, $action], $params) : $controller->$action();
            }else{
                // Gestion d'erreur : la méthode demandée n'existe pas
                http_response_code(404);
                echo "La page recherchée n'existe pas";
            }
        }else{
            // ===== ÉTAPE 4 : PAGE D'ACCUEIL PAR DÉFAUT =====
            // Aucun paramètre dans l'URL → Affichage de la page d'accueil
            $controller = new \App\Controllers\MainController();
            $controller->index();
        }
    }
}