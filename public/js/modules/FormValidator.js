/**
 * FormValidator.js - Validation des formulaires en temps réel
 *
 * OBJECTIF:
 * Valider les inputs utilisateur pendant la saisie et empêcher l'envoi
 * de formulaires invalides. Fourni un feedback visuel instantané.
 *
 * FONCTIONNALITÉS PRINCIPALES:
 * - Validation temps réel lors de la saisie (événement 'input')
 * - Validation au départ du focus (événement 'blur')
 * - Vérification asynchrone du nom d'utilisateur (unicité)
 * - Feedback visuel avec classes Bootstrap is-valid/is-invalid
 * - Messages d'erreur contextualisés sous chaque champ
 * - Prévention de submit pour les formulaires invalides
 * - Notifications toast si validation échoue
 *
 * TYPES DE VALIDATION SUPPORTÉS:
 * - Email: Format standard RFC avec regex
 * - Password: Minimum 8 caractères (OWASP)
 * - Username/Nom: Minimum 2 caractères + vérification unicité serveur
 * - Message/Textarea: Minimum 10 caractères
 * - Champs requis: Vérifie qu'ils ne sont pas vides
 *
 * ÉVÉNEMENTS GÉRÉS:
 * - 'input' : Validation en temps réel (every keystroke)
 * - 'blur'  : Validation au départ du focus
 * - 'submit': Validation complète avant envoi
 *
 * @class FormValidator
 * @param {NodeList} forms - Liste des formulaires <form> à valider
 *
 * @example
 * // Initialisation dans main.js
 * const forms = document.querySelectorAll('form');
 * new FormValidator(forms);
 *
 * @example
 * // Résultat visuel
 * // ✅ Champ valide: border verte + icône checkmark Bootstrap
 * // ❌ Champ invalide: border rouge + message d'erreur visible
 *
 * @author Marmiton-Exam v1.0
 * @see ../notification.js pour Notifications.error()
 */
class FormValidator {
    /**
     * Constructeur - Initialise la validation pour tous les formulaires
     *
     * ACTIONS:
     * 1. Récupère tous les formulaires à valider
     * 2. Pour chaque formulaire, trouve tous les inputs/textareas requis
     * 3. Ajoute listeners pour validation temps réel
     * 4. Ajoute listener pour validation au submit
     *
     * @constructor
     * @param {NodeList} forms - Liste des formulaires HTML (<form>)
     * @returns {void}
     *
     * @throws {Silencieux} Si forms.length === 0, ne fait rien
     */
    constructor(forms) {
        // Stockage des formulaires pour utilisation dans d'autres méthodes
        this.forms = forms;

        // Vérifier qu'il y a au moins un formulaire
        if (this.forms.length === 0) {
            return; // Rien à faire si aucun formulaire
        }

        // === CONFIGURATION DES LISTENERS PAR FORMULAIRE ===
        this.forms.forEach(form => {
            // Récupérer TOUS les inputs et textareas obligatoires du formulaire
            // Utilisation de l'attribut [required] pour identifier les champs obligatoires
            const inputs = form.querySelectorAll('input[required], textarea[required]');

            // === CONFIGURATION PAR INPUT ===
            inputs.forEach(input => {
                // Listener 'input': Déclenché à chaque caractère tapé
                // Permet validation temps réel (feedback immédiat)
                input.addEventListener('input', () => this.validateField(input));

                // Listener 'blur': Déclenché quand l'utilisateur quitte le champ
                // Permet une validation finale du champ (moins intrusif que 'input')
                input.addEventListener('blur', () => this.validateField(input));
            });

            // === CONFIGURATION DU FORMULAIRE ===
            // Listener 'submit': Déclenché lors de l'envoi du formulaire
            // Vérifie que TOUS les champs sont valides avant d'autoriser l'envoi
            form.addEventListener('submit', (e) => this.preventInvalidSubmit(e, form));
        });
    }

