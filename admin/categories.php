<?php
$adminTitle = 'Categories de ciment';
$adminLead = 'Gerez les differentes categories de ciment disponibles dans le catalogue.';
$adminBadge = 'Gestion des produits';
$adminPage = 'categories.php';
include '_frame_start.php';
?>

<section class="toolbar">
    <div class="toolbar-actions">
        <button class="btn btn-brand px-4 py-3"><i class="fa-solid fa-plus"></i> Nouvelle categorie</button>
    </div>
    <div class="toolbar-actions">
        <input type="text" class="form-control" placeholder="Rechercher une categorie..." style="min-width:280px;">
    </div>
</section>

<section class="public-grid">
    <article class="dashboard-card">
        <h3 style="font-family:'Barlow Condensed','Arial Narrow',sans-serif;font-size:2rem;text-transform:uppercase;">Categories de ciment disponibles</h3>
        <p>Toutes les categories de ciment proposees par NOCIBE avec le nombre de produits dans chaque categorie.</p>
        <div class="table-shell mt-4">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Produits</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="table-strong">1</td>
                        <td>Ciment Portland</td>
                        <td>Ciment universel pour tous types de travaux</td>
                        <td>2 produits</td>
                        <td><button class="btn btn-soft btn-sm">Modifier</button></td>
                    </tr>
                    <tr>
                        <td class="table-strong">2</td>
                        <td>Ciment blanc</td>
                        <td>Ciment blanc pour finitions aesthetiques</td>
                        <td>1 produit</td>
                        <td><button class="btn btn-soft btn-sm">Modifier</button></td>
                    </tr>
                    <tr>
                        <td class="table-strong">3</td>
                        <td>Ciment rapide</td>
                        <td>Prise rapide pour travaux urgents</td>
                        <td>1 produit</td>
                        <td><button class="btn btn-soft btn-sm">Modifier</button></td>
                    </tr>
                    <tr>
                        <td class="table-strong">4</td>
                        <td>Ciment resistant</td>
                        <td>Resistance accrue pour ouvrages d'art</td>
                        <td>1 produit</td>
                        <td><button class="btn btn-soft btn-sm">Modifier</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>

    <article class="dashboard-card">
        <img src="../assets/images/admin-hero.svg" alt="Illustration categories de ciment">
        <h3>Gestion simplifiee du catalogue</h3>
        <p>Un systeme de categorisation clair pour mieux organiser les differents types de ciment et faciliter la recherche pour les clients.</p>
    </article>
</section>

<?php include '_frame_end.php'; ?>
