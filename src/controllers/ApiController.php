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
use App\Core\ErrorHandler;

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

    /**
     * Affiche les détails complets d'une recette TheMealDB
     *
     * Récupère les données complètes d'une recette depuis l'API TheMealDB
     * et les affiche dans l'interface de l'application (page api/lire.php).
     * Implémente un système de cache en session (30 minutes) pour améliorer la performance.
     *
     * @param string $id_api L'ID de la recette sur TheMealDB
     * @return void Affiche la vue api/lire.php ou redirige vers /favorites en cas d'erreur
     *
     * @security Vérification XSS sur toutes les données API
     * @security Gestion d'erreur robuste (API down, timeout, JSON invalide)
     *
     * @note Cache en session pendant 30 minutes pour éviter les appels API répétés
     * @note Format des ingrédients transformé pour compatibilité avec lire.php local
     */
    public function lireRecette($id_api)
    {
        // Validation de l'ID API (doit être numérique)
        if (!is_numeric($id_api) || $id_api <= 0) {
            ErrorHandler::logValidationError('id_api', 'ID API invalide (non numérique)', '❌ Recette non trouvée');
            header('Location: /favorites');
            exit;
        }

        // ═══════════════════════════════════════════════════════════════
        // 1. VÉRIFIER LE CACHE EN SESSION (30 minutes)
        // ═══════════════════════════════════════════════════════════════

        $cacheKey = "api_recipe_{$id_api}";
        $cacheMaxAge = 1800; // 30 minutes en secondes

        if (isset($_SESSION[$cacheKey]) &&
            (time() - $_SESSION[$cacheKey]['timestamp']) < $cacheMaxAge) {

            // Les données sont en cache et encore valides
            $recette = $_SESSION[$cacheKey]['data'];
            ErrorHandler::log(
                "Cache HIT pour recette API {$id_api}",
                ErrorHandler::TYPE_INFO,
                null,
                ['action' => 'api/lireRecette', 'method' => 'cache_hit']
            );

        } else {

            // ═══════════════════════════════════════════════════════════════
            // 2. APPEL À L'API THEMEALDB
            // ═══════════════════════════════════════════════════════════════

            try {
                $url = "https://www.themealdb.com/api/json/v1/1/lookup.php?i={$id_api}";

                // Configuration du context avec timeout (5 secondes max)
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 5,
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    ]
                ]);

                // Appel API - SANS le suppresseur @ (for proper error handling)
                $response = file_get_contents($url, false, $context);

                // Gestion des erreurs de connexion
                if ($response === false) {
                    throw new \Exception("Impossible de contacter TheMealDB (timeout ou connexion refusée)");
                }

                // ═══════════════════════════════════════════════════════════════
                // 3. PARSING JSON ET VALIDATION
                // ═══════════════════════════════════════════════════════════════

                $data = json_decode($response, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("JSON invalide retourné: " . json_last_error_msg());
                }

                // Vérifier que la recette existe
                if (!$data || !isset($data['meals']) || empty($data['meals'])) {
                    throw new \Exception("Recette non trouvée (id_api: {$id_api})");
                }

                $meal = $data['meals'][0];

                // ═══════════════════════════════════════════════════════════════
                // 4. TRANSFORMATION DES INGRÉDIENTS
                // ═══════════════════════════════════════════════════════════════
                // Format API: strIngredient1, strMeasure1, strIngredient2, strMeasure2, ...
                // Format local: [{"name": "Tomate", "qty": "500g"}, ...]

                $ingredients = [];

                for ($i = 1; $i <= 20; $i++) {
                    // Récupérer l'ingrédient et la mesure
                    $ingredientKey = "strIngredient{$i}";
                    $measureKey = "strMeasure{$i}";

                    $ingredient = trim($meal[$ingredientKey] ?? '');
                    $measure = trim($meal[$measureKey] ?? '');

                    // Si l'ingrédient n'est pas vide, l'ajouter
                    if (!empty($ingredient)) {
                        $ingredients[] = [
                            'name' => htmlspecialchars($ingredient, ENT_QUOTES, 'UTF-8'),
                            'qty' => htmlspecialchars($measure, ENT_QUOTES, 'UTF-8')
                        ];
                    }
                }

                // ═══════════════════════════════════════════════════════════════
                // 5. CRÉER L'OBJET RECETTE UNIFIÉ
                // ═══════════════════════════════════════════════════════════════

                $recette = (object) [
                    // Données de base
                    'id_api' => htmlspecialchars($meal['idMeal'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'title' => htmlspecialchars($meal['strMeal'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'category' => htmlspecialchars($meal['strCategory'] ?? 'Non catégorisée', ENT_QUOTES, 'UTF-8'),
                    'area' => htmlspecialchars($meal['strArea'] ?? 'Origine inconnue', ENT_QUOTES, 'UTF-8'),

                    // Contenu
                    'ingredients' => json_encode($ingredients), // JSON pour compatibilité lire.php
                    'instructions' => htmlspecialchars($meal['strInstructions'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'image_url' => htmlspecialchars($meal['strMealThumb'] ?? '', ENT_QUOTES, 'UTF-8'),

                    // Données supplémentaires
                    'source_url' => htmlspecialchars($meal['strSource'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'tags' => htmlspecialchars($meal['strTags'] ?? '', ENT_QUOTES, 'UTF-8'),

                    // Métadonnées
                    'type' => 'api', // Marqueur pour distinguer recettes locales
                    'created_at' => date('Y-m-d H:i:s') // Date de consultation
                ];

                // ═══════════════════════════════════════════════════════════════
                // 6. MISE EN CACHE (30 MINUTES)
                // ═══════════════════════════════════════════════════════════════

                $_SESSION[$cacheKey] = [
                    'data' => $recette,
                    'timestamp' => time()
                ];

                // Log le succès
                ErrorHandler::log(
                    "Recette API chargée et mise en cache: {$recette->title} (ID: {$id_api})",
                    ErrorHandler::TYPE_INFO,
                    null,
                    ['action' => 'api/lireRecette', 'method' => 'cache_miss_then_hit']
                );

            } catch (\Exception $e) {
                // Erreur lors de l'appel ou du parsing API
                ErrorHandler::logApiError($e, 'TheMealDB', [
                    'action' => 'api/lireRecette',
                    'id_api' => $id_api,
                    'url' => $url ?? 'N/A'
                ]);

                $_SESSION['toasts'][] = [
                    'type' => 'error',
                    'message' => '❌ Service TheMealDB temporairement indisponible. Réessayez plus tard.'
                ];

                header('Location: /favorites');
                exit;
            }
        }

        // ═══════════════════════════════════════════════════════════════
        // 7. AFFICHAGE DE LA VUE
        // ═══════════════════════════════════════════════════════════════

        $this->render('api/lire', [
            'recette' => $recette,
            'titre' => $recette->title
        ]);
    }
}