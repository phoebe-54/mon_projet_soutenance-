<?php
include '../includes/auth_check.php';
$pageTitle = 'Suivi';
$basePath = '../';
include '../includes/header.php';
?>

<main class="site-container section-block">
    <div class="section-header">
        <div>
            <h2 class="section-title">Suivi de commande</h2>
            <p class="section-subtitle">Le suivi adopte une timeline plus nette et un recapitulatif plus lisible.</p>
        </div>
    </div>

    <section class="tracking-layout">
        <article class="tracking-card">
            <div class="input-group mb-4">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control" placeholder="Numero de commande ex: NOC-2026-001">
                <button class="btn btn-brand px-4">Suivre</button>
            </div>

            <div class="timeline">
                <div class="timeline-step done">
                    <h4>Commande recue</h4>
                    <p>23 avril 2026, 14:30</p>
                </div>
                <div class="timeline-step done">
                    <h4>Paiement confirme</h4>
                    <p>23 avril 2026, 14:35</p>
                </div>
                <div class="timeline-step active">
                    <h4>En preparation</h4>
                    <p>La commande est en cours de traitement.</p>
                </div>
                <div class="timeline-step">
                    <h4>Expedition</h4>
                    <p>En attente de prise en charge.</p>
                </div>
                <div class="timeline-step">
                    <h4>Livraison</h4>
                    <p>La remise finale apparaitra ici.</p>
                </div>
            </div>
        </article>

        <aside class="tracking-card">
            <img src="../assets/images/delivery-hero.svg" alt="Illustration suivi">
            <h3>Commande NOC-2026-001</h3>
            <div class="summary-line"><span>Client</span><span>Jean Dupont</span></div>
            <div class="summary-line"><span>Adresse</span><span>Dakar</span></div>
            <div class="summary-line"><span>Montant</span><span>155 000 FCFA</span></div>
            <div class="summary-line"><span>Statut</span><span class="status-pill status-pending">En preparation</span></div>
        </aside>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
