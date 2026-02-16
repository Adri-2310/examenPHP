/**
 * PasswordToggler.js
 *
 * Gère l'affichage/masquage du mot de passe dans les formulaires.
 * Permet aux utilisateurs de vérifier leur mot de passe avant de soumettre.
 * Crée des styles CSS dynamiquement et ajoute des boutons toggle à chaque input password.
 *
 * @class PasswordToggler
 * @example
 * // Utilisation dans main.js
 * new PasswordToggler();
 */
class PasswordToggler {
    /**
     * Initialise le gestionnaire de visibilité des mots de passe
     * Ajoute les styles CSS et crée les boutons toggle pour chaque input password
     * @constructor
     */
    constructor() {
        // Ajouter du CSS au document
        this.addStyles();

        // Sélectionner tous les champs password du formulaire
        this.passwordInputs = document.querySelectorAll('input[type="password"]');

        if (this.passwordInputs.length === 0) {
            return;
        }

        // Boucler sur chaque input password
        this.passwordInputs.forEach((input) => {
            try {
                this.createToggleButton(input);
            } catch (error) {
                // Erreur silencieuse
            }
        });
    }

    /**
     * Ajoute les styles CSS pour le wrapper et bouton toggle
     * Crée une balise <style> et l'injecte dans le <head> du document
     *
     * @returns {void}
     */
    addStyles() {
        // Créer une balise <style> et l'ajouter au document
        const style = document.createElement('style');
        style.textContent = `
            .password-wrapper {
                position: relative;
                display: flex;
                align-items: center;
            }

            .password-wrapper input[type="password"],
            .password-wrapper input[type="text"] {
                padding-right: 40px;
                flex: 1;
            }

            .password-toggle-btn {
                position: absolute !important;
                right: 0 !important;
                border: none !important;
                background: transparent !important;
                cursor: pointer;
                padding: 8px 12px !important;
                font-size: 18px;
                z-index: 10;
            }

            .password-toggle-btn:hover {
                background-color: rgba(0, 0, 0, 0.05) !important;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Crée et insère le bouton toggle pour un input password
     * Enveloppe l'input dans un wrapper et ajoute le bouton avec les styles appropriés
     *
     * @param {HTMLElement} passwordInput - L'input password concerné
     * @returns {void}
     */
    createToggleButton(passwordInput) {
        // Créer le bouton de toggle
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'password-toggle-btn';
        toggleBtn.textContent = '👁️';
        toggleBtn.title = 'Afficher/Masquer le mot de passe';

        // Ajouter le listener au bouton
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.togglePassword(passwordInput, toggleBtn);
        });

        // Créer un wrapper autour de l'input
        const wrapper = document.createElement('div');
        wrapper.className = 'password-wrapper';

        // Insérer le wrapper avant l'input
        passwordInput.parentNode.insertBefore(wrapper, passwordInput);

        // Déplacer l'input à l'intérieur du wrapper
        wrapper.appendChild(passwordInput);

        // Ajouter le bouton après l'input
        wrapper.appendChild(toggleBtn);
    }

    /**
     * Bascule la visibilité du mot de passe (change input type entre password et text)
     * Met à jour l'icône du bouton en conséquence (👁️ ou 🙈)
     *
     * @param {HTMLElement} passwordInput - L'input password à basculer
     * @param {HTMLElement} toggleBtn - Le bouton toggle
     * @returns {void}
     */
    togglePassword(passwordInput, toggleBtn) {
        // Basculer entre password et text
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleBtn.textContent = '🙈';
            toggleBtn.title = 'Masquer le mot de passe';
        } else {
            passwordInput.type = 'password';
            toggleBtn.textContent = '👁️';
            toggleBtn.title = 'Afficher le mot de passe';
        }
    }
}

// Exposer la classe globalement pour utilisation dans main.js
window.PasswordToggler = PasswordToggler;
