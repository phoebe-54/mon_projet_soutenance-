<?php
$pageTitle = 'Accueil';
$basePath = '';
$bodyClass = 'site-shell homepage-bg';
include 'includes/header.php';
?>

<section class="hero-section-nocibe">
    <div class="home-hero-inner"> 
        <div class="home-hero-copy">
            <span class="home-hero-kicker">CimenteriNOe NOCIBE</span>
            <h1>Commandez votre ciment en ligne, simplement et rapidement.</h1>
            <p>
                Une plateforme moderne pour consulter les produits, passer vos commandes,
                suivre les livraisons et gerer vos achats de ciment en toute confiance.
            </p>
        </div>
        <div class="home-hero-actions hero-actions">
            <a href="client/catalogue.php" class="btn btn-brand px-4 py-3">
                <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                Voir catalogue
            </a>
            <a href="login.php" class="btn btn-soft px-4 py-3">
                <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                Se connecter
            </a>
        </div>
    </div>
</section>

<main class="site-container">

    <section class="dashboard-overview" aria-labelledby="why-nocibe">
        <div class="overview-title">
            <h2 id="why-nocibe">Pourquoi choisir NOCIBE</h2>
            <p>Les raisons de faire confiance a notre plateforme</p>
        </div>

        <div class="metrics-grid">
            <div class="metric-box">
                <div class="metric-icon">
                    <i class="fa-solid fa-certificate"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">100%</div>
                    <div class="metric-label">Ciment certifie</div>
                </div>
            </div>

            <div class="metric-box">
                <div class="metric-icon">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">24-48h</div>
                    <div class="metric-label">Livraison rapide</div>
                </div>
            </div>

            <div class="metric-box">
                <div class="metric-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">5000+</div>
                    <div class="metric-label">Clients satisfaits</div>
                </div>
            </div>

            <div class="metric-box">
                <div class="metric-icon">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value">Meilleurs prix</div>
                    <div class="metric-label">Garantis du marche</div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-process-section">
        <div class="overview-title">
            <h2>Comment ca marche</h2>
            <p>Un parcours simple, pense pour les clients et les gestionnaires</p>
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
                Notre equipe vous accompagne pour le choix du ciment, la validation
                des commandes et le suivi des livraisons.
            </p>
        </div>
        <a href="client/catalogue.php" class="btn btn-brand">
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            Demarrer maintenant
        </a>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
