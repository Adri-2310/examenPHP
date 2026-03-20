<?php
/**
 * Configuration de l'application par environnement
 *
 * Ce fichier diffère entre les branches develop et main
 * Ne contient AUCUN secret (pas de password, pas de clé API)
 * Les secrets sont dans .env
 */

return [
    // Environnement d'exécution
    'APP_ENV'           => 'production',
    'APP_DEBUG'         => false,
    'APP_URL'           => 'https://votre-domaine.com',

    // Logging et erreurs
    'LOG_LEVEL'         => 'error',              // Logs erreurs seulement
    'LOG_PATH'          => ROOT . '/logs/errors.log',
    'DISPLAY_ERRORS'    => false,               // Ne pas afficher les erreurs
    'DB_SHOW_DETAILS'   => false,               // Ne pas afficher détails erreurs BD

    // Sécurité
    'SESSION_SECURE'    => true,                // HTTPS requis
    'HSTS_ENABLED'      => true,                // HSTS activé en prod

    // Uploads
    'UPLOAD_MAX_SIZE'   => 2 * 1024 * 1024,    // 2 MB en prod
    'UPLOAD_PATH'       => ROOT . '/public/uploads/',
];
