<?php
include '../includes/auth_check.php';
$pageTitle = 'Catalogue';
$basePath = '../';
include '../includes/header.php';
?>

<main class="site-container">
    <section class="hero-grid">
        <article class="glass-card hero-copy">
            <span class="eyebrow"><i class="fa-solid fa-cubes-stacked"></i> Notre collection de ciment</span>
            <h1 class="hero-title">Le meilleur ciment pour tous vos projets de construction</h1>
            <p>
                Decouvrez notre large gamme de ciments de qualite superieure. Tous nos produits sont certifies et livres avec traçabilite. 
                Selection parfaite pour vos besoins, du Portland au ciment rapide, du ciment blanc au ciment resistant.
            </p>
            <div class="hero-actions">
                <a href="panier.php" class="btn btn-brand px-4 py-3"><i class="fa-solid fa-cart-shopping"></i> Voir le panier</a>
                <a href="commande.php" class="btn btn-soft px-4 py-3"><i class="fa-solid fa-credit-card"></i> Finaliser commande</a>
            </div>
            <div class="catalog-points">
                <div class="catalog-point">
                    <i class="fa-solid fa-certificate"></i>
                    <div>Tous nos ciments sont certifies conformes aux normes internationales.</div>
                </div>
                <div class="catalog-point">
                    <i class="fa-solid fa-truck-fast"></i>
                    <div>Livraison rapide et securisee sur tout le territoire.</div>
                </div>
            </div>
        </article>
        <article class="glass-card hero-visual">
            <img src="../assets/images/catalogue-hero.svg" alt="Illustration du catalogue de ciment">
            <div class="hero-note">
                <strong>Qualite garantie</strong>
                Tous les produits sont controles et conformes aux certifications exigees.
            </div>
        </article>
    </section>

    <section class="catalog-layout section-block">
        <aside class="filter-card panel-card">
            <div class="filter-group">
                <h4>Types de ciment</h4>
                <label class="filter-option"><input type="checkbox" checked> Ciment Portland</label>
                <label class="filter-option"><input type="checkbox" checked> Ciment blanc</label>
                <label class="filter-option"><input type="checkbox" checked> Ciment rapide</label>
                <label class="filter-option"><input type="checkbox" checked> Ciment resistant</label>
            </div>
            <div class="filter-group">
                <h4>Conditionnement</h4>
                <label class="filter-option"><input type="checkbox" checked> Sacs 25kg</label>
                <label class="filter-option"><input type="checkbox" checked> Sacs 50kg</label>
                <label class="filter-option"><input type="checkbox" checked> Palette 50 sacs</label>
            </div>
            <div class="filter-group">
                <h4>Prix maximum</h4>
                <input type="range" class="form-range" min="0" max="200000" value="120000">
                <div class="range-value">Jusqu'a 120 000 FCFA</div> 50kg">
                <div class="product-card-body">
                    <span class="product-tag"><i class="fa-solid fa-tags"></i> Portland</span>
                    <h3>Ciment Portland 50kg</h3>
                    <p>Ciment universel pour tous travaux de construction. Resistance optimale et prise normale. Ideal pour les projets generaux.</p>
                    <div class="product-meta">
                        <span class="product-price">15 000 FCFA</span>
                        <span class="stock-pill stock-ok">En stock</span>
                    </div>
                    <button class="btn btn-brand w-100 py-3"><i class="fa-solid fa-cart-plus"></i> Ajouter au panier</button>
                </div>
            </article>

            <article class="product-card">
                <img src="../assets/images/product-sand.svg" alt="Ciment blanc 25kg">
                <div class="product-card-body">
                    <span class="product-tag"><i class="fa-solid fa-tags"></i> Blanc</span>
                    <h3>Ciment blanc 25kg</h3>
                    <p>Ciment blanc premium pour finitions aesthetiques. Ideal pour travaux visibles et carrelage decoratif. Tres haute qualite.</p>
                    <div class="product-meta">
                        <span class="product-price">18 500 FCFA</span>
                        <span class="stock-pill stock-ok">En stock</span>
                    </div>
                    <button class="btn btn-brand w-100 py-3"><i class="fa-solid fa-cart-plus"></i> Ajouter au panier</button>
                </div>
            </article>

            <article class="product-card">
                <img src="../assets/images/product-gravel.svg" alt="Ciment rapide 25kg">
                <div class="product-card-body">
                    <span class="product-tag"><i class="fa-solid fa-tags"></i> Rapide</span>
                    <h3>Ciment rapide 25kg</h3>
                    <p>Prise rapide pour travaux urgents. Ideal pour reparations et chantiers necessitant une acceleration du calendrier.</p>
                    <div class="product-meta">
                        <span class="product-price">16 500 FCFA</span>
                        <span class="stock-pill stock-low">Stock faible</span>
                    </div>
                    <button class="btn btn-brand w-100 py-3"><i class="fa-solid fa-cart-plus"></i> Ajouter au panier</button>
                </div>
            </article>

            <article class="product-card">
                <img src="../assets/images/product-concrete.svg" alt="Ciment resistant 50kg">
                <div class="product-card-body">
                    <span class="product-tag"><i class="fa-solid fa-tags"></i> Resistant</span>
                    <h3>Ciment resistant 50kg</h3>
                    <p>Resistance accrue pour ouvrages d'art et structures speciales. Ideal pour environnements agressifs et charges lourdes.</p>
                    <div class="product-meta">
                        <span class="product-price">17 000 FCFA</span>
                        <span class="stock-pill stock-ok">En stock</span>
                    </div>
                    <button class="btn btn-brand w-100 py-3"><i class="fa-solid fa-cart-plus"></i> Ajouter au panier</button>
                </div>
            </article>

            <article class="product-card">
                <img src="../assets/images/product-cement.svg" alt="Ciment Portland 25kg">
                <div class="product-card-body">
                    <span class="product-tag"><i class="fa-solid fa-tags"></i> Portland</span>
                    <h3>Ciment Portland 25kg</h3>
                    <p>Format compact du ciment Portland. Parfait pour petits chantiers et travaux domestiques. Meme qualite que le 50kg.</p>
                    <div class="product-meta">
                        <span class="product-price">8 000 FCFA</span>
                        <span class="stock-pill stock-ok">En stock</span>
                    </div>
                    <button class="btn btn-brand w-100 py-3"><i class="fa-solid fa-cart-plus"></i> Ajouter au panier</button>
                </div>
            </article>

            <article class="product-card">
                <img src="../assets/images/product-sand.svg" alt="Ciment Portland - Palette">
                <div class="product-card-body">
                    <span class="product-tag"><i class="fa-solid fa-tags"></i> Gros volume</span>
                    <h3>Ciment Portland - Palette 50 sacs</h3>
                    <p>Palette complete de 50 sacs de 50kg. Tarif degressif pour gros volume. Livraison incluse pour les palettes.</p>
                    <div class="product-meta">
                        <span class="product-price">700crete.svg" alt="Beton pret">
                <div class="product-card-body">
                    <span class="product-tag"><i class="fa-solid fa-tags"></i> Beton</span>
                    <h3>Beton pret a l'emploi 1m3</h3>
                    <p>Produit pret a l'emploi affiche avec les memes codes de couleur que le panier et la commande.</p>
                    <div class="product-meta">
                        <span class="product-price">85 000 FCFA</span>
                        <span class="stock-pill stock-ok">En stock</span>
                    </div>
                    <button class="btn btn-brand w-100 py-3"><i class="fa-solid fa-cart-plus"></i> Ajouter au panier</button>
                </div>
            </article>
        </section>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
