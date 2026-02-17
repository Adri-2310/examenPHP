<?php
/**
 * Nom du fichier : ContactController.php
 *
 * Description :
 * Contrôleur gérant le formulaire de contact
 * Affiche le formulaire et traite les soumissions.
 *
 * Fonctionnalités principales :
 * - Affichage du formulaire de contact
 * - Validation des données
 * - Message de confirmationñ
 *
 * Sécurité :
 * - Vérification du token CSRF
 * - Validation de l'email
 * - Nettoyage des données (strip_tags)
 *
 * @package    App\Controllers
 * @author     Projet Examen PHP
 * @created    2026
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ErrorHandler;

class ContactController extends Controller
{
    /**
     * Affiche et traite le formulaire de contact
     *
     * Cette méthode gère l'affichage du formulaire et le traitement
     * des soumissions avec validation et notification.
     *
     * @return void Affiche la vue contact/index.php
     */
    public function contact()
    {
        // ===== TRAITEMENT DU FORMULAIRE =====
        if (!empty($_POST)) {
            // Validation CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                ErrorHandler::logAccessDenied('contact/contact', 'Token CSRF invalide');
                die("Erreur de sécurité : Token CSRF invalide");
            }

            // Validation des champs obligatoires
            if (!empty($_POST['nom']) && !empty($_POST['email']) &&
                !empty($_POST['sujet']) && !empty($_POST['message'])) {

                // Validation de l'email
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    ErrorHandler::logValidationError(
                        'email',
                        'Format email invalide dans contact: ' . $_POST['email'],
                        '❌ Adresse email invalide'
                    );
                    $erreur = "Adresse email invalide";
                } else {
                    // Nettoyage des données
                    $nom = strip_tags($_POST['nom']);
                    $email = strip_tags($_POST['email']);
                    $sujet = strip_tags($_POST['sujet']);
                    $message = strip_tags($_POST['message']);

                    // Log la soumission du message de contact
                    ErrorHandler::log(
                        "Message de contact reçu de {$email} (sujet: {$sujet})",
                        ErrorHandler::TYPE_INFO,
                        null,
                        ['action' => 'contact/contact', 'method' => 'submit', 'email' => $email]
                    );

                    // Toast de succès
                    $_SESSION['toasts'][] = [
                        'type' => 'success',
                        'message' => '✅ Merci ' . htmlspecialchars($nom) . ' ! Votre message a été envoyé avec succès ! Nous vous répondrons rapidement.'
                    ];

                    // Redirection
                    header('Location: /contact/contact');
                    exit;
                }
            } else {
                ErrorHandler::logValidationError(
                    'form',
                    'Formulaire contact incomplet',
                    '❌ Veuillez remplir tous les champs obligatoires'
                );
                $erreur = "Veuillez remplir tous les champs obligatoires.";
            }
        }

        // Affichage du formulaire
        $this->render('contact/index', [
            'erreur' => $erreur ?? null,
            'titre' => 'Contact'
        ]);
    }
}
