<?php

// Envoi des emails du site avec la bibliothèque PHPMailer
// (copiée dans src/lib/phpmailer, sans Composer)
require_once __DIR__ . '/../lib/phpmailer/Exception.php';
require_once __DIR__ . '/../lib/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../lib/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer_ahmed
{
    // Tableau associatif lu dans config.mail.local.php (null si le fichier n'existe pas)
    private $config;

    public function __construct()
    {
        $this->config = null;
        if (file_exists(__DIR__ . "/config.mail.local.php")) {
            $this->config = require __DIR__ . "/config.mail.local.php";
        }
    }

    // Renvoie true si le compte SMTP est renseigné dans config.mail.local.php
    public function estConfigure()
    {
        return $this->config != null && $this->config['expediteur'] != 'A_REMPLIR' && $this->config['motDePasse'] != 'A_REMPLIR';
    }

    // Envoie un email en texte simple. Renvoie true si l'email est parti, false sinon.
    // Une erreur d'envoi ne doit jamais bloquer le site : elle est seulement notée dans le journal PHP.
    public function envoyer($destinataire, $sujet, $message)
    {
        if (!$this->estConfigure()) {
            return false;
        }

        $mail = new PHPMailer(true); // true : PHPMailer lance une exception en cas d'erreur

        try {
            // Connexion au serveur SMTP
            $mail->isSMTP();
            $mail->Host = $this->config['hote'];
            $mail->Port = $this->config['port'];
            $mail->Timeout = 10; // on n'attend pas plus de 10 secondes le serveur

            if ($this->config['securite'] == '') {
                // Serveur de test sans chiffrement
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
            } else {
                $mail->SMTPSecure = $this->config['securite'];
            }

            if ($this->config['identifiant'] != '') {
                $mail->SMTPAuth = true;
                $mail->Username = $this->config['identifiant'];
                $mail->Password = $this->config['motDePasse'];
            }

            // Contenu de l'email
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($this->config['expediteur'], $this->config['nomExpediteur']);
            $mail->addAddress($destinataire);
            $mail->isHTML(false);
            $mail->Subject = $sujet;
            $mail->Body = $message;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email non envoyé à " . $destinataire . " : " . $mail->ErrorInfo);
            return false;
        }
    }

    // Email envoyé juste après l'inscription : le compte attend la validation d'un gestionnaire
    public function envoyerCompteCree($utilisateur)
    {
        $message = "Bonjour " . $utilisateur->getPrenom() . ",\n\n"
            . "Votre demande d'inscription sur la plateforme du Lycée Robert Schuman a bien été enregistrée.\n"
            . "Un gestionnaire doit maintenant la valider. Vous recevrez un email dès que votre compte sera activé.\n\n"
            . "L'équipe du Lycée Robert Schuman";

        return $this->envoyer($utilisateur->getEmail(), "Votre inscription est en attente de validation", $message);
    }

    // Email envoyé quand un gestionnaire valide le compte
    public function envoyerCompteValide($utilisateur)
    {
        // Sans configuration, on ne peut pas écrire le lien vers le site
        if (!$this->estConfigure()) {
            return false;
        }

        $message = "Bonjour " . $utilisateur->getPrenom() . ",\n\n"
            . "Bonne nouvelle : votre compte sur la plateforme du Lycée Robert Schuman a été validé.\n"
            . "Vous pouvez maintenant vous connecter avec votre adresse email et votre mot de passe :\n"
            . $this->config['urlSite'] . "/connexion/connexion.php\n\n"
            . "L'équipe du Lycée Robert Schuman";

        return $this->envoyer($utilisateur->getEmail(), "Votre compte a été validé", $message);
    }

    // Email envoyé quand un gestionnaire refuse l'inscription ou désactive le compte
    public function envoyerCompteRefuse($utilisateur)
    {
        // Sans configuration, on ne peut pas écrire le lien vers le site
        if (!$this->estConfigure()) {
            return false;
        }

        $message = "Bonjour " . $utilisateur->getPrenom() . ",\n\n"
            . "Un gestionnaire de la plateforme du Lycée Robert Schuman a refusé ou désactivé votre compte.\n"
            . "Vous ne pouvez donc pas vous connecter pour le moment.\n"
            . "Pour toute question, vous pouvez écrire aux gestionnaires avec le formulaire de contact du site :\n"
            . $this->config['urlSite'] . "/page_acceuille/page_accueil.php#contact\n\n"
            . "L'équipe du Lycée Robert Schuman";

        return $this->envoyer($utilisateur->getEmail(), "Votre compte n'est pas actif", $message);
    }
}
