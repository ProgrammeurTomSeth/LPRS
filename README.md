# SiteEcole

## Envoi des emails

Le site envoie un email à la création d'un compte, puis quand un gestionnaire le valide ou le refuse.
L'envoi utilise PHPMailer, copié dans `src/lib/phpmailer` (pas de Composer).

1. Copier `src/mail/config.mail.local.php.example` en `src/mail/config.mail.local.php`.
2. Remplir le serveur SMTP, l'identifiant, le mot de passe et l'adresse d'expéditeur.
3. Ce fichier est ignoré par Git : le mot de passe n'est jamais publié.

Tant que le fichier contient `A_REMPLIR`, aucun email n'est envoyé et le site fonctionne normalement.
