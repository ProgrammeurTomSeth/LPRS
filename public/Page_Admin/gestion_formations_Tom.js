document.addEventListener('DOMContentLoaded', function () {
    const formulairesSuppression = document.querySelectorAll('.form-suppression-formation');
    formulairesSuppression.forEach(function (formulaire) {
        formulaire.addEventListener('submit', function (event) {
            const message = formulaire.dataset.confirmation;
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
});