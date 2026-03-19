/**
 * PasswordToggler.js - Affichage/masquage du mot de passe
 *
 * OBJECTIF:
 * Permettre aux utilisateurs de voir/masquer leur mot de passe en cliquant
 * sur une icône (👁️ = visible, 🙈 = masqué). Améliore l'UX sur mobile/desktop.
 *
 * FONCTIONNALITÉS:
 * - Icône toggle (👁️/🙈) positionnée à droite du champ
 * - Styles CSS générés dynamiquement (injection <style>)
 * - Compatible avec la validation Bootstrap (is-valid/is-invalid)
 * - Responsive et fluide sur tous les appareils
 * - Changement instantané du type d'input (password ↔ text)
 *
 * WORKFLOW:
 * 1. Détecte tous les <input type="password">
 * 2. Enveloppe chaque input dans un div .password-wrapper
 * 3. Ajoute un bouton avec icône 👁️
 * 4. Au clic: change input.type entre "password" et "text"
 * 5. Met à jour l'icône (👁️ ↔ 🙈)
 *
 * UX AMÉLIORATIONS:
 * - Utilisateurs peuvent vérifier leur mot de passe avant submit
 * - Particulièrement utile sur mobile avec petits claviers
 * - Améliore la confiance de l'utilisateur
 * - Réduit les erreurs de saisie de mot de passe
 *
 * @class PasswordToggler
 * @param {void}
 *
 * @example
 * // Initialisation automatique dans main.js
 * new PasswordToggler();
 *
 * @example
 * // Résultat HTML généré
 * <div class="password-wrapper">
 *   <input type="password" class="form-control" ...>
 *   <button class="password-toggle-btn" title="Afficher/Masquer">👁️</button>
 * </div>
 *
 * @author Marmiton-Exam v1.0
 */
class PasswordToggler {
    /**
     * Constructeur - Initialise le toggle pour tous les champs password
     *
     * ACTIONS:
     * 1. Injecte les styles CSS pour .password-wrapper et .password-toggle-btn
     * 2. Sélectionne tous les <input type="password">
     * 3. Crée un bouton toggle pour chaque input
     * 4. Enveloppe l'input dans un conteneur flex
     *
     * POINTS CLÉS:
     * - Les styles sont créés dynamiquement (pas de fichier CSS externe)
     * - Gère les cas avec/sans validation Bootstrap (padding différent)
     * - Utilise position: relative/absolute pour positionnement du bouton
     *
     * @constructor
     * @returns {void}
     */
    constructor() {
        // === ÉTAPE 1: INJECTION DES STYLES CSS ===
        // Crée une balise <style> avec tous les CSS nécessaires
        // Fait en JavaScript pour plus de flexibilité
        this.addStyles();

        // === ÉTAPE 2: SÉLECTION DES INPUTS PASSWORD ===
        // Sélectionne TOUS les champs password du document
        // Utilise querySelectorAll pour cibler les inputs du DOM
        this.passwordInputs = document.querySelectorAll('input[type="password"]');

        // === ÉTAPE 3: VÉRIFICATION ===
        // Si aucun input password, arrêter (pas d'erreur)
        if (this.passwordInputs.length === 0) {
            return;
        }

        // === ÉTAPE 4: CRÉATION DES BOUTONS ===
        // Pour chaque input password, créer son bouton toggle
        // Utilise try/catch pour éviter qu'une erreur bloque tout
        this.passwordInputs.forEach((input) => {
            try {
                this.createToggleButton(input);
            } catch (error) {
                // Silencieuse: continue même si une erreur survient
                // Un input cassé ne doit pas casser les autres
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

            /* Ajouter plus d'espace quand il y a une icône de validation Bootstrap */
            .password-wrapper input.is-valid,
            .password-wrapper input.is-invalid {
                padding-right: 75px;
            }

            .password-toggle-btn {
                position: absolute !important;
                right: 32px !important;
                border: none !important;
                background: transparent !important;
                cursor: pointer;
                padding: 8px 12px !important;
                font-size: 18px;
                z-index: 10;
            }

            /* Positionner l'oeil plus à droite quand pas de validation */
            .password-wrapper input:not(.is-valid):not(.is-invalid) ~ .password-toggle-btn {
                right: 0 !important;
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
