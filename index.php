<?php
$pageTitle = 'Accueil';
$basePath = '';
$bodyClass = 'site-shell homepage-bg';
include 'includes/header.php';
?>

<section class="hero-section-nocibe">
    <div class="home-hero-inner"> 
        <div class="home-hero-copy">
            <h1>Plateforme de commande et de suivi de ciment.</h1>
            <p>
                Une solution simple pour commander vos ciments et suivre vos livraisons
                depuis un espace fiable et adapté aux professionnels.
            </p>
        </div>
        <div class="home-hero-actions hero-actions">
            <a href="register.php" class="btn btn-brand home-action-btn">
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                Creer un compte
            </a>
            <a href="login.php" class="btn btn-soft home-action-btn">
                <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                Se connecter
            </a>
        </div>
    </div>
</section>

<main class="site-container">

    <section class="home-about-section" aria-labelledby="about-nocibe">
        <div class="overview-title">
            <h2 id="about-nocibe">À propos de NOCIBE</h2>
            <p>Notre mission est de simplifier l'achat de ciment avec une plateforme moderne et fiable.</p>
        </div>

        <div class="home-about-grid">
            <article class="home-about-card">
                <img src="assets/images/Copilot_20260428_135728.png" alt="Production locale NOCIBE">
                <i class="fa-solid fa-industry" aria-hidden="true"></i>
                <h3>Production locale</h3>
                <p>NOCIBE produit du ciment de qualité directement accessible en ligne pour les professionnels et particuliers.</p>
            </article>
            <article class="home-about-card">
                <img src="assets/images/Copilot_20260428_135752.png" alt="Livraison rapide NOCIBE">
                <i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
                <h3>Livraison rapide</h3>
                <p>Nous proposons une livraison en 24-72h selon les zones, avec un suivi de commande clair.</p>
            </article>
            <article class="home-about-card">
                <img src="assets/images/Copilot_20260518_132212.png" alt="Support dédié">
                <i class="fa-solid fa-headset" aria-hidden="true"></i>
                <h3>Support dédié</h3>
                <p>Une équipe à l'écoute vous accompagne pour le choix du produit et le suivi de votre commande.</p>
            </article>
        </div>
    </section>

    <section class="home-categories-section" aria-labelledby="product-categories">
        <div class="overview-title">
            <h2 id="product-categories">Catégories de produits</h2>
            <p>Explorez nos différentes familles de ciment.</p>
        </div>

        <div class="categories-grid">
            <a href="login.php?login_required=order" class="category-card">
                <img src="assets/images/Copilot_20260506_180528.png" alt="Ciment Portland">
                <strong>Ciment Portland</strong>
                <p>Le ciment standard utilisé pour les constructions durables.</p>
            </a>
            <a href="login.php?login_required=order" class="category-card">
                <img src="assets/images/Copilot_20260428_135747.png" alt="Ciment standard">
                <strong>Ciment standard</strong>
                <p>Une solution fiable pour les besoins classiques du bâtiment.</p>
            </a>
            <a href="login.php?login_required=order" class="category-card">
                <img src="assets/images/Copilot_20260518_123256.png" alt="Ciment multi-usage">
                <strong>Ciment multi-usage</strong>
                <p>Polyvalent, adapté à divers usages du bâtiment.</p>
            </a>
            <a href="login.php?login_required=order" class="category-card">
                <img src="assets/images/Copilot_20260504_144154.png" alt="Ciment haute performance">
                <strong>Ciment haute performance</strong>
                <p>Résistance élevée pour structures exigeantes.</p>
            </a>
            <a href="login.php?login_required=order" class="category-card">
                <img src="assets/images/Copilot_20260504_144151.png" alt="Ciment blanc">
                <strong>Ciment blanc</strong>
                <p>Finition soignée pour chantiers décoratifs et architecturaux.</p>
            </a>
        </div>
    </section>


    <section class="home-process-section">
        <div class="overview-title">
            <h2>Comment ça marche</h2>
            <p>Un parcours simple, penser pour les clients et les gestionnaires</p>
        </div>

        <div class="home-process-grid">
            <article class="home-process-card">
                <span>01</span>
                <div class="home-process-visual">
                    <img src="assets/images/home-process-order.png" alt="Gamme de produits ciment NOCIBE">
                </div>
                <i class="fa-solid fa-boxes-stacked" aria-hidden="true"></i>
                <h3>Choisir les produits</h3>
                <p>Consultez les types de ciment disponibles et selectionnez la quantite souhaitee.</p>
            </article>

            <article class="home-process-card">
                <span>02</span>
                <div class="home-process-visual">
                    <img src="assets/images/home-process-command-validation.png" alt="Validation de commande NOCIBE">
                </div>
                <i class="fa-solid fa-file-signature" aria-hidden="true"></i>
                <h3>Passer commande</h3>
                <p>Remplissez les informations de livraison et validez votre demande en quelques clics.</p>
            </article>

            <article class="home-process-card">
                <span>03</span>
                <div class="home-process-visual">
                    <img src="assets/images/payment-nocibe-command.png" alt="Paiement en ligne securise NOCIBE">
                </div>
                <i class="fa-solid fa-credit-card" aria-hidden="true"></i>
                <h3>Payer en ligne</h3>
                <p>Effectuez votre paiement de maniere securisee et recevez la confirmation.</p>
            </article>

            <article class="home-process-card">
                <span>04</span>
                <div class="home-process-visual">
                    <img src="assets/images/home-process-delivery.png" alt="Camion de livraison NOCIBE">
                </div>
                <i class="fa-solid fa-route" aria-hidden="true"></i>
                <h3>Suivre la livraison</h3>
                <p>Gardez une vue claire sur l'etat de votre commande jusqu'a la reception.</p>
            </article>
        </div>
    </section>
    <section class="home-support-section" id="support">
        <div>
            <span class="home-hero-kicker">Support client</span>
            <h2>Besoin d'aide pour une commande ?</h2>
            <p>
                Notre equipe vous accompagne pour le choix du produit et le suivi de votre commande.
            </p>
        </div>
        <a href="login.php?login_required=order" class="btn btn-brand home-support-btn">
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            Commander maintenant
        </a>
    </section>

    <section class="home-contact-section" aria-labelledby="contact-nocibe">
        <div class="overview-title">
            <h2 id="contact-nocibe">Contact</h2>
            <p>Contactez-nous pour toute question sur les produits, les commandes ou la livraison.</p>
        </div>
        <div class="contact-grid">
            <article class="contact-card">
                <i class="fa-solid fa-phone"></i>
                <h3>Téléphone</h3>
                <p>(+229) 01 21 50 00 42<br>(+229) 01 21 31 55 13</p>
            </article>
            <article class="contact-card">
                <i class="fa-solid fa-envelope"></i>
                <h3>Email</h3>
                <p>contact@nocibe.com</p>
            </article>
            <article class="contact-card">
                <i class="fa-solid fa-map-marker-alt"></i>
                <h3>Adresse</h3>
                <p>Villa N°11, Résidence Akarade<br>08 BP 1024<br>Cotonou - Bénin</p>
            </article>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
