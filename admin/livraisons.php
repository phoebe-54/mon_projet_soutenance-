<?php
$adminTitle = 'Livraisons';
$adminLead = 'Surveillez les expeditions dans des cartes plus propres et plus faciles a parcourir.';
$adminBadge = 'Suivi logistique';
$adminPage = 'livraisons.php';
include '_frame_start.php';
?>

<section class="filter-tabs mb-3">
    <span class="tab active">Toutes</span>
    <span class="tab">En attente</span>
    <span class="tab">En preparation</span>
    <span class="tab">Expediees</span>
    <span class="tab">Livrees</span>
</section>

<section class="delivery-grid">
    <article class="delivery-card">
        <div class="delivery-meta">
            <h3>#1001</h3>
            <span class="status-pill status-pending">En attente</span>
        </div>
        <p><strong>Jean Dupont</strong></p>
        <p>123 Rue de la Paix, Dakar</p>
        <p>Livraison prevue le 25/04/2025</p>
        <button class="btn btn-soft mt-2">Mettre a jour</button>
    </article>
    <article class="delivery-card">
        <div class="delivery-meta">
            <h3>#1002</h3>
            <span class="status-pill status-shipped">Expediee</span>
        </div>
        <p><strong>Marie Martin</strong></p>
        <p>456 Avenue Senghor, Dakar</p>
        <p>Livraison prevue le 24/04/2025</p>
        <button class="btn btn-soft mt-2">Mettre a jour</button>
    </article>
    <article class="delivery-card">
        <div class="delivery-meta">
            <h3>#1003</h3>
            <span class="status-pill status-delivered">Livree</span>
        </div>
        <p><strong>Paul Durand</strong></p>
        <p>789 Boulevard Republique, Dakar</p>
        <p>Livree le 23/04/2025</p>
        <button class="btn btn-soft mt-2">Historique</button>
    </article>
    <article class="delivery-card">
        <div class="delivery-meta">
            <h3>#1005</h3>
            <span class="status-pill status-confirmed">En preparation</span>
        </div>
        <p><strong>Marc Bernard</strong></p>
        <p>321 Rue Kermel, Dakar</p>
        <p>Livraison prevue le 26/04/2025</p>
        <button class="btn btn-soft mt-2">Mettre a jour</button>
    </article>
    <article class="delivery-card">
        <div class="delivery-meta">
            <h3>#1006</h3>
            <span class="status-pill status-pending">En attente</span>
        </div>
        <p><strong>Alice Dubois</strong></p>
        <p>654 Avenue Pompiers, Dakar</p>
        <p>Livraison prevue le 27/04/2025</p>
        <button class="btn btn-soft mt-2">Mettre a jour</button>
    </article>
    <article class="delivery-card">
        <div class="delivery-meta">
            <h3>#1007</h3>
            <span class="status-pill status-shipped">Expediee</span>
        </div>
        <p><strong>Thomas Moreau</strong></p>
        <p>987 Rue Felix Faure, Dakar</p>
        <p>Livraison prevue le 25/04/2025</p>
        <button class="btn btn-soft mt-2">Mettre a jour</button>
    </article>
</section>

<?php include '_frame_end.php'; ?>
