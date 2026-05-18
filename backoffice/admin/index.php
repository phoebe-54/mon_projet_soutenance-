<?php
require_once '../../includes/auth_check.php';
requireRole('admin');
require_once '../../config/database.php';

$dashboardTitle = 'Dashboard admin';
$dashboardLead = 'Vue generale des commandes, produits, clients et livraisons.';
include '../includes/header.php';
?>

<section class="admin-metric-grid">
    <article class="metric-card">
        <strong>Commandes</strong>
        <span>Suivi global des commandes clients</span>
    </article>
    <article class="metric-card">
        <strong>Produits</strong>
        <span>Gestion du catalogue NOCIBE</span>
    </article>
    <article class="metric-card">
        <strong>Livraisons</strong>
        <span>Pilotage des expeditions</span>
    </article>
</section>

        </main>
    </div>
</body>
</html>
