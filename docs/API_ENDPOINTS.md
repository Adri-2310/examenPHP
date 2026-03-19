# 📡 Guide des Endpoints API - Marmiton-Exam

**Version:** 1.0.0
**Date:** 19 mars 2026
**Format:** Requêtes HTTP (GET, POST, PUT, DELETE)

---

## 🏗️ Architecture générale

**Base URL:** `http://localhost:8000/public/?url=`

**Format:** `/?url=controller/action[/param1/param2]`

**Authentification:** Session PHP (cookie PHPSESSID)

---

## 👤 UTILISATEURS (Users)

### 1. Inscription (Register)

```http
GET /?url=users/register
POST /?url=users/register
```

**Paramètres POST:**
```json
{
  "email": "user@example.com",      // Email unique, validé
  "nom": "JohnDoe",                  // Min 2 caractères, unique
  "password": "SecurePass123",       // Min 8 caractères
  "confirm_password": "SecurePass123",
  "csrf_token": "token_value"        // Jeton CSRF
}
```

**Réponse (succès):**
```
Redirection vers: /?url=users/login
Session: user = {id, email, nom}
```

**Réponse (erreur):**
```
Affichage du formulaire avec messages d'erreur
Message: "Email déjà utilisé" ou "Nom existe déjà"
```

**Validation côté serveur:**
- Email: Format valide + Unicité en base
- Nom: Min 2 caractères + Unicité en base
- Password: Min 8 caractères + Confirmation match

---

### 2. Connexion (Login)

```http
GET /?url=users/login
POST /?url=users/login
```

**Paramètres POST:**
```json
{
  "email": "user@example.com",
  "password": "SecurePass123",
  "csrf_token": "token_value"
}
```

**Réponse (succès):**
```
Session: user = {id, email, nom, created_at}
Redirection vers: /?url=main
Cookie: PHPSESSID (session)
```

**Réponse (erreur):**
```
Message d'erreur: "Email ou mot de passe incorrect"
Limite: Max 3 tentatives / 5 minutes (rate limiting)
Attendre: 5 minutes avant nouvelle tentative
```

**Sécurité:**
- Rate limiting: 3 tentatives max par 5 minutes
- Password hashing: bcrypt (password_verify)
- Session: HttpOnly flag, SameSite=Strict

---

### 3. Vérification unicité du nom

```http
POST /?url=users/checkNom
```

**Paramètres (FormData):**
```
nom: "JohnDoe"
csrf_token: "token_value"
```

**Réponse (JSON):**
```json
{
  "exists": false,
  "message": "Nom disponible"
}
```

ou

```json
{
  "exists": true,
  "message": "Ce nom est déjà utilisé"
}
```

---

### 4. Profil utilisateur

```http
GET /?url=users/profile
```

**Authentification requise:** OUI (session user)

**Réponse:**
- Affichage du profil avec email, nom, date création
- Liste des recettes personnelles (CRUD)
- Lien vers "Mes favoris"

---

### 5. Déconnexion

```http
GET /?url=users/logout
```

**Action:**
- Destruction de la session
- Suppression du cookie PHPSESSID
- Redirection vers accueil

---

## 🍽️ RECETTES LOCALES (Recipes)

### 1. Lister les recettes

```http
GET /?url=recipes
GET /?url=recipes/index
```

**Paramètres (optionnel):**
```
search=terme       // Filtrer par titre/description
```

**Réponse:**
- Liste toutes les recettes de l'utilisateur connecté
- Affichage: titre, image, description courte
- Boutons: Consulter, Modifier, Supprimer

---

### 2. Consulter une recette

```http
GET /?url=recipes/lire/5
```

**Paramètre:**
- `5` = ID de la recette

**Réponse:**
```json
{
  "id": 5,
  "title": "Pizza Margherita",
  "description": "...",
  "ingredients": [
    {"name": "Tomate", "qty": "500g"},
    {"name": "Mozzarella", "qty": "250g"}
  ],
  "instructions": "...",
  "image_url": "/public/uploads/recipe_5.jpg",
  "user_id": 1,
  "created_at": "2026-03-15 10:30:00",
  "updated_at": "2026-03-19 14:45:00"
}
```

**Contrôle d'accès:**
- Si propriétaire: Voir boutons Modifier/Supprimer
- Si autre utilisateur: Voir lecture seule
- Si pas connecté: Redirection login

---

### 3. Créer une recette

```http
GET /?url=recipes/ajouter
POST /?url=recipes/ajouter
```

**Paramètres POST (multipart/form-data):**
```json
{
  "title": "Pizza Margherita",
  "description": "Une pizza italienne...",
  "ingredients[name][]": ["Tomate", "Mozzarella"],
  "ingredients[qty][]": ["500g", "250g"],
  "instructions": "1. Étaler la pâte\n2. Ajouter sauce...",
  "image": <file>,                    // Optionnel, JPG/PNG
  "csrf_token": "token_value"
}
```

**Validation:**
- Title: Requis, non vide
- Description: Requis, non vide
- Ingredients: Au moins 1, name ET qty requis
- Instructions: Requis, non vide
- Image: Optionnel, max 5MB, format JPG/PNG/WEBP

**Réponse (succès):**
```
INSERT en base + File upload
Redirection: /?url=recipes/lire/{newId}
Message: "Recette créée avec succès"
```

**Réponse (erreur):**
```
Affichage du formulaire avec erreurs validées
Message: "Le titre est requis"
```

---

### 4. Modifier une recette

```http
GET /?url=recipes/edit/5
POST /?url=recipes/edit/5
```

**Même paramètres que création + ID**

**Contrôle d'accès:**
- Seulement propriétaire peut modifier
- Autre utilisateur: Erreur 403 Forbidden

