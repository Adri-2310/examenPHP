/**
 * FormValidator.js
 *
 * Valide les formulaires en temps réel (login, register).
 * Fournit un feedback visuel instantané (couleurs verte/rouge) pendant que l'utilisateur tape.
 */
class FormValidator {
    constructor(forms) {
        // Sélectionner tous les inputs dans les formulaires
        this.forms = forms;

        if (this.forms.length === 0) {
            return;
        }

        // Boucler sur chaque formulaire
        this.forms.forEach(form => {
            // Récupérer tous les inputs de ce formulaire
            const inputs = form.querySelectorAll('input[required]');

            // Ajouter les listeners 'input' et 'blur' sur chaque input
            inputs.forEach(input => {
                input.addEventListener('input', () => this.validateField(input));
                input.addEventListener('blur', () => this.validateField(input));
            });

            // Ajouter un listener 'submit' sur le formulaire
            form.addEventListener('submit', (e) => this.preventInvalidSubmit(e, form));
        });
    }

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
            // Nom : minimum 3 caractères et pas vide
            isValid = input.value.trim().length >= 3;

            // Si le nom est valide et contient au moins 3 caractères, vérifier avec le serveur
            if (isValid && input.value.trim().length >= 3) {
                this.checkNomAsynchrone(input);
                return; // Attendre la réponse du serveur
            }
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

    hideErrorMessage(input) {
        // Masquer le message d'erreur sous le champ
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.style.display = 'none';
        }
    }

    preventInvalidSubmit(e, form) {
        // Vérifier si tous les champs sont valides
        const inputs = form.querySelectorAll('input[required]');
        let allValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
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
