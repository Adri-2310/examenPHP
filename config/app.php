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
    'APP_ENV'           => 'development',
    'APP_DEBUG'         => true,
    'APP_URL'           => 'http://localhost',

    // Logging et erreurs
    'LOG_LEVEL'         => 'debug',              // Logs détaillés
    'LOG_PATH'          => ROOT . '/logs/errors.log',
    'DISPLAY_ERRORS'    => true,                // Afficher les erreurs
    'DB_SHOW_DETAILS'   => true,                // Afficher détails erreurs BD

    // Sécurité
    'SESSION_SECURE'    => false,               // Pas de HTTPS en local
    'HSTS_ENABLED'      => false,               // HSTS désactivé en dev

    // Uploads
    'UPLOAD_MAX_SIZE'   => 5 * 1024 * 1024,    // 5 MB en dev
    'UPLOAD_PATH'       => ROOT . '/public/uploads/',
];