    /**
     * Valide un champ individuel en fonction de son type
     *
     * LOGIQUE DE VALIDATION:
     * 1. Identifie le type d'input (email, password, textarea, etc.)
     * 2. Applique les règles de validation appropriées
     * 3. Met à jour les classes Bootstrap (is-valid / is-invalid)
     * 4. Affiche/masque le message d'erreur
     * 5. Pour "nom", déclenche validation serveur (unicité)
     *
     * RÈGLES DE VALIDATION:
     * | Type | Règle | Raison |
     * |------|-------|--------|
     * | email | Format RFC5322 simple | Standard d'authentification |
     * | password | Min 8 caractères | OWASP Top 10 - Sécurité |
     * | nom | Min 2 + unicité serveur | UX + Sécurité (pas de doublons) |
     * | message | Min 10 caractères | Évite les messages vides/courts |
     * | default | Non vide (trim) | Requis par défaut |
     *
     * @method validateField
     * @param {HTMLElement} input - L'input/textarea à valider
     * @returns {boolean} true = valide, false = invalide
     *
     * @fires checkNomAsynchrone() Si le champ est 'nom' et valide localement
     *
     * @example
     * // Usage interne (appelé par listeners)
     * const isValid = this.validateField(emailInput);
     * if (isValid) { console.log('Email valide!'); }
     */
    validateField(input) {
        // === ÉTAPE 1: VÉRIFICATION INITIALE ===
        // Vérifier si le champ est vide (après trim pour ignorer espaces)
        const isEmpty = input.value.trim() === '';
        let isValid = false;

        // === ÉTAPE 2: APPLIQUER RÈGLES DE VALIDATION SELON LE TYPE ===

        if (input.type === 'email') {
            // ✉️ VALIDATION EMAIL
            // Regex simple: non vide + @ + domaine + extension
            // Note: Validation complète RFC5322 est complexe, cette regex est suffisante
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            isValid = emailRegex.test(input.value) && !isEmpty;

        } else if (input.type === 'password') {
            // 🔐 VALIDATION MOT DE PASSE
            // Exigence: minimum 8 caractères (OWASP standard)
            // Raison: Sécurité - mots de passe courts = faciles à craquer
            isValid = input.value.length >= 8;

        } else if (input.name === 'nom') {
            // 👤 VALIDATION NOM D'UTILISATEUR
            // Règle 1 (Client): Min 2 caractères
            isValid = input.value.trim().length >= 2;

            // Règle 2 (Serveur): Vérifier l'unicité avec le serveur
            // Important: Éviter les doublons (contrainte UNIQUE en base)
            if (isValid && input.value.trim().length >= 2) {
                // Appliquer temporairement le style valid
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                this.hideErrorMessage(input);

                // Déclencher vérification asynchrone du serveur
                // Le serveur répondra avec {"exists": true/false}
                this.checkNomAsynchrone(input);
                return; // IMPORTANT: Arrêter ici, attendre réponse serveur
            }

        } else if (input.name === 'message' || input.tagName === 'TEXTAREA') {
            // 💬 VALIDATION MESSAGE/TEXTAREA
            // Exigence: minimum 10 caractères
            // Raison: Éviter les messages trop courts/spam
            isValid = input.value.trim().length >= 10;

        } else {
            // 📝 CAS PAR DÉFAUT (champs texte génériques)
            // Vérifie juste que le champ n'est pas vide
            isValid = !isEmpty;
        }

        // === ÉTAPE 3: APPLIQUER FEEDBACK VISUEL ===
        // Mettre à jour les classes Bootstrap pour feedback utilisateur

        if (isValid) {
            // ✅ CHAMP VALIDE
            input.classList.remove('is-invalid');
            input.classList.add('is-valid'); // Bordure verte + icône checkmark
            this.hideErrorMessage(input); // Masquer message d'erreur

        } else {
            // ❌ CHAMP INVALIDE
            input.classList.remove('is-valid');
            input.classList.add('is-invalid'); // Bordure rouge + icône X
            this.showErrorMessage(input); // Afficher message d'erreur
        }

        return isValid;
    }

    /**
     * Vérifie de manière asynchrone si le nom d'utilisateur existe déjà
     * Appelle le serveur pour valider l'unicité du nom
     *
     * @async
     * @param {HTMLElement} input - L'input du nom à vérifier
     * @returns {Promise<void>}
     */
    checkNomAsynchrone(input) {
        // Vérifier le nom via AJAX avec le serveur
        const nom = input.value.trim();

        // Récupérer le token CSRF depuis le formulaire
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;

        // Créer un FormData pour envoyer les données
        const formData = new FormData();
        formData.append('nom', nom);
        formData.append('csrf_token', csrfToken);

        // Faire un fetch POST vers la route correcte (/?url=users/checkNom)
        fetch('/?url=users/checkNom', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                // Le nom existe déjà
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                this.showErrorMessage(input, data.message);
            } else {
                // Le nom est disponible
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                this.hideErrorMessage(input);
            }
        })
        .catch(error => {
            // Erreur silencieuse
        });
    }

    /**
     * Affiche le message d'erreur sous un champ
     * Utilise la classe Bootstrap invalid-feedback
     *
     * @param {HTMLElement} input - L'input concerné
     * @param {string|null} customMessage - Message d'erreur personnalisé (optionnel)
     * @returns {void}
     */
    showErrorMessage(input, customMessage = null) {
        // Afficher le message d'erreur sous le champ
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            // Si un message personnalisé est fourni, l'utiliser
            if (customMessage) {
                errorDiv.textContent = customMessage;
            }
            errorDiv.style.display = 'block';
        }
    }

    /**
     * Masque le message d'erreur sous un champ
     *
     * @param {HTMLElement} input - L'input concerné
     * @returns {void}
     */
    hideErrorMessage(input) {
        // Masquer le message d'erreur sous le champ
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.style.display = 'none';
        }
    }

    /**
     * Empêche l'envoi du formulaire s'il y a des erreurs de validation
     * Affiche une notification d'erreur si validations échouent
     *
     * @param {Event} e - Événement du submit du formulaire
     * @param {HTMLElement} form - Le formulaire à valider
     * @returns {void}
     */
    preventInvalidSubmit(e, form) {
        // Vérifier si tous les champs sont valides (inputs ET textareas)
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        let allValid = true;

        inputs.forEach(input => {
            // Valider le champ et vérifier qu'il a la classe is-valid
            this.validateField(input);

            // Vérifier si le champ a la classe is-invalid
            if (input.classList.contains('is-invalid')) {
                allValid = false;
            }
        });

        // Si un champ est invalide, empêcher l'envoi
        if (!allValid) {
            e.preventDefault();
            Notifications.error('Veuillez corriger les erreurs du formulaire.');
        }
    }
}

// Exposer la classe globalement pour utilisation dans main.js
window.FormValidator = FormValidator;
