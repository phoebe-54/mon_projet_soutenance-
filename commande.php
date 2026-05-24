<?php
require_once '../../includes/auth_check.php';
requireRole('client');
require_once '../../config/database.php';

$pageTitle = "Commande";
$pageLead = "Finalisez votre commande NOCIBE en quelques clics.";

include '../includes/header.php';

/* =========================
   PANIER SESSION
========================= */
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$panier = $_SESSION['panier'];

/* PRODUITS DEMO */
$produits = [
    1 => ['nom' => 'Ciment Portland CEM II', 'prix' => 5800],
    2 => ['nom' => 'Ciment Gris 32.5R', 'prix' => 4900],
    3 => ['nom' => 'Ciment Premium 52.5N', 'prix' => 7200],
];

$total = 0;

/* =========================
   VALIDATION COMMANDE
========================= */
if (isset($_POST['valider'])) {

    // ici tu peux enregistrer en base
    $_SESSION['panier'] = [];

    $success = "Commande validée avec succès !";
}
?>

<style>

/* =========================
   PAGE
========================= */
.commande-live {
    padding-top: 14px;
}

/* =========================
   HERO
========================= */
.commande-hero {
    display: grid;
    grid-template-columns: minmax(0,1.05fr) minmax(0,.95fr);
    gap: 22px;
}

.commande-box {
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(226,232,240,.95);
    border-radius: 22px;
    padding: 28px;
    box-shadow: var(--shadow-md);
}

.commande-badge {
    display: inline-flex;
    padding: 10px 16px;
    border-radius: 999px;
    background: rgba(14,165,233,.12);
    color: #0ea5e9;
    font-weight: 800;
}

/* =========================
   TITRE
========================= */
.commande-box h2 {
    margin: 18px 0 10px;
    font-family: "Barlow Condensed", sans-serif;
    font-size: 2.2rem;
    color: #0ea5e9;
}

/* =========================
   FORM
========================= */
.form-group {
    margin-bottom: 14px;
}

label {
    font-weight: 700;
    display: block;
    margin-bottom: 6px;
}

input, textarea {
    width: 100%;
    padding: 12px;

    border-radius: 12px;

    border: 1px solid rgba(148,163,184,.4);

    outline: none;
}

/* =========================
   SUMMARY
========================= */
.summary {
    margin-top: 18px;
}

.item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    color: var(--text-soft);
}

.total {
    font-weight: 900;
    font-size: 1.2rem;
    margin-top: 10px;
    color: #0ea5e9;
}

/* =========================
   BUTTONS
========================= */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    padding: 14px 18px;

    border-radius: 14px;

    text-decoration: none;

    font-weight: 700;

    border: none;

    cursor: pointer;

    transition: .2s ease;
}

.btn-primary {
    background: #0ea5e9;
    color: #fff;
}

.btn-primary:hover {
    background: #0284c7;
}

.btn-secondary {
    background: #fff;
    border: 1px solid rgba(148,163,184,.3);
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 900px){
    .commande-hero {
        grid-template-columns: 1fr;
    }
}

</style>

<section class="commande-live">

<div class="commande-hero">

    <!-- FORMULAIRE -->
    <div class="commande-box">

        <span class="commande-badge">
            FINALISATION
        </span>

        <h2>Commande NOCIBE</h2>

        <?php if (!empty($success)): ?>
            <p style="color:green;font-weight:700;">
                <?= $success ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Nom complet</label>
                <input type="text" required>
            </div>

            <div class="form-group">
                <label>Adresse de livraison</label>
                <textarea rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" required>
            </div>

            <a href="paiement.php" class="btn btn-primary">
                Valider la commande
            </a>

            <a href="panier.php" class="btn btn-secondary">
                Retour panier
            </a>

        </form>

    </div>

    <!-- RESUME -->
    <div class="commande-box">

        <span class="commande-badge">
            RÉCAPITULATIF
        </span>

        <h2>Votre commande</h2>

        <div class="summary">

            <?php foreach ($panier as $id => $qty): ?>

                <?php
                    $p = $produits[$id];
                    $sub = $p['prix'] * $qty;
                    $total += $sub;
                ?>

                <div class="item">
                    <span><?= $p['nom'] ?> x<?= $qty ?></span>
                    <span><?= number_format($sub,0,',',' ') ?> FCFA</span>
                </div>

            <?php endforeach; ?>

            <div class="total">
                Total: <?= number_format($total,0,',',' ') ?> FCFA
            </div>

        </div>

    </div>

</div>

</section>

</body>
</html>