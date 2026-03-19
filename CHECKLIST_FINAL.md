# ✅ CHECKLIST FINAL PRÉ-SOUMISSION

**Échéance:** 27 mars 2026 23:45
**Statut:** À compléter avant la date limite

---

## 🔴 CRITIQUE - À FAIRE OBLIGATOIREMENT

### Avant le 27 mars 23:45

- [ ] **Créer fichier ZIP**
  ```bash
  zip -r nom-prenom.zip examenPHP/ -x "examenPHP/.git/*" "examenPHP/node_modules/*"
  ```
  - [ ] Remplacer `nom` et `prenom` par vos vrais noms
  - [ ] Vérifier le fichier ZIP est créé et téléchargeable
  - [ ] Tester le dézip pour vérifier l'intégrité

- [ ] **Tester la base de données**
  ```bash
  mysql -u root -p < database/examenPhp.sql
  ```
  - [ ] L'import se fait sans erreur
  - [ ] Les 3 tables sont créées (users, recipes, favorites)
  - [ ] Les structures de table sont correctes

- [ ] **Tester les fonctionnalités critiques**
  - [ ] Inscription utilisateur (formulaire complet)
  - [ ] Connexion utilisateur (email + password)
  - [ ] Création recette (texte + ingrédients + image)
  - [ ] Modification recette
  - [ ] Suppression recette
  - [ ] Ajout aux favoris (API TheMealDB)
  - [ ] Suppression favori
  - [ ] Formulaire contact (envoi)
  - [ ] Changer thème (dark/light)
  - [ ] Pages d'erreur (404, 403, 500)

- [ ] **Vérifier les validations**
  - [ ] Validation email côté client ET serveur
  - [ ] Validation mot de passe (min 8 caractères)
  - [ ] Validation ingrédients requis
  - [ ] Validation messages d'erreur affichent correctement
  - [ ] CSRF token valide sur tous les formulaires

- [ ] **Vérifier la sécurité**
  - [ ] Aucun fichier .env exposé
  - [ ] Les mots de passe sont hashés en base (bcrypt)
  - [ ] Les uploads d'images ont une extension valide
  - [ ] Les injections SQL ne passent pas
  - [ ] XSS payload testé (ex: `<script>alert('xss')</script>`)

- [ ] **Vérifier les logs**
  ```bash
  ls -la logs/errors.log
  ```
  - [ ] Le fichier logs/ existe
  - [ ] Pas d'erreurs critiques non gérées
  - [ ] Les erreurs sont loggées correctement

---

## 🟡 IMPORTANT - Fichiers optionnels

### Fichier liens.txt (facultatif mais recommandé)

Si vous avez un hébergement ou GitHub:

- [ ] Créer fichier `liens.txt` à la racine du projet
  ```
  Lien vers le site en ligne: https://...
  Lien vers le dépôt GitHub: https://github.com/...
  ```

- [ ] Tester que les liens sont accessibles
- [ ] Ajouter le fichier dans le ZIP avant soumission

---

## 🟢 VÉRIFICATIONS DE QUALITÉ

### Documentation

- [ ] README.md est à jour (19 mars 2026)
- [ ] Les consignes de l'examen sont documentées
- [ ] Les instructions d'installation sont claires
- [ ] Les références de sécurité sont présentes

### Code

- [ ] Aucun `console.log()` de debug en production
- [ ] Aucun `var_dump()` ou `print_r()` visible
- [ ] Pas de fichiers temporaires (`.bak`, `.tmp`)
- [ ] Pas de dossiers `node_modules/` inutiles

### Commentaires

- [ ] Les commentaires sont en français
- [ ] Pas de commentaires obsolètes
- [ ] Les méthodes importantes sont documentées
- [ ] Les sections complexes sont expliquées

---

## 🔍 VÉRIFICATION FINALE

### 1. Structure du projet

```
examenPHP/
├── public/                 ✅ Présent
├── src/                    ✅ Présent
├── views/                  ✅ Présent
├── database/               ✅ Présent (examenPhp.sql)
├── docs/                   ✅ Présent
├── README.md               ✅ Présent et à jour
├── RAPPORT_CONFORMITE_EXAMEN.md    ✅ Présent
├── RAPPORT_AUDIT_COMPLET.md        ✅ Présent
└── CHECKLIST_FINAL.md              ✅ Ce fichier
```

- [ ] Tous les dossiers sont présents
- [ ] Aucun dossier ne manque
- [ ] La structure est logique et organisée

### 2. Fichiers importants

- [ ] `database/examenPhp.sql` - Base de données
- [ ] `public/index.php` - Front controller
- [ ] `src/core/Db.php` - Connexion BD
- [ ] `src/core/Main.php` - Routeur
- [ ] `Autoloader.php` - Chargement classes

### 3. Fonctionnalités clés

**CRUD Recettes locales:**
- [ ] Create (ajouter) ✅
- [ ] Read (consulter) ✅
- [ ] Update (modifier) ✅
- [ ] Delete (supprimer) ✅

