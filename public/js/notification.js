/**
 * Helper pour les notifications Toastify
 */
const Notifications = {
    /**
     * Affiche une notification de succès
     * @param {string} message - Le message à afficher
     */
    success(message) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            stopOnFocus: true
        }).showToast();
    },

    /**
     * Affiche une notification d'erreur
     * @param {string} message - Le message à afficher
     */
    error(message) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
            stopOnFocus: true
        }).showToast();
    },

    /**
     * Affiche une notification d'information
     * @param {string} message - Le message à afficher
     */
    info(message) {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #00d2ff, #3a7bd5)",
            stopOnFocus: true
        }).showToast();
    }
};

window.Notifications = Notifications;