<?php
$adminBadge = 'Gestion des produits';
$adminPage = 'produits.php';
include '_frame_start.php';
?>

<section class="toolbar">
    <div class="toolbar-actions">
        <button class="btn btn-brand px-4 py-3"><i class="fa-solid fa-plus"></i> Ajouter un produit</button>
        <button class="btn btn-soft px-4 py-3"><i class="fa-solid fa-filter"></i> Filtrer</button>
    </div>
    <div class="toolbar-actions">
        <input type="text" class="form-control" placeholder="Rechercher un produit..." style="min-width:280px;">
    </div>
</section>

<section class="product-admin-layout">
    <article class="dashboard-card">
        <span class="eyebrow"><i class="fa-solid fa-cubes-stacked"></i> Catalogue NOCIBE</span>
        <h3 class="panel-title mt-3">Gestion des produits</h3>
        <p>Chaque produit affiche son nom, sa categorie, son prix unitaire et son niveau de stock avec une indication visuelle.</p>

        <div class="table-shell mt-4">
            <table class="table-modern product-management-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Categorie</th>
                        <th>Prix unitaire</th>
                        <th>Stock</th>
                        <th>Indication</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="table-product">
                                <img class="table-thumb" src="../assets/images/Copilot_20260506_180438.png" alt="Ciment Portland 50kg" onerror="this.onerror=null;this.src='../assets/images/product-cement.svg';">
                                <div>
                                    <div class="table-strong">Ciment Portland 50kg</div>
                                    <div class="helper-text">Sac standard pour chantiers</div>
                                </div>
                            </div>
                        </td>
                        <td>Portland</td>
                        <td>15 000 FCFA</td>
                        <td>
                            <div class="stock-meter"><span style="width: 82%"></span></div>
                            <strong>150 sacs</strong>
                        </td>
                        <td><span class="stock-pill stock-ok"><i class="fa-solid fa-circle-check"></i> Stock suffisant</span></td>
                        <td>
                            <div class="row-actions">
                                <button class="icon-btn" type="button" aria-label="Modifier Ciment Portland 50kg"><i class="fa-solid fa-pen"></i></button>
                                <button class="icon-btn icon-btn-danger" type="button" aria-label="Supprimer Ciment Portland 50kg"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="table-product">
                                <img class="table-thumb" src="../assets/images/Copilot_20260506_180528.png" alt="Ciment Portland 25kg" onerror="this.onerror=null;this.src='../assets/images/product-sand.svg';">
                                <div>
                                    <div class="table-strong">Ciment Portland 25kg</div>
                                    <div class="helper-text">Format compact et leger</div>
                                </div>
                            </div>
                        </td>
                        <td>Portland</td>
                        <td>8 000 FCFA</td>
                        <td>
                            <div class="stock-meter"><span style="width: 90%"></span></div>
                            <strong>200 sacs</strong>
                        </td>
                        <td><span class="stock-pill stock-ok"><i class="fa-solid fa-circle-check"></i> Stock suffisant</span></td>
                        <td>
                            <div class="row-actions">
                                <button class="icon-btn" type="button" aria-label="Modifier Ciment Portland 25kg"><i class="fa-solid fa-pen"></i></button>
                                <button class="icon-btn icon-btn-danger" type="button" aria-label="Supprimer Ciment Portland 25kg"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="table-product">
                                <img class="table-thumb" src="../assets/images/Copilot_20260504_144151.png" alt="Ciment blanc 25kg" onerror="this.onerror=null;this.src='../assets/images/product-gravel.svg';">
                                <div>
                                    <div class="table-strong">Ciment blanc 25kg</div>
                                    <div class="helper-text">Premium pour finitions</div>
                                </div>
                            </div>
                        </td>
                        <td>Ciment blanc</td>
                        <td>18 500 FCFA</td>
                        <td>
                            <div class="stock-meter stock-meter-low"><span style="width: 32%"></span></div>
                            <strong>24 sacs</strong>
                        </td>
                        <td><span class="stock-pill stock-low"><i class="fa-solid fa-triangle-exclamation"></i> Stock bas</span></td>
                        <td>
                            <div class="row-actions">
                                <button class="icon-btn" type="button" aria-label="Modifier Ciment blanc 25kg"><i class="fa-solid fa-pen"></i></button>
                                <button class="icon-btn icon-btn-danger" type="button" aria-label="Supprimer Ciment blanc 25kg"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="table-product">
                                <img class="table-thumb" src="../assets/images/Copilot_20260504_144157.png" alt="Ciment rapide 25kg" onerror="this.onerror=null;this.src='../assets/images/product-cement.svg';">
                                <div>
                                    <div class="table-strong">Ciment rapide 25kg</div>
                                    <div class="helper-text">Prise rapide pour travaux urgents</div>
                                </div>
                            </div>
                        </td>
                        <td>Rapide</td>
                        <td>16 500 FCFA</td>
                        <td>
                            <div class="stock-meter stock-meter-out"><span style="width: 8%"></span></div>
                            <strong>0 sac</strong>
                        </td>
                        <td><span class="stock-pill stock-out"><i class="fa-solid fa-circle-xmark"></i> Rupture</span></td>
                        <td>
                            <div class="row-actions">
                                <button class="icon-btn" type="button" aria-label="Modifier Ciment rapide 25kg"><i class="fa-solid fa-pen"></i></button>
                                <button class="icon-btn icon-btn-danger" type="button" aria-label="Supprimer Ciment rapide 25kg"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>

    <article class="dashboard-card product-form-card">
        <span class="eyebrow"><i class="fa-solid fa-box-open"></i> Nouveau produit</span>
        <h3 class="panel-title mt-3">Ajouter ou modifier</h3>
        <form>
            <label class="form-label" for="productName">Nom du produit</label>
            <input id="productName" class="form-control" type="text" placeholder="Ex: Ciment Portland 50kg">

            <label class="form-label" for="productCategory">Categorie</label>
            <select id="productCategory" class="form-select">
                <option>Portland</option>
                <option>Ciment blanc</option>
                <option>Rapide</option>
                <option>Resistant</option>
            </select>

            <label class="form-label" for="productPrice">Prix unitaire</label>
            <input id="productPrice" class="form-control" type="text" placeholder="Ex: 15 000 FCFA">

            <label class="form-label" for="productStock">Niveau de stock</label>
            <input id="productStock" class="form-control" type="number" placeholder="Ex: 150">

            <div class="form-actions">
                <button class="btn btn-brand w-100" type="button"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
                <button class="btn btn-soft w-100" type="reset"><i class="fa-solid fa-rotate-left"></i> Reinitialiser</button>
            </div>
        </form>
    </article>
</section>

<?php include '_frame_end.php'; ?>
