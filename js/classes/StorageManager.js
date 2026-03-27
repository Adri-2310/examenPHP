/**
 * Classe StorageManager - Gestion du stockage avec fallback localStorage → IndexedDB
 *
 * Fonctionnalités :
 * - Essaie localStorage en priorité
 * - Bascule automatiquement à IndexedDB si localStorage est bloqué
 * - Interface identique à localStorage (getItem, setItem, removeItem)
 * - Gestion asynchrone transparente
 *
 * @class
 */
class StorageManager {
    constructor() {
        this.isLocalStorageAvailable = this.checkLocalStorage();
        this.db = null;
        this.isIndexedDBReady = false;

        // Initialiser IndexedDB si localStorage n'est pas disponible
        if (!this.isLocalStorageAvailable) {
            this.initIndexedDB();
        }
    }

    /**
     * Vérifie si localStorage est accessible
     */
    checkLocalStorage() {
        try {
            const test = '__storage_test__';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch (e) {
            console.warn('localStorage bloqué par le navigateur, basculement vers IndexedDB');
            return false;
        }
    }

    /**
     * Initialise IndexedDB comme fallback
     */
    async initIndexedDB() {
        return new Promise((resolve) => {
            const request = indexedDB.open('app_storage', 1);

            request.onerror = () => {
                console.error('Erreur IndexedDB:', request.error);
                resolve(false);
            };

            request.onsuccess = () => {
                this.db = request.result;
                this.isIndexedDBReady = true;
                resolve(true);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                if (!db.objectStoreNames.contains('data')) {
                    db.createObjectStore('data');
                }
            };
        });
    }

    /**
     * Obtient une valeur (synchrone pour localStorage, asynchrone pour IndexedDB)
     * @param {string} key - La clé à récupérer
     * @returns {string|null} La valeur ou null
     */
    getItem(key) {
        if (this.isLocalStorageAvailable) {
            try {
                return localStorage.getItem(key);
            } catch (e) {
                console.warn(`Erreur lors de la lecture de ${key} dans localStorage:`, e);
                return null;
            }
        }

        // Pour IndexedDB, retourner une Promise qui sera gérée
        // Mais pour compatibilité, on stocke le résultat synchrone si disponible
        if (this.isIndexedDBReady && this.db) {
            // Lecture synchrone non possible avec IndexedDB
            // On retourne null et on recommande d'utiliser getItemAsync
            console.warn(`Utilisez getItemAsync() pour IndexedDB ou assurez-vous que localStorage est disponible`);
            return null;
        }

        return null;
    }

    /**
     * Définit une valeur (synchrone pour localStorage, asynchrone pour IndexedDB)
     * @param {string} key - La clé à définir
     * @param {string} value - La valeur à stocker
     */
    setItem(key, value) {
        if (this.isLocalStorageAvailable) {
            try {
                localStorage.setItem(key, value);
                return true;
            } catch (e) {
                console.warn(`Erreur lors de l'écriture de ${key} dans localStorage:`, e);
                return false;
            }
        }

        // Pour IndexedDB
        if (this.isIndexedDBReady && this.db) {
            return this.setItemAsync(key, value);
        }

        console.error('Aucun système de stockage disponible');
        return false;
    }

    /**
     * Supprime une valeur
     * @param {string} key - La clé à supprimer
     */
    removeItem(key) {
        if (this.isLocalStorageAvailable) {
            try {
                localStorage.removeItem(key);
                return true;
            } catch (e) {
                console.warn(`Erreur lors de la suppression de ${key}:`, e);
                return false;
            }
        }

        // Pour IndexedDB
        if (this.isIndexedDBReady && this.db) {
            return this.removeItemAsync(key);
        }

        return false;
    }

    /**
     * Obtient une valeur de manière asynchrone (recommandé pour IndexedDB)
     * @param {string} key - La clé à récupérer
     * @returns {Promise<string|null>} La valeur ou null
     */
    async getItemAsync(key) {
        if (this.isLocalStorageAvailable) {
            return this.getItem(key);
        }

        if (!this.isIndexedDBReady || !this.db) {
            return null;
        }

        return new Promise((resolve) => {
            const transaction = this.db.transaction(['data'], 'readonly');
            const store = transaction.objectStore('data');
            const request = store.get(key);

            request.onsuccess = () => {
                resolve(request.result || null);
            };

            request.onerror = () => {
                console.error('Erreur IndexedDB lors de la lecture:', request.error);
                resolve(null);
            };
        });
    }

    /**
     * Définit une valeur de manière asynchrone (recommandé pour IndexedDB)
     * @param {string} key - La clé à définir
     * @param {string} value - La valeur à stocker
     * @returns {Promise<boolean>} Succès ou non
     */
    async setItemAsync(key, value) {
        if (this.isLocalStorageAvailable) {
            return this.setItem(key, value);
        }

        if (!this.isIndexedDBReady || !this.db) {
            return false;
        }

        return new Promise((resolve) => {
            const transaction = this.db.transaction(['data'], 'readwrite');
            const store = transaction.objectStore('data');
            const request = store.put(value, key);

            request.onsuccess = () => {
                resolve(true);
            };

            request.onerror = () => {
                console.error('Erreur IndexedDB lors de l\'écriture:', request.error);
                resolve(false);
            };
        });
    }

    /**
     * Supprime une valeur de manière asynchrone
     * @param {string} key - La clé à supprimer
     * @returns {Promise<boolean>} Succès ou non
     */
    async removeItemAsync(key) {
        if (this.isLocalStorageAvailable) {
            return this.removeItem(key);
        }

        if (!this.isIndexedDBReady || !this.db) {
            return false;
        }

        return new Promise((resolve) => {
            const transaction = this.db.transaction(['data'], 'readwrite');
            const store = transaction.objectStore('data');
            const request = store.delete(key);

            request.onsuccess = () => {
                resolve(true);
            };

            request.onerror = () => {
                console.error('Erreur IndexedDB lors de la suppression:', request.error);
                resolve(false);
            };
        });
    }

    /**
     * Vide tout le stockage
     * @returns {Promise<boolean>} Succès ou non
     */
    async clear() {
        if (this.isLocalStorageAvailable) {
            try {
                localStorage.clear();
                return true;
            } catch (e) {
                console.warn('Erreur lors du clear localStorage:', e);
                return false;
            }
        }

        if (this.isIndexedDBReady && this.db) {
            return new Promise((resolve) => {
                const transaction = this.db.transaction(['data'], 'readwrite');
                const store = transaction.objectStore('data');
                const request = store.clear();

                request.onsuccess = () => {
                    resolve(true);
                };

                request.onerror = () => {
                    console.error('Erreur IndexedDB lors du clear:', request.error);
                    resolve(false);
                };
            });
        }

        return false;
    }
}

// Initialisation globale unique
window.StorageManager = new StorageManager();
