<?php
/**
 * Nom du fichier : Model.php
 *
 * Description :
 * Classe de base pour tous les modèles de l'application.
 * Implémente le pattern Active Record simplifié avec des méthodes CRUD génériques.
 *
 * Fonctionnalités principales :
 * - Exécution de requêtes préparées (sécurité SQL injection)
 * - Opérations CRUD (find, findAll, create)
 * - Génération dynamique de requêtes INSERT
 *
 * Pattern utilisé : Active Record
 * Sécurité : Requêtes préparées obligatoires
 *
 * @package    App\Core
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Core;

class Model
{
    /**
     * Nom de la table dans la base de données
     * @var string
     */
    protected $table;

    /**
     * Instance de connexion PDO (Singleton Db)
     * @var \PDO
     */
    private $db;

    /**
     * Exécute une requête SQL préparée ou simple
     *
     * Cette méthode centralise l'exécution de toutes les requêtes SQL.
     * Elle utilise automatiquement des requêtes préparées si des paramètres sont fournis,
     * sinon elle exécute une requête simple.
     *
     * Exemple d'utilisation :
     * ```php
     * // Requête préparée (sécurisée)
     * $this->requete("SELECT * FROM users WHERE id = ?", [5]);
     *
     * // Requête simple
     * $this->requete("SELECT * FROM users");
     * ```
     *
     * @param string $sql         La requête SQL à exécuter
     * @param array|null $attributs Les valeurs à lier aux placeholders (optionnel)
     * @return \PDOStatement      Le statement PDO avec les résultats
     *
     * @security Protection contre les injections SQL via requêtes préparées
     */
    public function requete(string $sql, array $attributs = null)
    {
        // Récupération de l'instance unique de connexion (pattern Singleton)
        $this->db = Db::getInstance();

        if($attributs !== null){
            // Requête préparée : les valeurs sont automatiquement échappées
            $query = $this->db->prepare($sql);
            $query->execute($attributs);
            return $query;
        } else {
            // Requête simple (à utiliser uniquement pour les requêtes sans paramètres)
            return $this->db->query($sql);
        }
    }

    /**
     * Récupère tous les enregistrements de la table
     *
     * Cette méthode retourne toutes les lignes de la table associée au modèle.
     * Les résultats sont retournés sous forme de tableaux associatifs.
     *
     * Exemple d'utilisation :
     * ```php
     * $recettes = new RecipesModel();
     * $toutesLesRecettes = $recettes->findAll();
     * ```
     *
     * @return array Tableau d'objets contenant tous les enregistrements
     */
    public function findAll()
    {
        $query = $this->requete('SELECT * FROM ' . $this->table);
        return $query->fetchAll();
    }

    /**
     * Récupère un enregistrement par son ID
     *
     * Cette méthode retourne une seule ligne correspondant à l'ID fourni.
     * Utilise une requête préparée pour la sécurité.
     *
     * Exemple d'utilisation :
     * ```php
     * $recettes = new RecipesModel();
     * $recette = $recettes->find(5);
     * ```
     *
     * @param int $id L'identifiant de l'enregistrement à récupérer
     * @return object|false L'enregistrement trouvé ou false si inexistant
     *
     * @security Requête préparée contre injection SQL
     */
    public function find(int $id)
    {
        return $this->requete("SELECT * FROM {$this->table} WHERE id = ?", [$id])->fetch();
    }
    
    /**
     * Crée un nouvel enregistrement dans la base de données
     *
     * Cette méthode génère dynamiquement une requête INSERT en analysant
     * les propriétés de l'objet modèle passé en paramètre.
     *
     * Exemple d'utilisation :
     * ```php
     * $user = new UsersModel();
     * $user->email = "test@test.com";
     * $user->nom = "Dupont";
     * $this->create($user);
     * ```
     *
     * @param Model $model Instance du modèle contenant les données à insérer
     * @return \PDOStatement Résultat de l'exécution de la requête
     *
     * @security Les valeurs sont automatiquement échappées via requêtes préparées
     * @note Les propriétés 'db' et 'table' sont automatiquement exclues
     */
    public function create(Model $model)
    {
        // 1. Initialisation des tableaux pour construire la requête dynamique
        $champs = [];    // Noms des colonnes (ex: ['email', 'nom'])
        $inter = [];     // Placeholders (ex: ['?', '?'])
        $valeurs = [];   // Valeurs réelles (ex: ['test@test.com', 'Dupont'])

        // 2. Analyse des propriétés de l'objet pour extraire les données
        foreach($model as $champ => $valeur){
            // On exclut les propriétés internes de la classe (db, table)
            if($valeur !== null && $champ != 'db' && $champ != 'table'){
                $champs[] = $champ;
                $inter[] = "?";
                $valeurs[] = $valeur;
            }
        }

        // 3. Construction de la requête SQL dynamique
        $liste_champs = implode(', ', $champs);  // "email, nom"
        $liste_inter = implode(', ', $inter);     // "?, ?"

        // 4. Exécution de la requête préparée
        // Exemple final : INSERT INTO users (email, nom) VALUES (?, ?)
        return $this->requete('INSERT INTO '.$this->table.' ('. $liste_champs .')VALUES('.$liste_inter.')', $valeurs);
    }
}