**Authentification:**
- [ ] Register (inscription) ✅
- [ ] Login (connexion) ✅
- [ ] Logout (déconnexion) ✅
- [ ] Profil utilisateur ✅

**Bonus:**
- [ ] API TheMealDB intégrée ✅
- [ ] Système de favoris ✅
- [ ] Thème sombre/clair ✅
- [ ] Gestion d'erreurs 404/403/500 ✅

---

## 🧪 TEST D'ACCEPTATION

### Scénario 1: Nouvel utilisateur

- [ ] Accéder au site
- [ ] Voir la page d'accueil avec recettes aléatoires
- [ ] Cliquer "Inscription"
- [ ] Remplir le formulaire (email, nom, password)
- [ ] Valider inscription
- [ ] Se connecter avec les identifiants
- [ ] Voir le profil utilisateur
- [ ] Voir "Ajouter une recette"

### Scénario 2: Création recette

- [ ] Cliquer "Ajouter une recette"
- [ ] Remplir le formulaire:
  - [ ] Titre
  - [ ] Description
  - [ ] Ingrédients (au moins 2)
  - [ ] Instructions
  - [ ] Image (optionnel)
- [ ] Valider le formulaire
- [ ] Voir la recette dans la liste
- [ ] Consulter les détails
- [ ] Modifier la recette
- [ ] Retour à la liste

### Scénario 3: Favoris (API)

- [ ] Cliquer "Recherche"
- [ ] Chercher une recette (ex: "pizza")
- [ ] Voir résultats de TheMealDB
- [ ] Ajouter aux favoris
- [ ] Voir le bouton changé ("Retirer des favoris")
- [ ] Aller à "Mes favoris"
- [ ] Voir la recette ajoutée
- [ ] Consulter les détails de la recette
- [ ] Retirer des favoris (test suppression)

### Scénario 4: Erreurs

- [ ] Tenter accès page admin (404)
- [ ] Tenter modification recette d'un autre (403)
- [ ] Forcer erreur base de données (500)
- [ ] Vérifier pages d'erreur affichent correctement

---

## 📦 AVANT LE DÉPÔT FINAL

### Juste avant de soumettre

1. **Créer le ZIP** ← DERNIER MOMENT
   ```bash
   zip -r nom-prenom.zip examenPHP/ \
     -x "examenPHP/.git/*" \
        "examenPHP/.claude/*" \
        "examenPHP/logs/*" \
        "examenPHP/node_modules/*" \
        "examenPHP/public/uploads/*"
   ```

2. **Vérifier taille du ZIP**
   - Doit être < 50 MB (normal pour ce projet)
   - Pas plus de 100 MB

3. **Tester extraction du ZIP**
   ```bash
   mkdir test-extract
   unzip nom-prenom.zip -d test-extract/
   cd test-extract/examenPHP/
   # Vérifier structure
   ```

4. **Lire une dernière fois les instructions d'examen**
   - Date limite: 27 mars 2026 23:45
   - Format: nom-prenom.zip
   - Contenu: dossier complet + database.sql + liens.txt (opt)

5. **Soumettre**
   - Plateforme d'examen: [À compléter]
   - Fichier: nom-prenom.zip
   - ✅ Envoyer

---

## 📋 AIDE-MÉMOIRE

### Commandes utiles

**Tester l'application localement:**
```bash
cd examenPHP/
php -S localhost:8000
# Accès: http://localhost:8000/public
```

**Importer la base de données:**
```bash
mysql -u root -p < database/examenPhp.sql
```

**Créer le ZIP final:**
```bash
zip -r nom-prenom.zip examenPHP/ -x "examenPHP/.git/*"
```

---

## ✨ BONUS

Si vous avez terminé la checklist et avez du temps:

- [ ] Ajouter tests unitaires (PHP)
- [ ] Ajouter documentation OpenAPI (API endpoints)
- [ ] Créer diagramme UML (architecture)
- [ ] Améliorer commentaires JavaScript JSDoc
- [ ] Ajouter mode "readonly" pour démos
- [ ] Créer script de seed (données d'exemple)

---

## 🎯 RÉSUMÉ FINAL

**SI COCHÉ TOUS LES POINTS CRITIQUES (🔴):**
✅ Votre projet est prêt pour la soumission

**SI COCHÉ TOUS LES POINTS:**
⭐ Votre projet est EXCELLENT

**Dernière vérification avant de cliquer "SOUMETTRE":**
- [ ] Fichier ZIP créé: `nom-prenom.zip`
- [ ] Base de données: `database/examenPhp.sql` importée
- [ ] Liens.txt: Optionnel (mais recommandé si lien existant)
- [ ] README.md: À jour
- [ ] Tous les tests passent
- [ ] Aucune erreur dans les logs

---

**BON COURAGE! 🚀**

*L'équipe de correction appréciera votre organisation et votre documentation complète.*

**À bientôt pour les résultats!** 🎓
