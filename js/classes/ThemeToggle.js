/**
 * Classe ThemeToggle - Permet de basculer entre thème clair et sombre
 *
 * Fonctionnalités :
 * - Sauvegarde de la préférence dans localStorage
 * - Application automatique du thème au chargement
 * - Bouton de toggle dans la navigation
 *
 * @class
 */
class ThemeToggle {
    /**
     * Initialise le système de thème
     */
    constructor() {
        this.theme = localStorage.getItem('theme') || 'light';
        this.init();
    }

    /**
     * Initialise les événements et applique le thème
     */
    init() {
        this.applyTheme();

        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggle());
        }
    }

    /**
     * Bascule entre les thèmes clair et sombre
     */
    toggle() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        this.applyTheme();
        localStorage.setItem('theme', this.theme);
    }

    /**
     * Applique le thème au document
     */
    applyTheme() {
        document.body.setAttribute('data-theme', this.theme);

        const icon = document.getElementById('theme-icon');
        if (icon) {
            icon.textContent = this.theme === 'light' ? '🌙' : '☀️';
        }
    }
}

// Export pour utilisation dans main.js
window.ThemeToggle = ThemeToggle;