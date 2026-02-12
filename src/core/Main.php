<?php
namespace App\Core;

class Main
{
    public function start()
    {
        // On récupère l'URL (ex: recettes/liste)
        // On retire le "trailing slash" éventuel
        $uri = $_SERVER['REQUEST_URI'];
        
        // On vérifie que l'uri n'est pas vide et ne termine pas par un slash
        if(!empty($uri) && $uri != '/' && $uri[-1] === '/'){
            // On enlève le slash
            $uri = substr($uri, 0, -1);
            
            // On envoie un code de redirection permanente
            http_response_code(301);
            
            // On redirige vers l'URL sans /
            header('Location: '.$uri);
            exit;
        }

        // On gère les paramètres d'URL (p=controleur/methode)
        // Récupération des paramètres
        $params = [];
        if(isset($_GET['url'])){
            $params = explode('/', $_GET['url']);
        }

        if(!empty($params) && $params[0] != ""){
            // On a au moins un paramètre
            // On récupère le nom du contrôleur à instancier
            // On met une majuscule en 1ère lettre, on ajoute le namespace complet
            $controller = '\\App\\Controllers\\'.ucfirst(array_shift($params)).'Controller';

            // On instancie le contrôleur
            $controller = new $controller();

            // On récupère le 2ème paramètre d'URL (la méthode)
            $action = (isset($params[0])) ? array_shift($params) : 'index';

            if(method_exists($controller, $action)){
                // Si il reste des params on les passe à la méthode
                (isset($params[0])) ? call_user_func_array([$controller, $action], $params) : $controller->$action();
            }else{
                http_response_code(404);
                echo "La page recherchée n'existe pas";
            }
        }else{
            // Pas de paramètres => Page d'accueil
            // On instancie le contrôleur par défaut
            $controller = new \App\Controllers\MainController();
            $controller->index();
        }
    }
}