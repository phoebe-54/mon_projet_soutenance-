<?php
$adminTitle = 'Tableau de bord';
$adminLead = "Suivez les indicateurs cles de la plateforme NOCIBE, les dernieres commandes et les livraisons en cours.";
$adminBadge = 'Vue generale';
$adminPage = 'dashboard.php';
include '_frame_start.php';
?>

<section class="admin-metric-grid">
    <article class="metric-card">
        <strong>1 247</strong>
        <span>Total des commandes</span>
    </article>
    <article class="metric-card">
        <strong>856</strong>
        <span>Clients enregistres</span>
    </article>
    <article class="metric-card">
        <strong>24</strong>
        <span>Produits en catalogue</span>
    </article>
    <article class="metric-card">
        <strong>2,45 M FCFA</strong>
        <span>Revenus confirmes</span>
    </article>
</section>

<section class="public-grid mt-3">
    <article class="dashboard-card">
        <span class="eyebrow"><i class="fa-solid fa-cart-shopping"></i> Commandes</span>
        <h3 class="panel-title mt-3">Dernieres commandes</h3>
        <p>Recapitulatif rapide des commandes recemment creees ou confirmees sur la plateforme NOCIBE.</p>
        <div class="table-shell mt-4">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Commande</th>
                        <th>Client</th>
                        <th>Produit</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="table-strong">NOC-2026-001</td>
                        <td>Jean Dupont</td>
                        <td>Ciment CPJ 35</td>
                        <td>450 000 FCFA</td>
                        <td><span class="status-pill status-confirmed">Confirmee</span></td>
                    </tr>
                    <tr>
                        <td class="table-strong">NOC-2026-002</td>
                        <td>Marie Martin</td>
                        <td>Ciment CPA 45</td>
                        <td>785 000 FCFA</td>
                        <td><span class="status-pill status-shipped">En livraison</span></td>
                    </tr>
                    <tr>
                        <td class="table-strong">NOC-2026-003</td>
                        <td>Paul Durand</td>
                        <td>Ciment blanc</td>
                        <td>320 000 FCFA</td>
                        <td><span class="status-pill status-pending">En attente</span></td>
                    </tr>
                    <tr>
                        <td class="table-strong">NOC-2026-004</td>
                        <td>Awa Mensah</td>
                        <td>Ciment CPJ 35</td>
                        <td>610 000 FCFA</td>
                        <td><span class="status-pill status-delivered">Livree</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>

    <article class="dashboard-card">
        <div class="dashboard-chart" aria-label="Graphe des performances du dashboard">
            <div class="chart-head">
                <span>Performance</span>
                <strong>+18%</strong>
            </div>
            <div class="chart-bars">
                <span style="--value: 62%" aria-label="Commandes 62%"></span>
                <span style="--value: 78%" aria-label="Clients 78%"></span>
                <span style="--value: 54%" aria-label="Catalogue 54%"></span>
                <span style="--value: 86%" aria-label="Livraisons 86%"></span>
                <span style="--value: 72%" aria-label="Revenus 72%"></span>
            </div>
            <div class="chart-legend">
                <span>Commandes</span>
                <span>Livraisons</span>
                <span>Revenus</span>
            </div>
        </div>
        <h3 class="panel-title">Tableau de bord principal</h3>
        <p>Cette vue centralise les performances commerciales, les clients, le catalogue et les flux logistiques de NOCIBE.</p>
    </article>
</section>

<section class="delivery-grid mt-3">
    <article class="delivery-card">
        <div class="delivery-meta">
            <h3>NOC-2026-002</h3>
            <span class="status-pill status-shipped">Expediee</span>
        </div>
        <p><strong>Marie Martin</strong></p>
        <p>Livraison en cours vers Cotonou, Akpakpa.</p>
        <a href="livraisons.php" class="btn btn-soft mt-2">Suivre la livraison</a>
    </article>
    <article class="delivery-card">
        <div class="delivery-meta">
            <h3>NOC-2026-005</h3>
            <span class="status-pill status-pending">En preparation</span>
        </div>
        <p><strong>Marc Bernard</strong></p>
        <p>Chargement programme pour Porto-Novo.</p>
        <a href="livraisons.php" class="btn btn-soft mt-2">Voir la livraison</a>
    </article>
    <article class="delivery-card">
        <div class="delivery-meta">
            <h3>NOC-2026-006</h3>
            <span class="status-pill status-confirmed">Confirmee</span>
        </div>
        <p><strong>Alice Dubois</strong></p>
        <p>Commande validee, affectation du camion en attente.</p>
        <a href="livraisons.php" class="btn btn-soft mt-2">Planifier</a>
    </article>
</section>

<?php include '_frame_end.php'; ?>
