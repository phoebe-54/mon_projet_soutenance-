<?php
require_once __DIR__ . '/includes/auth_check.php';
requireRole('client');

$pageTitle = "Mon Panier";
$pageLead = "Vérifiez vos produits sélectionnés avant commande.";

include __DIR__ . '/backoffice/includes/header.php';

/* PANIER SESSION */
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$panier = $_SESSION['panier'];

/* PRODUITS FIXES (démo) */
$produits = [
    1 => ['nom' => 'Ciment Portland CEM II', 'prix' => 5800, 'image' => 'assets/images/product-cement.svg'],
    2 => ['nom' => 'Ciment Gris 32.5R', 'prix' => 4900, 'image' => 'assets/images/product-concrete.svg'],
    3 => ['nom' => 'Ciment Premium 52.5N', 'prix' => 7200, 'image' => 'assets/images/product-gravel.svg'],
];

$total = 0;
?>

<style>

/* =========================
   GLOBAL
========================= */

.cart-live {
    padding-top: 14px;
}

/* =========================
   HERO
========================= */

.cart-hero {
    display: grid;
    grid-template-columns: minmax(0,1.05fr) minmax(0,.95fr);
    gap: 22px;
    margin-top: 10px;
}

.cart-hero-copy {
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(226,232,240,.95);
    border-radius: 22px;
    padding: 28px;
    box-shadow: var(--shadow-md);
}

.cart-badge {
    display: inline-flex;
    padding: 10px 16px;
    border-radius: 999px;
    background: rgba(7,95,199,.12);
    color: #075fc7;
    font-size: .82rem;
    font-weight: 800;
}

.cart-hero-copy h2 {
    margin: 18px 0 12px;
    font-family: "Barlow Condensed", sans-serif;
    font-weight: 800;
    color: #075fc7;
    font-size: clamp(2rem,4vw,2.6rem);
    line-height: 1;
}

.cart-hero-copy p {
    margin: 0;
    color: var(--text-soft);
    line-height: 1.8;
}

.cart-actions {
    display: flex;
    gap: 12px;
    margin-top: 22px;
}

.cart-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border-radius: 14px;
    font-weight: 700;
    text-decoration: none;
}

.cart-btn-back {
    background: #fff;
    border: 1px solid rgba(148,163,184,.25);
    color: #0f172a;
}

.cart-btn-back:hover {
    background: #f8fafc;
}

.cart-btn-primary {
    background: #075fc7;
    color: #fff;
}

.cart-btn-primary:hover {
    background: #0284c7;
}

/* =========================
   VISUAL
========================= */

.cart-hero-visual {
    position: relative;
    overflow: hidden;
    border-radius: 22px;
    min-height: 320px;
    background: #fff;
    border: 1px solid rgba(226,232,240,.95);
}

.cart-hero-visual img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* =========================
   GRID PANIER
========================= */

.cart-products {
    display: grid;
    grid-template-columns: repeat(3,minmax(0,1fr));
    gap: 18px;
    margin-top: 24px;
}

/* =========================
   CARD
========================= */

.cart-card {
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(226,232,240,.95);
    border-radius: 22px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: .2s ease;
}

.cart-card:hover {
    transform: translateY(-3px);
    border-color: rgba(7,95,199,.35);
}

.cart-card-image {
    height: 200px;
    background: #f8fafc;
}

.cart-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-card-body {
    padding: 18px;
}

.cart-card h3 {
    margin: 10px 0;
    font-weight: 800;
}

.cart-price {
    color: #075fc7;
    font-weight: 900;
    font-size: 1.1rem;
}

/* =========================
   QTY
========================= */

.qty-controls {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
}

.qty-controls button {
    width: 30px;
    height: 30px;
    border: none;
    border-radius: 8px;
    background: #075fc7;
    color: #fff;
    font-weight: 900;
    cursor: pointer;
}

.qty-controls button:hover {
    background: #0284c7;
}

.qty-controls span {
    font-weight: 700;
}

/* =========================
   REMOVE
========================= */

.remove-btn {
    margin-top: 10px;
    background: #ef4444;
    color: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 10px;
    cursor: pointer;
}

.remove-btn:hover {
    background: #dc2626;
}

/* =========================
   RESPONSIVE
========================= */

@media (max-width: 992px){
    .cart-hero {
        grid-template-columns: 1fr;
    }

    .cart-products {
        grid-template-columns: 1fr;
    }
}

</style>

<section class="cart-live">

<!-- HERO -->
<div class="cart-hero">

    <div class="cart-hero-copy">

        <span class="cart-badge">
            PANIER NOCIBE
        </span>

        <h2>
            Votre Panier
        </h2>

        <p>
            Vérifiez vos produits, ajustez les quantités
            et finalisez votre commande en toute simplicité.
        </p>

        <div class="cart-actions">

            <a href="catalogue.php" class="cart-btn cart-btn-back">
                ← Continuer achats
            </a>

            <a href="commande.php" class="cart-btn cart-btn-primary">
                Valider commande
            </a>

        </div>

    </div>

    <div class="cart-hero-visual">
        <img src="assets/images/accueil.png" alt="Panier NOCIBE">
    </div>

</div>

<!-- PRODUITS -->
<div class="cart-products">

<?php if (empty($panier)): ?>

    <div class="cart-card">
        <div class="cart-card-body">
            Votre panier est vide.
        </div>
    </div>

<?php else: ?>

    <?php foreach ($panier as $id => $qty): ?>

        <?php
            $p = $produits[$id];
            $subtotal = $p['prix'] * $qty;
            $total += $subtotal;
        ?>

        <div class="cart-card">

            <div class="cart-card-image">
                <img src="<?= $p['image'] ?>" alt="">
            </div>

            <div class="cart-card-body">

                <h3><?= $p['nom'] ?></h3>

                <div class="cart-price">
                    <?= number_format($p['prix'],0,',',' ') ?> FCFA
                </div>

                <div class="qty-controls">

                    <button onclick="location.href='panier.php?dec=<?= $id ?>'">-</button>

                    <span><?= $qty ?></span>

                    <button onclick="location.href='panier.php?inc=<?= $id ?>'">+</button>

                </div>

                <div class="cart-price" style="margin-top:8px;">
                    Total: <?= number_format($subtotal,0,',',' ') ?> FCFA
                </div>

                <button class="remove-btn"
                        onclick="location.href='panier.php?remove=<?= $id ?>'">
                    Supprimer
                </button>

            </div>

        </div>

    <?php endforeach; ?>

<?php endif; ?>

</div>

</section>

</body>
</html>
