<?php
require_once '../../includes/auth_check.php';
requireRole('commercial');
require_once '../../config/database.php';

$dashboardTitle = 'Dashboard commercial';
$dashboardLead = 'Suivez les commandes et accompagnez les clients.';
include '../includes/header.php';
?>

<section class="admin-metric-grid">
    <article class="metric-card">
        <strong>Commandes</strong>
        <span>Validation et suivi commercial</span>
    </article>
    <article class="metric-card">
        <strong>Clients</strong>
        <span>Accompagnement des comptes clients</span>
    </article>
    <article class="metric-card">
        <strong>Catalogue</strong>
        <span>Consultation des produits et stocks</span>
    </article>
</section>

        </main>
    </div>
</body>
</html>
