/**
 * FormValidator.js
 *
 * Valide les formulaires en temps réel (login, register).
 * Fournit un feedback visuel instantané (couleurs verte/rouge) pendant que l'utilisateur tape.
 *
 * @class FormValidator
 * @example
 * // Utilisation dans main.js
 * const forms = document.querySelectorAll('form');
 * new FormValidator(forms);
 */
class FormValidator {
    /**
     * Initialise la validation pour tous les formulaires
     * @constructor
     * @param {NodeList} forms - Liste des formulaires à valider
     */
    constructor(forms) {
        // Sélectionner tous les inputs dans les formulaires
        this.forms = forms;

        if (this.forms.length === 0) {
            return;
        }

        // Boucler sur chaque formulaire
        this.forms.forEach(form => {
            // Récupérer tous les inputs ET textareas de ce formulaire
            const inputs = form.querySelectorAll('input[required], textarea[required]');

            // Ajouter les listeners 'input' et 'blur' sur chaque input/textarea
            inputs.forEach(input => {
                input.addEventListener('input', () => this.validateField(input));
                input.addEventListener('blur', () => this.validateField(input));
            });

            // Ajouter un listener 'submit' sur le formulaire
            form.addEventListener('submit', (e) => this.preventInvalidSubmit(e, form));
        });
    }

    /**
     * Valide un champ de formulaire en fonction de son type
     * Applique les classes Bootstrap is-valid/is-invalid
     *
     * @param {HTMLElement} input - L'élément input ou textarea à valider
     * @returns {boolean} true si le champ est valide, false sinon
     */
    validateField(input) {
        // Vérifier si le champ est vide
        const isEmpty = input.value.trim() === '';

        // Déterminer le type de validation selon le type d'input
        let isValid = false;

        if (input.type === 'email') {
            // Email : tester avec regex
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            isValid = emailRegex.test(input.value) && !isEmpty;
        } else if (input.type === 'password') {
            // Password : tester si length >= 8
            isValid = input.value.length >= 8;
        } else if (input.name === 'nom') {
            // Nom : minimum 2 caractères et pas vide (comme indiqué dans le formulaire)
            isValid = input.value.trim().length >= 2;

            // Si le nom est valide et contient au moins 2 caractères, vérifier avec le serveur
            if (isValid && input.value.trim().length >= 2) {
                // Appliquer temporairement la classe is-valid
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                this.hideErrorMessage(input);

                // Puis vérifier avec le serveur
                this.checkNomAsynchrone(input);
                return; // Attendre la réponse du serveur
            }
        } else if (input.name === 'message' || input.tagName === 'TEXTAREA') {
            // Message/Textarea : minimum 10 caractères
            isValid = input.value.trim().length >= 10;
        } else {
            // Requis : tester si value.trim() !== ''
            isValid = !isEmpty;
        }

        // Appliquer les classes Bootstrap
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            this.hideErrorMessage(input);
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            this.showErrorMessage(input);
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
