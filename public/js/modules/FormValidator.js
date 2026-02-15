/**
 * FormValidator.js
 *
 * Valide les formulaires en temps réel (login, register).
 * Fournit un feedback visuel instantané (couleurs verte/rouge) pendant que l'utilisateur tape.
 */
class FormValidator {
    constructor(forms) {
        // TODO: Initialisation du validateur
        // - Sélectionner tous les inputs dans les formulaires
        // - Ajouter des listeners 'input' et 'blur' sur chaque input
        // - Ajouter un listener 'submit' sur chaque formulaire
    }

    validateField(input) {
        // TODO: Valider un champ selon son type
        // - Email : tester avec regex /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        // - Password : tester si length >= 8
        // - Requis : tester si value.trim() !== ''
        // - Ajouter/retirer les classes 'is-valid' / 'is-invalid'
    }

    preventInvalidSubmit(e) {
        // TODO: Empêcher l'envoi si des champs sont invalides
        // - Vérifier si un input a la classe 'is-invalid'
        // - Si oui, faire e.preventDefault()
    }
}

// Exposer la classe globalement pour utilisation dans main.js
window.FormValidator = FormValidator;
