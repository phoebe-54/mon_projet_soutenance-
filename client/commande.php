<?php
include '../includes/auth_check.php';
$pageTitle = 'Commande';
$basePath = '../';
include '../includes/header.php';
?>

<main class="site-container section-block">
    <div class="stepper">
        <div class="step done"><strong>1</strong><div>Panier</div></div>
        <div class="step active"><strong>2</strong><div>Livraison</div></div>
        <div class="step"><strong>3</strong><div>Paiement</div></div>
        <div class="step"><strong>4</strong><div>Confirmation</div></div>
    </div>

    <section class="checkout-layout">
        <article class="checkout-card">
            <span class="eyebrow"><i class="fa-solid fa-location-dot"></i> Livraison</span>
            <h3 style="font-family:'Barlow Condensed','Arial Narrow',sans-serif;font-size:2rem;margin-top:12px;text-transform:uppercase;">Adresse de livraison</h3>
            <p>Le parcours de commande suit maintenant une structure plus propre avec des etapes plus lisibles.</p>

            <form class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Quartier</label>
                    <input type="text" class="form-control" placeholder="Ex: Plateau">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ville</label>
                    <select class="form-select">
                        <option>Dakar</option>
                        <option>Pikine</option>
                        <option>Keur Massar</option>
                        <option>Thies</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Adresse complete</label>
                    <textarea class="form-control" rows="4" placeholder="Numero, rue, repere..."></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Instructions de livraison</label>
                    <textarea class="form-control" rows="3" placeholder="Informations utiles pour la remise..."></textarea>
                </div>
            </form>
        </article>

        <aside class="checkout-card">
            <img src="../assets/images/delivery-hero.svg" alt="Illustration livraison">
            <h3>Recapitulatif commande</h3>
            <div class="summary-line"><span>Sous-total</span><span>140 000 FCFA</span></div>
            <div class="summary-line"><span>Livraison</span><span>15 000 FCFA</span></div>
            <div class="summary-total"><span>Total</span><span>155 000 FCFA</span></div>
            <a href="../paiement/fedapay.php" class="btn btn-brand w-100 py-3 mt-3">
                <i class="fa-solid fa-arrow-right"></i>
                Continuer vers le paiement
            </a>
            <a href="panier.php" class="btn btn-soft w-100 py-3 mt-2">
                <i class="fa-solid fa-arrow-left"></i>
                Retour au panier
            </a>
        </aside>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
