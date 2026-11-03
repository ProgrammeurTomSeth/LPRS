document.addEventListener('DOMContentLoaded', function () {
    const formulaire = document.querySelector('.form-creation-offre');

    if (!formulaire) {
        return;
    }

    const champTitre = document.getElementById('titre');
    const champDescription = document.getElementById('description');
    const champMissions = document.getElementById('missions');
    const champSalaire = document.getElementById('salaire');
    const champType = document.getElementById('type');
    const champProfil = document.getElementById('profil_cible');

    const boutonPublication = formulaire.querySelector('button[type="submit"]');

    function afficherErreur(champ, message) {
        let erreur = champ.parentElement.querySelector('.erreur-js');

        if (!erreur) {
            erreur = document.createElement('small');
            erreur.classList.add('erreur-js');
            erreur.setAttribute('role', 'alert');
            champ.parentElement.appendChild(erreur);
        }
        erreur.textContent = message;
        champ.classList.add('champ-invalide');
        champ.setAttribute('aria-invalid', 'true');
        champ.setCustomValidity(message);
    }

    function supprimerErreur(champ) {
        const erreur = champ.parentElement.querySelector('.erreur-js');

        if (erreur) {
            erreur.remove();
        }
        champ.classList.remove('champ-invalide');
        champ.removeAttribute('aria-invalid');
        champ.setCustomValidity('');
    }

    function verifierChampTexte(champ, nomChamp) {

        const valeur = champ.value.trim();
        if (valeur === '') {
            afficherErreur(
                champ,
                'Le champ « ' + nomChamp + ' » est obligatoire.'
            );
            return false;
        }
        supprimerErreur(champ);
        return true;
    }

    function verifierSalaire() {
        const valeur = champSalaire.value.trim();

        if (valeur === '') {
            supprimerErreur(champSalaire);
            return true;
        }
        const salaireNormalise = valeur.replace(',', '.');
        const formatValide = /^\d+(\.\d{1,2})?$/.test(salaireNormalise);
        if (!formatValide) {
            afficherErreur(champSalaire, 'Veuillez saisir un salaire valide.');
            return false;
        }
        const salaire = Number(salaireNormalise);
        if (!Number.isFinite(salaire) || salaire < 0) {
            afficherErreur(champSalaire, 'Le salaire doit être un nombre positif ou nul.');
            return false;
        }
        supprimerErreur(champSalaire);
        return true;
    }

    function verifierType() {
        const typesAutorises = ['stage', 'alternance', 'cdd', 'cdi'];

        if (!typesAutorises.includes(champType.value)) {
            afficherErreur(champType, 'Veuillez sélectionner un type de contrat valide.');
            return false;
        }
        supprimerErreur(champType);
        return true;
    }

    function verifierProfil() {
        const profilsAutorises = ['tous', 'etudiant', 'alumni'];

        if (!profilsAutorises.includes(champProfil.value)) {
            afficherErreur(champProfil, 'Veuillez sélectionner un profil valide.');
            return false;
        }
        supprimerErreur(champProfil);
        return true;
    }

    function nettoyerChamps() {
        champTitre.value = champTitre.value.trim();
        champDescription.value = champDescription.value.trim();
        champMissions.value = champMissions.value.trim();
    }

    formulaire.addEventListener('submit', function (event) {
        nettoyerChamps();

        let formulaireValide = true;
        if (!verifierChampTexte(champTitre, 'Titre')) {
            formulaireValide = false;
        }
        if (!verifierChampTexte(champDescription, 'Description')) {
            formulaireValide = false;
        }
        if (!verifierChampTexte(champMissions, 'Missions')) {
            formulaireValide = false;
        }
        if (!verifierSalaire()) {
            formulaireValide = false;
        }
        if (!verifierType()) {
            formulaireValide = false;
        }
        if (!verifierProfil()) {
            formulaireValide = false;
        }
        if (!formulaireValide) {
            event.preventDefault();
            const premierChampInvalide = formulaire.querySelector(
                '.champ-invalide'
            );
            if (premierChampInvalide) {
                premierChampInvalide.focus();
            }
            return;
        }
        boutonPublication.disabled = true;
        boutonPublication.textContent = 'Publication en cours...';
    });

    const champs = [champTitre, champDescription, champMissions, champSalaire, champType, champProfil];

    champs.forEach(function (champ) {
        champ.addEventListener('input', function () {
            supprimerErreur(champ);
        });
        champ.addEventListener('change', function () {
            supprimerErreur(champ);
        });
    });
});