**Validation:**
- Identique à création

---

### 5. Supprimer une recette

```http
POST /?url=recipes/delete/5
```

**Paramètres:**
```json
{
  "csrf_token": "token_value"
}
```

**Contrôle d'accès:**
- Seulement propriétaire peut supprimer
- Confirmation avant suppression (confirmation dialog)

**Action:**
- DELETE de la base
- Suppression du fichier image associé
- Redirection: /?url=recipes

---

## ❤️ FAVORIS (Favorites)

### 1. Lister les favoris

```http
GET /?url=favorites
GET /?url=favorites/index
```

**Authentification:** OUI

**Réponse:**
- Liste toutes les recettes API sauvegardées en favoris
- Affiche: titre, image, bouton "Voir la recette", "Retirer"
- Lien "Voir la recette" → `/api/lire/{idApi}`

---

### 2. Toggle favori (Ajouter/Retirer)

```http
POST /?url=favorites/toggle
```

**Paramètres (FormData):**
```
id_api: "12345",              // ID TheMealDB
titre: "Pizza Margherita",
image_url: "https://...",
csrf_token: "token_value"
```

**Logique:**
- Si favori existe: SUPPRIMER
- Si favori n'existe pas: CRÉER
- Retour: État actuel du favori

**Réponse (JSON):**
```json
{
  "success": true,
  "isFavorite": true,
  "message": "Ajouté aux favoris"
}
```

ou

```json
{
  "success": false,
  "message": "Erreur lors de la modification"
}
```

---

### 3. Voir détails recette API

```http
GET /?url=api/lire/12345
```

**Paramètre:**
- `12345` = ID TheMealDB

**Réponse:**
- Récupère depuis API TheMealDB en temps réel
- Cache en session (30 minutes) pour performance
- Affiche: Titre, image, ingrédients, instructions, catégorie, région

**Gestion d'erreurs:**
- API down: "Service indisponible"
- ID inexistant: "Recette non trouvée"
- Timeout: "Délai d'attente dépassé"

---

## 🔍 RECHERCHE (API TheMealDB)

### 1. Rechercher des recettes

```http
GET /?url=api/index
POST /?url=api/search
```

**Paramètres:**
```json
{
  "search": "pizza"
}
```

**Réponse (HTML):**
- Affiche liste des recettes trouvées
- Boutons: "Voir", "Ajouter aux favoris"
- Pagination: 12 résultats max

**Cache:**
- Durée: 30 minutes en session
- Clé: `cache_api_search_{search}`

---

### 2. Recettes aléatoires (Accueil)

```http
GET /?url=main
```

**Actions:**
- Récupère 6 recettes aléatoires de TheMealDB
- Affiche dans section "Mes Coups de Cœur"
- Cache: 30 minutes

---

## 📧 CONTACT

### 1. Formulaire de contact

```http
GET /?url=contact
POST /?url=contact
```

**Paramètres POST:**
```json
{
  "email": "contact@example.com",
  "nom": "Jean Dupont",
  "message": "Bonjour, j'aimerais...",
  "csrf_token": "token_value"
}
```

**Validation:**
- Email: Format valide
- Nom: Min 2 caractères
- Message: Min 10 caractères

**Action:**
- Envoyer email à l'admin
- Redirection avec message succès

---

## 🛡️ SÉCURITÉ

### CSRF Protection
Tous les formulaires POST/PUT/DELETE doivent inclure:
```html
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
```

### Authentification
- Session-based avec PHP
- Cookie: PHPSESSID (HttpOnly, Secure, SameSite=Strict)
- Timeout: 30 minutes d'inactivité

### Validation
- Côté client: JavaScript (temps réel)
- Côté serveur: PHP (sécurité)
- Double validation obligatoire

### Rate Limiting
- Login: Max 3 tentatives / 5 minutes
- Contact: À implémenter
- API: À implémenter

---

## 🔴 CODES D'ERREUR

| Code | Signification | Cause |
|------|---------------|-------|
| 200 | OK | Succès |
| 400 | Bad Request | Données invalides |
| 401 | Unauthorized | Non authentifié |
| 403 | Forbidden | Non autorisé (pas propriétaire) |
| 404 | Not Found | Ressource inexistante |
| 500 | Server Error | Erreur serveur (logs/errors.log) |

---

## 📝 EXEMPLE WORKFLOW COMPLET

### Nouvel utilisateur, créer et aimer une recette

```
1. GET /?url=users/register
   → Affiche formulaire inscription

2. POST /?url=users/register
   → Email: john@example.com
   → Nom: JohnDoe
   → Password: SecurePass123
   ✅ Redirection login

3. POST /?url=users/login
   → Email: john@example.com
   → Password: SecurePass123
   ✅ Redirection main (accueil)
   ✅ Session créée

4. GET /?url=recipes/ajouter
   → Affiche formulaire création recette

5. POST /?url=recipes/ajouter
   → Title: "Ma Pizza"
   → Ingredients: Tomate 500g, Mozzarella 250g
   → Instructions: "..."
   → Image: pizza.jpg
   ✅ Redirection /?url=recipes/lire/1

6. GET /?url=api/index
   → Affiche formulaire recherche

7. POST /?url=api/search
   → Search: "pizza"
   ✅ Affiche résultats API TheMealDB

8. POST /?url=favorites/toggle
   → id_api: 12345
   ✅ JSON: {success: true, isFavorite: true}

9. GET /?url=favorites
   → Affiche la recette aux favoris
```

---

## 📞 Support

Pour des détails supplémentaires:
- Voir: `src/controllers/` pour logique
- Voir: `views/` pour templates HTML
- Voir: `docs/GESTION_ERREURS_BASIQUE.md` pour erreurs
