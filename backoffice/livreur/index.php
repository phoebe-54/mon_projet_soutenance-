<?php
require_once '../../includes/auth_check.php';
requireRole('livreur');
require_once '../../config/database.php';

$dashboardTitle = 'Dashboard livreur';
$dashboardLead = 'Consultez les livraisons affectees et leur statut.';
include '../includes/header.php';
?>

<section class="admin-metric-grid">
    <article class="metric-card">
        <strong>Livraisons</strong>
        <span>Liste des livraisons a effectuer</span>
    </article>
    <article class="metric-card">
        <strong>Itineraires</strong>
        <span>Informations de destination client</span>
    </article>
    <article class="metric-card">
        <strong>Statuts</strong>
        <span>Mise a jour de l'avancement</span>
    </article>
</section>

        </main>
    </div>
</body>
</html>
