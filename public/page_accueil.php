<?php

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>École Lourdeault — Ressources éducatives en ligne</title>
    <meta name="description" content="École Lourdeault : une éducation moderne et dynamique pour tous les élèves. Programmes, activités, événements et accompagnement personnalisé.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<a class="skip-link" href="#main">Aller au contenu principal</a>

<header class="site-header">
    <div class="container header-inner">
        <a href="#" class="logo">École<span>Robert Shuman</span></a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <li><a href="#mission">Programme</a></li>
                <li><a href="#evenements">Activités</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>

        <a href="#contact" class="btn btn-primary header-cta">Inscription Rapide</a>

        <button class="nav-toggle" id="nav-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<main id="main">

    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h1>Bienvenue ici</h1>
            <p>Épanouissez-vous dans l'éducation moderne et dynamique.</p>
            <a href="#mission" class="btn btn-primary btn-lg">Commencer Maintenant</a>
        </div>
    </section>

    <section class="section mission" id="mission">
        <div class="container mission-grid">
            <div class="mission-media">
                <img src="https://images.unsplash.com/photo-1571260899304-425eee4c7efc?auto=format&fit=crop&w=900&q=70" alt="Élèves en classe à l'École Lourdeault">
            </div>
            <div class="mission-text">
                <p class="eyebrow">Notre Mission</p>
                <h2>Une éducation qui s'adapte à chaque élève</h2>
                <p>À l'École Lourdeault, nous croyons fermement que chaque élève mérite une éducation qui répond à ses besoins et révèle son plein potentiel.</p>
                <p>Notre vision est d'inspirer l'apprentissage tout au long de la vie et d'encourager chacun à atteindre l'excellence, à son rythme et selon ses talents.</p>
                <p>Fort d'un historique riche depuis 1995, nous avons cultivé une communauté d'apprentissage engagée, bienveillante et inclusive.</p>

                <div class="stats">
                    <div class="stat">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Élèves heureux</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">30</span>
                        <span class="stat-label">Années d'excellence</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">12</span>
                        <span class="stat-label">Programmes variés</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section features">
        <div class="container">
            <p class="eyebrow center">La Vie Étudiante</p>
            <h2 class="center">Ce qui rend notre école unique</h2>

            <div class="features-grid">
                <article class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Apprentissage Actif</h3>
                    <p>Méthodes dynamiques et engageantes en classe.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon">💻</div>
                    <h3>Technologie Avancée</h3>
                    <p>Équipements modernes facilitant le processus éducatif.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Environnement Sécure</h3>
                    <p>Un espace accueillant pour tous les élèves.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <h3>Programmes Variés</h3>
                    <p>Des activités qui nourrissent la créativité et l'esprit.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon">🤝</div>
                    <h3>Partenariats Locaux</h3>
                    <p>Collaborations enrichissantes avec des entreprises et organismes.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon">🧑‍🏫</div>
                    <h3>Support Personnalisé</h3>
                    <p>Accompagnement dédié à chaque élève.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="section testimonials">
        <div class="container">
            <p class="eyebrow center">Avis</p>
            <h2 class="center">Voici ce que nos utilisateurs pensent de nous</h2>

            <div class="testimonials-grid">
                <blockquote class="testimonial-card">
                    <p>« Une plateforme incroyable qui aide mes enfants dans leurs études. »</p>
                    <cite>Marie Dupont</cite>
                </blockquote>
                <blockquote class="testimonial-card">
                    <p>« Facile à utiliser et très utile pour mes cours. »</p>
                    <cite>Pierre Martin</cite>
                </blockquote>
                <blockquote class="testimonial-card">
                    <p>« Une ressource essentielle pour chaque étudiant moderne. »</p>
                    <cite>Sophie Leroy</cite>
                </blockquote>
            </div>

            <div class="center">
                <a href="#" class="btn btn-outline">Lire plus d'avis</a>
            </div>
        </div>
    </section>

    <section class="section events" id="evenements">
        <div class="container">
            <p class="eyebrow center">Activités</p>
            <h2 class="center">Événements à Venir</h2>

            <div class="events-grid">
                <article class="event-card">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=70" alt="Journée Portes Ouvertes">
                    <div class="event-body">
                        <time datetime="2026-10-10">10 octobre 2026</time>
                        <h3>Journée Portes Ouvertes</h3>
                        <p>Venez découvrir notre école et rencontrer le personnel et les élèves.</p>
                        <a href="#contact" class="btn btn-sm">S'inscrire</a>
                    </div>
                </article>

                <article class="event-card">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=70" alt="Remise des Diplômes">
                    <div class="event-body">
                        <time datetime="2026-11-15">15 novembre 2026</time>
                        <h3>Remise des Diplômes</h3>
                        <p>Célébration des réalisations de nos élèves avec fierté et émotion.</p>
                        <a href="#contact" class="btn btn-sm">S'inscrire</a>
                    </div>
                </article>

                <article class="event-card">
                    <img src="https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&w=600&q=70" alt="Fête de Noël">
                    <div class="event-body">
                        <time datetime="2026-12-20">20 décembre 2026</time>
                        <h3>Fête de Noël</h3>
                        <p>Venez célébrer les festivités de fin d'année avec spectacles et surprises.</p>
                        <a href="#contact" class="btn btn-sm">S'inscrire</a>
                    </div>
                </article>

                <article class="event-card">
                    <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=600&q=70" alt="Semaine de la Science">
                    <div class="event-body">
                        <time datetime="2027-01-18">18 janvier 2027</time>
                        <h3>Semaine de la Science</h3>
                        <p>Événements interactifs et ateliers pour éveiller la curiosité scientifique.</p>
                        <a href="#contact" class="btn btn-sm">S'inscrire</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section faq">
        <div class="container">
            <p class="eyebrow center">FAQ</p>
            <h2 class="center">Questions Fréquemment Posées</h2>

            <div class="faq-list">
                <details class="faq-item" open>
                    <summary>Comment s'inscrire sur la plateforme ?</summary>
                    <p>Visitez notre page d'inscription et remplissez le formulaire.</p>
                </details>
                <details class="faq-item">
                    <summary>Quels sujets sont couverts ici ?</summary>
                    <p>Nous proposons des cours sur de nombreux sujets académiques.</p>
                </details>
                <details class="faq-item">
                    <summary>Puis-je utiliser la plateforme sur mobile ?</summary>
                    <p>Oui, notre site est entièrement compatible avec les mobiles.</p>
                </details>
                <details class="faq-item">
                    <summary>Offrez-vous des cours en direct ?</summary>
                    <p>Oui, nous avons des sessions en direct avec des instructeurs.</p>
                </details>
                <details class="faq-item">
                    <summary>Comment contacter le support ?</summary>
                    <p>Envoyez-nous un email à ...</p>
                </details>
            </div>
        </div>
    </section>

    <section class="cta-banner">
        <div class="container">
            <h2>Rejoignez notre communauté étudiante aujourd'hui</h2>
            <p>Ne laissez pas vos études stagner ! Inscrivez-vous dès maintenant et profitez de ressources exceptionnelles.</p>
            <a href="#contact" class="btn btn-primary btn-lg">Commencer ma formation</a>
        </div>
    </section>

    <section class="section contact" id="contact">
        <div class="container contact-grid">
            <div class="contact-info">
                <p class="eyebrow">Nous Contacter</p>
                <h2>Contactez-Nous Rapidement</h2>
                <ul>
                    <li><strong>Téléphone :</strong> <a href="tel:">...</a></li>
                    <li><strong>Email :</strong> <a href="mai@RobertShuman.fr">...</a></li>
                    <li><strong>Adresse :</strong>...Dugny</li>
                </ul>
                <div class="socials">
                    <a href="#" aria-label="Facebook">FB</a>
                    <a href="#" aria-label="Instagram">IG</a>
                    <a href="#" aria-label="YouTube">YT</a>
                    <a href="#" aria-label="LinkedIn">IN</a>
                </div>
            </div>

            <form class="contact-form" id="contact-form">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" placeholder="Nom" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required>

                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Message" rows="5" required></textarea>

                <button type="submit" class="btn btn-primary">Envoyer</button>
                <p class="form-status" id="form-status" hidden></p>
            </form>
        </div>
    </section>

</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <a href="#" class="logo">École<span>Robert Shuman</span></a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <p class="footer-copy">© 2026 École Robert Shuman. Tous droits réservés.</p>
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>
