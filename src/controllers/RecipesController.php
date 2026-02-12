use App\Models\FavoritesModel; // <-- N'oublie pas ça !

public function api()
    {
        // Sécurité : Si pas connecté, on vire vers le login !
        if (!isset($_SESSION['user'])) {
            header('Location: /users/login');
            exit;
        }

        $this->render('recipes/api', ['titre' => 'Recherche API']);
    }

public function index()
{
    // Vérification de sécurité
    if (!isset($_SESSION['user'])) {
        header('Location: /users/login');
        exit;
    }

    // 1. Récupérer mes créations (Table recipes)
    $recipesModel = new RecipesModel();
    $mesCreations = $recipesModel->findAllByUserId($_SESSION['user']['id']);

    // 2. Récupérer mes favoris (Table favorites)
    $favoritesModel = new FavoritesModel();
    $mesFavoris = $favoritesModel->findAllByUserId($_SESSION['user']['id']);

    // 3. Envoyer les deux listes à la vue
    $this->render('recipes/index', [
        'mesCreations' => $mesCreations,
        'mesFavoris' => $mesFavoris
    ]);
}