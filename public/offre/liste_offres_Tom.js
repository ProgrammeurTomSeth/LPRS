const boutonMenu = document.getElementById('nav-toggle');
const menu = document.getElementById('main-nav');

if (boutonMenu && menu) {
    boutonMenu.addEventListener('click', function () {
        const ouvert = boutonMenu.getAttribute('aria-expanded') === 'true';

        boutonMenu.setAttribute('aria-expanded', String(!ouvert));
        menu.classList.toggle('is-open', !ouvert);
    });
}
