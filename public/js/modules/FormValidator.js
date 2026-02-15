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
            console.warn('⚠️ FormValidator : Aucun formulaire trouvé');
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

        console.log('✅ FormValidator initialisé');
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

    showErrorMessage(input) {
        // Afficher le message d'erreur sous le champ
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
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
