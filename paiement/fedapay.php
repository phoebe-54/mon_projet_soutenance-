<?php
$pageTitle = 'Paiement';
$basePath = '../';
include '../includes/header.php';
?>

<main class="site-container section-block">
    <div class="section-header">
        <div>
            <h2 class="section-title">Paiement FedaPay</h2>
            <p class="section-subtitle">La derniere etape du parcours suit maintenant le meme design propre que le reste du frontend.</p>
        </div>
    </div>

    <section class="checkout-layout">
        <article class="checkout-card">
            <span class="eyebrow"><i class="fa-solid fa-credit-card"></i> Paiement</span>
            <h3 style="font-family:'Barlow Condensed','Arial Narrow',sans-serif;font-size:2rem;margin-top:12px;text-transform:uppercase;">Choisissez votre mode de paiement</h3>
            <p>Le bloc paiement est plus clair, avec des options mieux distinguees et une mise en page plus rassurante.</p>

            <div class="payment-option active">
                <div>
                    <strong>FedaPay Mobile Money</strong>
                    <div class="helper-text">Reglement securise via mobile money</div>
                </div>
                <input type="radio" checked>
            </div>
            <div class="payment-option">
                <div>
                    <strong>Carte bancaire</strong>
                    <div class="helper-text">Visa, MasterCard et cartes compatibles</div>
                </div>
                <input type="radio">
            </div>
            <div class="payment-option">
                <div>
                    <strong>Paiement en agence</strong>
                    <div class="helper-text">Validation manuelle pour les commandes entreprise</div>
                </div>
                <input type="radio">
            </div>
        </article>

        <aside class="checkout-card">
            <img src="../assets/images/payment-nocibe.svg" alt="Illustration paiement NOCIBE">
            <h3>Resume de la commande</h3>
            <div class="summary-line"><span>Sous-total</span><span>140 000 FCFA</span></div>
            <div class="summary-line"><span>Livraison</span><span>15 000 FCFA</span></div>
            <div class="summary-total"><span>Total</span><span>155 000 FCFA</span></div>
            <a href="../client/suivi.php" class="btn btn-brand w-100 py-3 mt-3">
                <i class="fa-solid fa-check"></i>
                Payer et confirmer
            </a>
            <a href="../client/commande.php" class="btn btn-soft w-100 py-3 mt-2">
                <i class="fa-solid fa-arrow-left"></i>
                Retour a la commande
            </a>
        </aside>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
