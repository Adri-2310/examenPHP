# RAPPORT - PROJET PROGRESSIF WEB
## Travail de Fin de Module

**Date**: 11 février 2026
**Étudiant**: [Votre nom]

---

## 📋 TABLE DES MATIÈRES

1. [Vue d'ensemble du projet](#vue-densemble-du-projet)
2. [Partie Backend PHP](#partie-backend-php)
3. [Partie Frontend JavaScript](#partie-frontend-javascript)
4. [Planning et Étapes](#planning-et-étapes)
5. [Checklist de progression](#checklist-de-progression)
6. [Ressources et liens](#ressources-et-liens)

---

## 🎯 VUE D'ENSEMBLE DU PROJET

Le projet progressif se compose de **deux grandes parties** :

### Backend (PHP)
- ✅ **6 parties obligatoires** à compléter
- 🎁 **4 parties facultatives** (bonus)

### Frontend (JavaScript)
- ✅ **3 exigences obligatoires** :
  1. Créer une classe JavaScript personnalisée
  2. Implémenter une bibliothèque JavaScript
  3. Intégrer une API externe

---

## 🔧 PARTIE BACKEND PHP

### 📊 Vue d'ensemble des parties obligatoires

| Partie | Sujet | Lien | Priorité |
|--------|-------|------|----------|
| **01** | Modèles de pages dynamiques | [Lien exercice](http://cours.cvmdev.be/php/modeles-de-pages-dynamiques#exercice-projet-progressif) | ⭐⭐⭐ |
| **02** | Gestion des formulaires | [Lien exercice](http://cours.cvmdev.be/php/gestion-des-formulaires#exercice-projet-progressif) | ⭐⭐⭐ |
| **04** | Gestion des bases de données | [Lien exercice](http://cours.cvmdev.be/php/gestion-des-bases-de-donnees#exercice-projet-progressif) | ⭐⭐⭐ |
| **05** | Cookies et variables de session | [Lien exercice](http://cours.cvmdev.be/php/cookies-et-variables-de-session#exercice-projet-progressif) | ⭐⭐⭐ |
| **06** | Sécuriser son application | [Lien exercice](http://cours.cvmdev.be/php/securiser-son-application#exercice-projet-progressif) | ⭐⭐⭐ |

### 📝 Parties facultatives (bonus)

| Partie | Sujet | Lien |
|--------|-------|------|
| **07** | Architecture MVC | [Lien exercice](http://cours.cvmdev.be/php/architecture-mvc#exercice-projet-progressif) |
| **08** | Les classes | [Lien exercice](http://cours.cvmdev.be/php/les-classes#exercice-projet-progressif) |
| **09** | Les namespaces | [Lien exercice](http://cours.cvmdev.be/php/les-namespaces#exercice-projet-progressif) |
| **10** | Les autoloaders | [Lien exercice](http://cours.cvmdev.be/php/les-autoloaders#exercice-projet-progressif) |

---

### 📖 DÉTAIL DES PARTIES BACKEND

#### ✅ PARTIE 01 : Modèles de pages dynamiques

**Objectifs** :
- Créer une structure de templates réutilisables
- Séparer la logique métier de la présentation
- Utiliser les systèmes d'inclusion PHP (`include`, `require`)
- Générer du contenu HTML dynamique

**Étapes à suivre** :

1. **Créer la structure de dossiers**
   ```
   /projet/
   ├── /public/
   │   ├── index.php
   │   ├── /css/
   │   └── /js/
   ├── /templates/
   │   ├── header.php
   │   ├── footer.php
   │   ├── nav.php
   │   └── /pages/
   └── /config/
       └── config.php
   ```

2. **Créer les templates de base**
   - Header (titre, meta, liens CSS)
   - Navigation (menu dynamique)
   - Footer (informations, copyright)

3. **Implémenter le système d'inclusion**
   - Utiliser `require_once` pour éviter les inclusions multiples
   - Passer des variables aux templates
   - Créer un fichier de configuration pour les chemins

**Conseils pratiques** :
- ✓ Définissez des constantes pour les chemins (`ROOT_PATH`, `TEMPLATE_PATH`)
- ✓ Créez des fonctions helper pour le rendu des templates
- ✓ Pensez à la réutilisabilité dès le début

---

#### ✅ PARTIE 02 : Gestion des formulaires

**Objectifs** :
- Créer des formulaires HTML (GET/POST)
- Récupérer et traiter les données (`$_GET`, `$_POST`)
- Valider les données côté serveur
- Afficher les erreurs de validation
- Repopuler les champs en cas d'erreur

**Étapes à suivre** :

1. **Créer les formulaires HTML**
   - Formulaire de contact
   - Formulaire d'inscription
   - Formulaire de connexion

2. **Implémenter la validation côté serveur**
   ```php
   $errors = [];

   // Validation email
   if (empty($_POST['email'])) {
       $errors['email'] = 'Email requis';
   } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
       $errors['email'] = 'Email invalide';
   }

   // Validation mot de passe
   if (empty($_POST['password'])) {
       $errors['password'] = 'Mot de passe requis';
   } elseif (strlen($_POST['password']) < 8) {
       $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères';
   }
   ```

3. **Gérer l'affichage des erreurs**
   - Afficher les messages d'erreur à côté des champs
   - Utiliser `htmlspecialchars()` pour afficher les valeurs

4. **Implémenter le pattern POST/Redirect/GET**
   - Éviter les double soumissions
   - Rediriger après un POST réussi

**Conseils pratiques** :
- ✓ **Toujours valider côté serveur**, même avec JavaScript
- ✓ Créez des fonctions de validation réutilisables
- ✓ Stockez les erreurs dans un tableau associatif
- ✓ Utilisez `trim()` pour nettoyer les espaces

---

#### ✅ PARTIE 04 : Gestion des bases de données

**Objectifs** :
- Se connecter à MySQL/MariaDB via PDO
- Utiliser des requêtes préparées (protection SQL injection)
- Implémenter les opérations CRUD (Create, Read, Update, Delete)
- Gérer les erreurs de base de données
- Organiser le code en classes/modèles

**Étapes à suivre** :

1. **Créer la base de données**
   - Définir le schéma des tables
   - Établir les relations entre tables
   - Créer les index nécessaires

2. **Configurer la connexion PDO**
   ```php
   try {
       $pdo = new PDO(
           "mysql:host=localhost;dbname=projet_web;charset=utf8mb4",
           $username,
           $password,
           [
               PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
               PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
               PDO::ATTR_EMULATE_PREPARES => false
           ]
       );
   } catch (PDOException $e) {
       error_log($e->getMessage());
       die("Erreur de connexion à la base de données");
   }
   ```

3. **Implémenter les opérations CRUD**
   - **Create** : Insertion de nouveaux enregistrements
   - **Read** : Sélection et affichage des données
   - **Update** : Modification des enregistrements
   - **Delete** : Suppression des enregistrements

4. **Créer des classes Model**
   - UserModel.php
   - ArticleModel.php
   - CommentModel.php

**Conseils pratiques** :
- ✓ **Utilisez UNIQUEMENT des requêtes préparées**
- ✓ Ne stockez jamais les identifiants en dur
- ✓ Créez un fichier `.env` pour les configurations sensibles
- ✓ Activez le mode d'erreur exception de PDO

---

#### ✅ PARTIE 05 : Cookies et variables de session

**Objectifs** :
- Démarrer et configurer les sessions PHP
- Stocker des données utilisateur temporaires
- Créer et lire des cookies
- Gérer l'authentification (login/logout)
- Implémenter des flash messages

**Étapes à suivre** :

1. **Configurer les sessions de manière sécurisée**
   ```php
   session_start([
       'cookie_lifetime' => 0,
       'cookie_httponly' => true,
       'cookie_secure' => true,      // Si HTTPS
       'cookie_samesite' => 'Lax',
       'use_strict_mode' => true
   ]);
   ```

2. **Implémenter le système de connexion**
   - Page de login
   - Vérification des identifiants
   - Stockage des infos utilisateur en session
   - Régénération de l'ID de session après login

3. **Créer le système de déconnexion**
   ```php
   session_start();
   $_SESSION = [];
   session_destroy();
   setcookie(session_name(), '', time()-3600, '/');
   header('Location: login.php');
   exit;
   ```

4. **Implémenter les flash messages**
   - Messages de succès
   - Messages d'erreur
   - Messages d'information

**Conseils pratiques** :
- ✓ **Toujours appeler `session_start()` au début**
- ✓ Régénérez l'ID de session après login : `session_regenerate_id(true)`
- ✓ Utilisez les sessions pour données sensibles, cookies pour préférences
- ✓ Implémentez un système "Se souvenir de moi" sécurisé

---

#### ✅ PARTIE 06 : Sécuriser son application

**Objectifs** :
- Protéger contre les attaques XSS (Cross-Site Scripting)
- Protéger contre les attaques CSRF (Cross-Site Request Forgery)
- Protéger contre les injections SQL
- Valider et nettoyer toutes les entrées utilisateur
- Hasher les mots de passe de manière sécurisée

**Étapes à suivre** :

1. **Protection XSS**
   ```php
   // TOUJOURS échapper les sorties HTML
   echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
   ```

2. **Protection CSRF**
   ```php
   // Génération du token
   if (empty($_SESSION['csrf_token'])) {
       $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
   }

   // Dans le formulaire
   <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

   // Vérification
   if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
       die('Token CSRF invalide');
   }
   ```

3. **Hashage des mots de passe**
   ```php
   // Lors de l'inscription
   $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

   // Lors de la connexion
   if (password_verify($password, $hashedPassword)) {
       // Mot de passe correct
   }
   ```

4. **Configuration des headers de sécurité**
   ```php
   header("X-Frame-Options: DENY");
   header("X-Content-Type-Options: nosniff");
   header("X-XSS-Protection: 1; mode=block");
   header("Content-Security-Policy: default-src 'self'");
   ```

5. **Validation des entrées**
   - Valider le type de données
   - Valider la longueur
   - Valider le format
   - Nettoyer les données

**Conseils pratiques** :
- ✓ **Règle d'or : Ne JAMAIS faire confiance aux données utilisateur**
- ✓ Échappez TOUTES les sorties HTML
- ✓ Ajoutez des tokens CSRF à TOUS les formulaires
- ✓ Limitez les tentatives de connexion (rate limiting)
- ✓ Loggez les tentatives suspectes

---

## 💻 PARTIE FRONTEND JAVASCRIPT

### 📊 Vue d'ensemble des exigences

| Exigence | Description | Recommandation |
|----------|-------------|----------------|
| **1** | Créer une classe JavaScript personnalisée | Menu Hamburger |
| **2** | Implémenter une bibliothèque JavaScript | Toastify-js ou Splide |
| **3** | Intégrer une API externe | JSONPlaceholder |

---

### 📖 DÉTAIL DES EXIGENCES FRONTEND

#### ✅ EXIGENCE 1 : Classe JavaScript personnalisée

**Options disponibles** :
- ✅ Menu hamburger (RECOMMANDÉ - Facile)
- Carousel d'images ou de cartes (Difficile)
- Modale (fenêtre popup) (Moyen)
- Onglets (navigation par panneaux) (Facile)
- Système de notifications toast (Moyen)
- Sélecteur de thème clair/sombre (Facile)
- Barre de recherche avec filtre en direct (Moyen)
- Compteur de caractères pour champ texte (Facile)
- Bouton retour en haut de page (Facile)

**Étapes à suivre (exemple : Menu Hamburger)** :

1. **Créer la classe JavaScript**
   ```javascript
   class HamburgerMenu {
       constructor(buttonSelector, menuSelector) {
           this.button = document.querySelector(buttonSelector);
           this.menu = document.querySelector(menuSelector);
           this.isOpen = false;
           this.init();
       }

       init() {
           this.button.addEventListener('click', () => this.toggle());
           this.addCloseOnOutsideClick();
       }

       toggle() {
           this.isOpen = !this.isOpen;
           this.menu.classList.toggle('active');
           this.button.classList.toggle('active');
           this.button.setAttribute('aria-expanded', this.isOpen);
       }

       addCloseOnOutsideClick() {
           document.addEventListener('click', (e) => {
               if (!this.menu.contains(e.target) &&
                   !this.button.contains(e.target) &&
                   this.isOpen) {
                   this.toggle();
               }
           });
       }
   }

   // Utilisation
   const menu = new HamburgerMenu('#hamburger-btn', '#mobile-menu');
   ```

2. **Créer le HTML**
   ```html
   <button id="hamburger-btn" aria-label="Menu" aria-expanded="false">
       <span></span>
       <span></span>
       <span></span>
   </button>

   <nav id="mobile-menu">
       <ul>
           <li><a href="#">Accueil</a></li>
           <li><a href="#">À propos</a></li>
           <li><a href="#">Contact</a></li>
       </ul>
   </nav>
   ```

3. **Ajouter le CSS**
   ```css
   #mobile-menu {
       position: fixed;
       top: 0;
       left: -100%;
       width: 80%;
       height: 100vh;
       background: #fff;
       transition: left 0.3s ease;
       z-index: 999;
   }

   #mobile-menu.active {
       left: 0;
   }

   #hamburger-btn span {
       display: block;
       width: 30px;
       height: 3px;
       background: #333;
       margin: 5px 0;
       transition: 0.3s;
   }
   ```

**Conseils pratiques** :
- ✓ Utilisez ES6+ (class, const/let, arrow functions)
- ✓ Ajoutez une gestion d'erreurs robuste
- ✓ Commentez votre code avec JSDoc
- ✓ Testez sur mobile et desktop
- ✓ Ajoutez les attributs ARIA pour l'accessibilité

---

#### ✅ EXIGENCE 2 : Bibliothèque JavaScript

**Options recommandées** :

| Bibliothèque | Utilité | Difficulté | Taille |
|--------------|---------|------------|--------|
| **Splide** | Slider/Carousel | Facile | 28KB |
| **Toastify-js** | Notifications toast | Très facile | 3KB |
| **MicroModal** | Modales | Facile | 4KB |
| **AOS** | Animations au scroll | Très facile | 13KB |
| **Tippy.js** | Tooltips | Facile | 20KB |
| **Choices.js** | Select amélioré | Moyen | 40KB |
| **VanillaTilt** | Effet tilt 3D | Facile | 5KB |
| **Anime.js** | Moteur d'animation | Difficile | 17KB |

**Étapes à suivre (exemple : Toastify-js)** :

1. **Installer via CDN**
   ```html
   <link rel="stylesheet" type="text/css"
         href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
   <script type="text/javascript"
           src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
   ```

2. **Utiliser dans votre code**
   ```javascript
   // Notification de succès
   function showSuccessToast(message) {
       Toastify({
           text: message,
           duration: 3000,
           gravity: "top",
           position: "right",
           backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
           stopOnFocus: true
       }).showToast();
   }

   // Notification d'erreur
   function showErrorToast(message) {
       Toastify({
           text: message,
           duration: 3000,
           gravity: "top",
           position: "right",
           backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
           stopOnFocus: true
       }).showToast();
   }

   // Utilisation avec un formulaire
   form.addEventListener('submit', async (e) => {
       e.preventDefault();
       try {
           const response = await submitForm();
           showSuccessToast('Formulaire envoyé avec succès !');
       } catch (error) {
           showErrorToast('Erreur lors de l\'envoi du formulaire');
       }
   });
   ```

**Conseils pratiques** :
- ✓ Commencez avec le CDN (plus simple)
- ✓ Lisez la documentation officielle
- ✓ Personnalisez les styles pour votre projet
- ✓ Vérifiez la compatibilité navigateur

---

#### ✅ EXIGENCE 3 : Intégration API

**Option A** : Réaliser les exercices exo-requetes-reseau-01 à 08
📚 Lien : https://cours.cvmdev.be/javascript/requetes-http-en-js

**Option B** : Intégrer une API de votre choix

**APIs recommandées** :

| API | Description | Difficulté | Auth requise |
|-----|-------------|------------|--------------|
| **JSONPlaceholder** | Fausses données (posts, users) | Très facile | Non |
| **Kaamelott's API** | Citations de Kaamelott | Facile | Non |
| **PokéAPI** | Données Pokémon | Facile | Non |
| **Open-Meteo** | Météo par coordonnées | Facile | Non |
| **REST Countries** | Informations pays | Facile | Non |
| **Random User** | Profils utilisateurs fictifs | Facile | Non |
| **Chuck Norris API** | Blagues Chuck Norris | Très facile | Non |
| **Quotable** | Citations courtes | Facile | Non |
| **DummyJSON** | E-commerce fictif | Facile | Non |

**Étapes à suivre (exemple : JSONPlaceholder)** :

1. **Créer une classe API**
   ```javascript
   class BlogAPI {
       constructor() {
           this.baseURL = 'https://jsonplaceholder.typicode.com';
       }

       async getPosts() {
           try {
               const response = await fetch(`${this.baseURL}/posts`);
               if (!response.ok) {
                   throw new Error(`HTTP error! status: ${response.status}`);
               }
               return await response.json();
           } catch (error) {
               console.error('Erreur lors de la récupération des posts:', error);
               throw error;
           }
       }

       async getPostById(id) {
           const response = await fetch(`${this.baseURL}/posts/${id}`);
           if (!response.ok) {
               throw new Error('Post non trouvé');
           }
           return await response.json();
       }

       async createPost(title, body) {
           const response = await fetch(`${this.baseURL}/posts`, {
               method: 'POST',
               headers: {
                   'Content-Type': 'application/json'
               },
               body: JSON.stringify({
                   title,
                   body,
                   userId: 1
               })
           });
           return await response.json();
       }
   }
   ```

2. **Afficher les données dans le HTML**
   ```javascript
   const api = new BlogAPI();

   async function displayPosts() {
       const container = document.getElementById('posts-container');
       const loader = document.getElementById('loader');

       try {
           // Afficher le loader
           loader.style.display = 'block';
           container.innerHTML = '';

           // Récupérer les posts
           const posts = await api.getPosts();

           // Afficher les 10 premiers posts
           posts.slice(0, 10).forEach(post => {
               const article = document.createElement('article');
               article.className = 'post-card';
               article.innerHTML = `
                   <h2>${post.title}</h2>
                   <p>${post.body}</p>
                   <button onclick="viewPost(${post.id})">Lire plus</button>
               `;
               container.appendChild(article);
           });

       } catch (error) {
           container.innerHTML = `
               <div class="error">
                   <p>❌ Erreur lors du chargement des posts</p>
                   <button onclick="displayPosts()">Réessayer</button>
               </div>
           `;
       } finally {
           loader.style.display = 'none';
       }
   }

   // Charger les posts au chargement de la page
   document.addEventListener('DOMContentLoaded', displayPosts);
   ```

3. **Ajouter le HTML**
   ```html
   <div id="loader" style="display: none;">
       <div class="spinner"></div>
       <p>Chargement...</p>
   </div>

   <div id="posts-container"></div>
   ```

**Conseils pratiques** :
- ✓ Utilisez `async/await` (plus moderne que `.then()`)
- ✓ Gérez les erreurs avec `try/catch`
- ✓ Affichez un loader pendant le chargement
- ✓ Gérez les cas d'erreur réseau
- ✓ Testez avec et sans connexion internet

---

## 📅 PLANNING ET ÉTAPES

### Phase 1 : Fondations Backend (Semaines 1-2)
**📌 PARTIE 01 : Modèles de pages dynamiques**
- [ ] Créer la structure de dossiers
- [ ] Créer header.php, footer.php, nav.php
- [ ] Implémenter le système d'inclusion
- [ ] Créer le fichier de configuration
- [ ] Tester sur 3 pages différentes

### Phase 2 : Interaction utilisateur (Semaines 3-4)
**📌 PARTIE 02 : Gestion des formulaires**
- [ ] Créer formulaire de contact
- [ ] Créer formulaire d'inscription
- [ ] Implémenter la validation côté serveur
- [ ] Afficher les erreurs
- [ ] Implémenter POST/Redirect/GET
- [ ] Repopuler les champs en cas d'erreur

### Phase 3 : Persistance des données (Semaines 5-6)
**📌 PARTIE 04 : Gestion des bases de données**
- [ ] Concevoir le schéma de base de données
- [ ] Créer les tables SQL
- [ ] Configurer PDO de manière sécurisée
- [ ] Créer les classes Model
- [ ] Implémenter CRUD pour chaque entité
- [ ] Connecter les formulaires à la BDD

### Phase 4 : État et authentification (Semaines 7-8)
**📌 PARTIE 05 : Cookies et sessions**
- [ ] Configurer les sessions de manière sécurisée
- [ ] Créer la page de connexion
- [ ] Implémenter le système de login
- [ ] Implémenter le système de logout
- [ ] Créer des zones protégées
- [ ] Ajouter les flash messages
- [ ] (Optionnel) Ajouter "Se souvenir de moi"

### Phase 5 : Sécurisation (Semaines 9-10)
**📌 PARTIE 06 : Sécuriser l'application**
- [ ] Auditer tout le code existant
- [ ] Échapper toutes les sorties HTML
- [ ] Ajouter tokens CSRF sur tous les formulaires
- [ ] Vérifier le hashage des mots de passe
- [ ] Configurer les headers de sécurité
- [ ] Valider/nettoyer toutes les entrées
- [ ] Implémenter rate limiting sur login
- [ ] Tester les vulnérabilités

### Phase 6 : Frontend JavaScript (Semaines 11-12)
**📌 EXIGENCE 1 : Classe JavaScript personnalisée**
- [ ] Choisir la fonctionnalité (ex: Menu Hamburger)
- [ ] Créer la classe JavaScript
- [ ] Créer le HTML nécessaire
- [ ] Ajouter les styles CSS
- [ ] Tester sur desktop et mobile
- [ ] Ajouter l'accessibilité (ARIA)

**📌 EXIGENCE 2 : Bibliothèque JavaScript**
- [ ] Choisir la bibliothèque (ex: Toastify-js)
- [ ] Installer via CDN
- [ ] Lire la documentation
- [ ] Implémenter dans le projet
- [ ] Personnaliser les styles
- [ ] Tester dans différents contextes

**📌 EXIGENCE 3 : Intégration API**
- [ ] Choisir l'API (ex: JSONPlaceholder)
- [ ] Créer la classe API
- [ ] Implémenter les méthodes fetch
- [ ] Afficher les données dans le HTML
- [ ] Ajouter gestion d'erreurs
- [ ] Ajouter loader/feedback utilisateur
- [ ] Tester avec/sans connexion

### Phase 7 : (Optionnel) Parties avancées (Semaines 13-14)
**📌 PARTIE 07-10 : Parties facultatives**
- [ ] Architecture MVC
- [ ] Les classes
- [ ] Les namespaces
- [ ] Les autoloaders

### Phase 8 : Finalisation (Semaine 15)
- [ ] Revoir tout le code
- [ ] Tester toutes les fonctionnalités
- [ ] Vérifier la sécurité
- [ ] Optimiser les performances
- [ ] Créer la documentation
- [ ] Préparer la présentation

---

## ✅ CHECKLIST DE PROGRESSION

### Backend PHP

#### ✅ Partie 01 : Modèles de pages
- [ ] Structure de dossiers créée
- [ ] Header/footer réutilisables
- [ ] Système d'inclusion fonctionnel
- [ ] Navigation dynamique
- [ ] Configuration centralisée

#### ✅ Partie 02 : Formulaires
- [ ] Formulaire de contact créé
- [ ] Formulaire d'inscription créé
- [ ] Validation complète côté serveur
- [ ] Affichage des erreurs
- [ ] Repopulation des champs
- [ ] Pattern POST/Redirect/GET

#### ✅ Partie 04 : Base de données
- [ ] Schéma BDD conçu
- [ ] Tables créées
- [ ] Connexion PDO sécurisée
- [ ] Requêtes préparées utilisées
- [ ] CRUD complet implémenté
- [ ] Classes Model créées
- [ ] Gestion des erreurs

#### ✅ Partie 05 : Sessions/Cookies
- [ ] Sessions configurées de manière sécurisée
- [ ] Système de login fonctionnel
- [ ] Système de logout fonctionnel
- [ ] Zones protégées créées
- [ ] Flash messages implémentés
- [ ] Régénération ID session après login

#### ✅ Partie 06 : Sécurité
- [ ] Protection XSS (htmlspecialchars partout)
- [ ] Tokens CSRF sur tous les formulaires
- [ ] Mots de passe hashés avec password_hash()
- [ ] Requêtes préparées PDO utilisées
- [ ] Headers de sécurité configurés
- [ ] Rate limiting sur le login
- [ ] Validation stricte des entrées
- [ ] Pas d'informations sensibles exposées

### Frontend JavaScript

#### ✅ Classe JavaScript personnalisée
- [ ] Fonctionnalité choisie
- [ ] Classe créée avec ES6+
- [ ] HTML créé
- [ ] CSS ajouté
- [ ] Fonctionnel sur desktop
- [ ] Fonctionnel sur mobile
- [ ] Accessibilité ajoutée (ARIA)
- [ ] Code commenté

#### ✅ Bibliothèque JavaScript
- [ ] Bibliothèque choisie
- [ ] Documentation lue
- [ ] CDN ou npm installé
- [ ] Intégrée dans le projet
- [ ] Styles personnalisés
- [ ] Testée dans différents contextes
- [ ] Compatible tous navigateurs

#### ✅ Intégration API
- [ ] API choisie
- [ ] Classe API créée
- [ ] Méthodes fetch implémentées
- [ ] Async/await utilisé
- [ ] Données affichées dans HTML
- [ ] Gestion d'erreurs ajoutée
- [ ] Loader/feedback utilisateur
- [ ] Try/catch utilisé
- [ ] Testée avec/sans connexion

---

## 📚 RESSOURCES ET LIENS

### Liens des exercices Backend
- [Partie 01 - Modèles de pages dynamiques](http://cours.cvmdev.be/php/modeles-de-pages-dynamiques#exercice-projet-progressif)
- [Partie 02 - Gestion des formulaires](http://cours.cvmdev.be/php/gestion-des-formulaires#exercice-projet-progressif)
- [Partie 04 - Gestion des bases de données](http://cours.cvmdev.be/php/gestion-des-bases-de-donnees#exercice-projet-progressif)
- [Partie 05 - Cookies et variables de session](http://cours.cvmdev.be/php/cookies-et-variables-de-session#exercice-projet-progressif)
- [Partie 06 - Sécuriser son application](http://cours.cvmdev.be/php/securiser-son-application#exercice-projet-progressif)
- [Partie 07 - Architecture MVC (facultatif)](http://cours.cvmdev.be/php/architecture-mvc#exercice-projet-progressif)
- [Partie 08 - Les classes (facultatif)](http://cours.cvmdev.be/php/les-classes#exercice-projet-progressif)
- [Partie 09 - Les namespaces (facultatif)](http://cours.cvmdev.be/php/les-namespaces#exercice-projet-progressif)
- [Partie 10 - Les autoloaders (facultatif)](http://cours.cvmdev.be/php/les-autoloaders#exercice-projet-progressif)

### Liens Frontend
- [Exercices requêtes réseau](https://cours.cvmdev.be/javascript/requetes-http-en-js)

### Documentation des bibliothèques JavaScript
- [Splide (carousel)](https://splidejs.com/)
- [Toastify-js (notifications)](https://github.com/apvarun/toastify-js)
- [MicroModal (modales)](https://micromodal.vercel.app/)
- [AOS (animations scroll)](https://michalsnik.github.io/aos/)
- [Tippy.js (tooltips)](https://atomiks.github.io/tippyjs/)
- [Choices.js (select)](https://choices-js.github.io/Choices/)
- [VanillaTilt (effet 3D)](https://micku7zu.github.io/vanilla-tilt.js/)
- [Anime.js (animations)](https://animejs.com/)

### APIs recommandées
- [JSONPlaceholder](https://jsonplaceholder.typicode.com/) - Fausses données
- [Kaamelott's API](https://kaamelott.chaudie.re/) - Citations Kaamelott
- [PokéAPI](https://pokeapi.co/) - Données Pokémon
- [Open-Meteo](https://open-meteo.com/) - Météo
- [REST Countries](https://restcountries.com/) - Infos pays
- [Random User](https://randomuser.me/) - Profils utilisateurs
- [Chuck Norris API](https://api.chucknorris.io/) - Blagues
- [Quotable](https://github.com/lukePeavey/quotable) - Citations
- [DummyJSON](https://dummyjson.com/) - E-commerce fictif

### Documentation PHP
- [PHP.net - Documentation officielle](https://www.php.net/manual/fr/)
- [PDO](https://www.php.net/manual/fr/book.pdo.php)
- [Sessions](https://www.php.net/manual/fr/book.session.php)
- [Sécurité](https://www.php.net/manual/fr/security.php)
- [Password hashing](https://www.php.net/manual/fr/book.password.php)

---

## 🎯 ARCHITECTURE RECOMMANDÉE DU PROJET

```
/projet-web/
│
├── /public/                          # Racine web accessible
│   ├── index.php                     # Point d'entrée
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   ├── contact.php
│   │
│   ├── /css/
│   │   ├── style.css
│   │   └── responsive.css
│   │
│   ├── /js/
│   │   ├── /classes/
│   │   │   └── HamburgerMenu.js     # Votre classe personnalisée
│   │   ├── /api/
│   │   │   └── BlogAPI.js           # Gestion API
│   │   └── main.js                  # Point d'entrée JS
│   │
│   └── /uploads/                    # Fichiers uploadés
│
├── /config/
│   ├── database.php                 # Configuration BDD
│   └── config.php                   # Configuration globale
│
├── /src/
│   ├── /Models/                     # Classes de modèles
│   │   ├── User.php
│   │   ├── Article.php
│   │   └── Comment.php
│   │
│   ├── /Controllers/                # Logique métier
│   │   ├── AuthController.php
│   │   └── ArticleController.php
│   │
│   └── /Helpers/                    # Fonctions utilitaires
│       ├── security.php
│       └── validation.php
│
├── /templates/
│   ├── header.php                   # En-tête réutilisable
│   ├── footer.php                   # Pied de page
│   ├── nav.php                      # Navigation
│   │
│   └── /pages/                      # Pages individuelles
│       ├── home.php
│       ├── about.php
│       └── contact-form.php
│
├── /storage/
│   └── /logs/                       # Fichiers de log
│
├── .env                             # Variables d'environnement (NE PAS COMMIT)
├── .gitignore
└── README.md
```

---

## 💡 CONSEILS GÉNÉRAUX

### Organisation du travail
1. **Travaillez par étapes** : Complétez une partie avant de passer à la suivante
2. **Testez régulièrement** : Testez après chaque fonctionnalité ajoutée
3. **Committez souvent** : Utilisez Git pour versionner votre code
4. **Documentez** : Commentez votre code et créez un README

### Bonnes pratiques
- ✅ Suivez les conventions de nommage (camelCase JS, snake_case PHP)
- ✅ Indentez correctement votre code
- ✅ Évitez la duplication de code
- ✅ Créez des fonctions réutilisables
- ✅ Séparez la logique de la présentation

### Sécurité (CRITIQUE)
- 🔒 **Ne faites JAMAIS confiance aux données utilisateur**
- 🔒 Validez et nettoyez TOUTES les entrées
- 🔒 Échappez TOUTES les sorties HTML
- 🔒 Utilisez UNIQUEMENT des requêtes préparées
- 🔒 Hashage les mots de passe avec `password_hash()`
- 🔒 Ajoutez des tokens CSRF sur tous les formulaires
- 🔒 Configurez les sessions de manière sécurisée

### Performance
- ⚡ Optimisez les requêtes SQL
- ⚡ Utilisez des index sur les colonnes fréquemment recherchées
- ⚡ Minimisez le nombre de requêtes
- ⚡ Compressez les images
- ⚡ Minifiez CSS/JS en production

### Debugging
- 🐛 Utilisez `var_dump()` et `die()` pour déboguer PHP
- 🐛 Utilisez `console.log()` pour déboguer JavaScript
- 🐛 Activez l'affichage des erreurs en développement
- 🐛 Consultez les logs d'erreurs
- 🐛 Utilisez les DevTools du navigateur

---

## 📝 NOTES IMPORTANTES

### Parties obligatoires vs facultatives
- **6 parties Backend OBLIGATOIRES** (01, 02, 04, 05, 06)
- **4 parties Backend FACULTATIVES** (07, 08, 09, 10) - Bonus
- **3 exigences Frontend OBLIGATOIRES** (Classe JS, Bibliothèque, API)

### Critères d'évaluation probables
1. **Fonctionnalité** : Le projet fonctionne-t-il correctement ?
2. **Sécurité** : Les protections sont-elles en place ?
3. **Code quality** : Le code est-il propre et bien organisé ?
4. **Architecture** : La structure est-elle logique ?
5. **Respect des consignes** : Toutes les parties sont-elles complétées ?

### Questions à vous poser régulièrement
- ✓ Mon code est-il sécurisé ?
- ✓ Mon code est-il lisible ?
- ✓ Mon code est-il testé ?
- ✓ Ai-je géré les cas d'erreur ?
- ✓ Ai-je documenté ce que j'ai fait ?

---

## 🎓 CONCLUSION

Ce projet progressif vous permet de :
- ✅ Maîtriser les fondamentaux du développement web full-stack
- ✅ Apprendre à sécuriser une application web
- ✅ Comprendre l'architecture d'une application moderne
- ✅ Pratiquer JavaScript moderne (ES6+)
- ✅ Intégrer des API externes

**Temps estimé total** : 12-15 semaines (3-4 mois)

**Bon courage pour votre projet ! 🚀**

---

*Document généré le 11 février 2026*
*Projet progressif - Module Web*
