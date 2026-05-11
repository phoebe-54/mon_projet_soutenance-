<?php
$adminTitle = 'Commandes';
$adminLead = 'Suivez les commandes dans un tableau plus lisible avec des statuts mieux distingues.';
$adminBadge = 'Flux commandes';
$adminPage = 'commandes.php';
include '_frame_start.php';
?>

<section class="filter-tabs mb-3">
    <span class="tab active">Toutes</span>
    <span class="tab">En attente</span>
    <span class="tab">Confirmees</span>
    <span class="tab">Livrees</span>
    <span class="tab">Annulees</span>
</section>

<section class="dashboard-card">
    <div class="toolbar">
        <div>
            <h3 class="panel-title">Liste des commandes</h3>
            <p class="panel-copy">Chaque ligne garde les informations essentielles sans surcharger visuellement l'ecran.</p>
        </div>
        <div class="toolbar-actions">
            <button class="btn btn-brand px-4 py-3"><i class="fa-solid fa-plus"></i> Creer une commande</button>
        </div>
    </div>
    <div class="table-shell">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Produits</th>
                    <th>Montant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="table-strong">1001</td>
                    <td>23/04/2025</td>
                    <td>Jean Dupont</td>
                    <td>Ciment x2, Sable x1</td>
                    <td>55 000 FCFA</td>
                    <td><span class="status-pill status-pending">En attente</span></td>
                </tr>
                <tr>
                    <td class="table-strong">1002</td>
                    <td>22/04/2025</td>
                    <td>Marie Martin</td>
                    <td>Beton pret x1</td>
                    <td>85 000 FCFA</td>
                    <td><span class="status-pill status-confirmed">Confirmee</span></td>
                </tr>
                <tr>
                    <td class="table-strong">1003</td>
                    <td>21/04/2025</td>
                    <td>Paul Durand</td>
                    <td>Graviers x2, Sable x1</td>
                    <td>95 000 FCFA</td>
                    <td><span class="status-pill status-delivered">Livree</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<?php include '_frame_end.php'; ?>
