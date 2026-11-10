// Menu mobile
const navToggle = document.getElementById('nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
  navToggle.addEventListener('click', () => {
    const isOpen = mainNav.classList.toggle('open');
    navToggle.setAttribute('aria-expanded', String(isOpen));
  });

  mainNav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      mainNav.classList.remove('open');
      navToggle.setAttribute('aria-expanded', 'false');
    });
  });
}

// Formulaire d'inscription
const registerForm = document.getElementById('register-form');

if (registerForm) {

  // ---------- Message d'erreur ou de succès : on l'amène à l'écran ----------
  const message = document.getElementById('inscription-message');
  if (message) {
    message.focus();
  }

  // ---------- Affichage des champs selon le profil choisi ----------
  const profilAttente = document.getElementById('profil-attente');

  // Renvoie le profil coché ("etudiant", "alumni"...), ou "" si aucun
  const profilChoisi = () => {
    const radio = registerForm.querySelector('input[name="role"]:checked');
    return radio ? radio.value : '';
  };

  const majChampsSelonRole = () => {
    const role = profilChoisi();

    // Tant qu'aucun profil n'est choisi, on cache la suite du formulaire
    registerForm.querySelectorAll('[data-apres-profil]').forEach((element) => {
      element.hidden = role === '';
    });
    if (profilAttente) {
      profilAttente.hidden = role !== '';
    }

    // Blocs propres à certains profils (data-roles="etudiant alumni"...)
    registerForm.querySelectorAll('[data-roles]').forEach((element) => {
      element.hidden = !element.dataset.roles.split(' ').includes(role);
    });

    // Champs obligatoires seulement pour certains profils
    registerForm.querySelectorAll('[data-required-roles]').forEach((champ) => {
      champ.required = champ.dataset.requiredRoles.split(' ').includes(role);
    });

    // Les champs masqués sont désactivés : ni validés ni envoyés
    // (les boutons radio du profil restent toujours actifs)
    registerForm.querySelectorAll('input, select, textarea').forEach((champ) => {
      if (champ.name !== 'role') {
        champ.disabled = champ.closest('[hidden]') !== null;
      }
    });
  };

  registerForm.querySelectorAll('input[name="role"]').forEach((radio) => {
    radio.addEventListener('change', majChampsSelonRole);
  });
  majChampsSelonRole();

  // ---------- Boutons "Afficher" / "Masquer" des mots de passe ----------
  registerForm.querySelectorAll('.mdp-afficher').forEach((bouton) => {
    bouton.addEventListener('click', () => {
      const champ = document.getElementById(bouton.dataset.cible);
      const visible = champ.type === 'password';
      champ.type = visible ? 'text' : 'password';
      bouton.textContent = visible ? 'Masquer' : 'Afficher';
      bouton.setAttribute('aria-pressed', String(visible));
    });
  });

  // ---------- Vérification des mots de passe pendant la saisie ----------
  const mdp = document.getElementById('mdp');
  const mdpConfirm = document.getElementById('mdp_confirm');
  const mdpRegle = document.getElementById('mdp-regle');
  const mdpIdentique = document.getElementById('mdp-identique');

  if (mdp && mdpConfirm) {
    const verifierMdp = () => {
      // Règle des 8 caractères : le texte passe en vert quand elle est respectée
      mdpRegle.textContent = mdp.value.length >= 8 ? '8 caractères minimum : c\'est bon.' : '8 caractères minimum.';
      mdpRegle.classList.toggle('form-hint--ok', mdp.value.length >= 8);

      // Les deux mots de passe doivent être identiques
      if (mdpConfirm.value === '') {
        mdpIdentique.textContent = '';
        mdpIdentique.className = 'form-hint';
        mdpConfirm.setCustomValidity('');
      } else if (mdpConfirm.value === mdp.value) {
        mdpIdentique.textContent = 'Les mots de passe sont identiques.';
        mdpIdentique.className = 'form-hint form-hint--ok';
        mdpConfirm.setCustomValidity('');
      } else {
        mdpIdentique.textContent = 'Les mots de passe ne sont pas identiques.';
        mdpIdentique.className = 'form-hint form-hint--erreur';
        mdpConfirm.setCustomValidity('Les mots de passe ne correspondent pas.');
      }
    };
    mdp.addEventListener('input', verifierMdp);
    mdpConfirm.addEventListener('input', verifierMdp);
  }

  // ---------- CV : nom du fichier choisi et contrôle du format ----------
  const cv = document.getElementById('cv');
  const cvNom = document.getElementById('cv-nom');
  const cvZone = registerForm.querySelector('.fichier-zone');

  if (cv && cvNom && cvZone) {
    cv.addEventListener('change', () => {
      cvZone.classList.remove('fichier-zone--rempli', 'fichier-zone--erreur');
      cv.setCustomValidity('');

      if (cv.files.length === 0) {
        cvNom.textContent = 'Aucun fichier choisi';
        return;
      }

      const fichier = cv.files[0];
      const tailleMo = (fichier.size / 1024 / 1024).toFixed(1);

      // Même contrôle que le serveur (InscriptionTraitement_ahmed.php) : PDF de 2 Mo maximum
      if (!fichier.name.toLowerCase().endsWith('.pdf')) {
        cv.setCustomValidity('Le CV doit être un fichier PDF.');
        cvNom.textContent = fichier.name + ' : ce n\'est pas un fichier PDF.';
        cvZone.classList.add('fichier-zone--erreur');
      } else if (fichier.size > 2 * 1024 * 1024) {
        cv.setCustomValidity('Le CV ne doit pas dépasser 2 Mo.');
        cvNom.textContent = fichier.name + ' (' + tailleMo + ' Mo) : le fichier dépasse 2 Mo.';
        cvZone.classList.add('fichier-zone--erreur');
      } else {
        cvNom.textContent = fichier.name + ' (' + tailleMo + ' Mo)';
        cvZone.classList.add('fichier-zone--rempli');
      }
    });
  }

  // ---------- Compteur de caractères du motif ----------
  const motif = document.getElementById('motif_inscription');
  const motifCompteur = document.getElementById('motif-compteur');

  if (motif && motifCompteur) {
    const majCompteur = () => {
      motifCompteur.textContent = motif.value.length + ' / ' + motif.maxLength + ' caractères';
    };
    motif.addEventListener('input', majCompteur);
    majCompteur();
  }
}