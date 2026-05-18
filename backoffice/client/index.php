<?php
require_once '../../includes/auth_check.php';
requireRole('client');
require_once '../../config/database.php';

$dashboardTitle = 'Dashboard client';
$dashboardLead = 'Retrouvez vos commandes, votre panier et le suivi de livraison.';
include '../includes/header.php';
?>

<section class="admin-metric-grid">
    <article class="metric-card">
        <strong>Catalogue</strong>
        <span>Consulter les produits disponibles</span>
    </article>
    <article class="metric-card">
        <strong>Panier</strong>
        <span>Verifier les produits selectionnes</span>
    </article>
    <article class="metric-card">
        <strong>Suivi</strong>
        <span>Suivre les livraisons en cours</span>
    </article>
</section>

        </main>
    </div>
</body>
</html>
