
document.addEventListener("DOMContentLoaded", function () {

    const formulaire = document.querySelector("form");

    formulaire.addEventListener("submit", function (event) {

        const siteWeb = document.querySelector(
            'input[name="siteWeb"]'
        ).value.trim();

        if (siteWeb === "") {
            event.preventDefault();
            alert("Veuillez saisir un site web.");
        }

    });

});
