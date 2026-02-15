/**
 * PasswordToggler.js
 *
 * Gère l'affichage/masquage du mot de passe dans les formulaires.
 * Permet aux utilisateurs de vérifier leur mot de passe avant de soumettre.
 */
class PasswordToggler {
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
