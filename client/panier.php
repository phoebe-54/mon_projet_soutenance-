<?php
include '../includes/auth_check.php';
$pageTitle = 'Panier';
$basePath = '../';
include '../includes/header.php';
?>

<main class="site-container section-block">
    <section class="hero-grid">
        <article class="glass-card hero-copy">
            <span class="eyebrow"><i class="fa-solid fa-cart-shopping"></i> Panier client</span>
            <h1 class="hero-title">Un panier plus stable, plus propre et mieux aligne.</h1>
            <p>
                Verifiez vos quantites, visualisez les produits choisis et confirmez votre commande
                dans une mise en page sans decalage et plus agreable a lire.
            </p>
            <div class="cart-points">
                <div class="cart-point">
                    <i class="fa-solid fa-layer-group"></i>
                    <div>Les blocs panier et recapitulatif gardent maintenant le meme style de carte.</div>
                </div>
                <div class="cart-point">
                    <i class="fa-solid fa-images"></i>
                    <div>Chaque ligne produit affiche une illustration claire au lieu d'un visuel fade.</div>
                </div>
            </div>
        </article>
        <article class="glass-card hero-visual">
            <img src="../assets/images/delivery-hero.svg" alt="Illustration panier et livraison">
            <div class="hero-note">
                <strong>Panier sans decalage</strong>
                L'organisation des colonnes, des images et du recapitulatif a ete renforcee pour rester propre sur grand et petit ecran.
            </div>
        </article>
    </section>

    <section class="cart-layout">
        <article class="cart-card">
            <div class="cart-item">
                <img src="../assets/images/product-cement.svg" alt="Ciment Portland">
                <div>
                    <h5>Ciment Portland 50kg</h5>
                    <p>Prix unitaire : 15 000 FCFA</p>
                    <div class="qty-controls">
                        <button>-</button>
                        <span>2</span>
                        <button>+</button>
                    </div>
                </div>
                <div class="text-end">
                    <div class="table-strong">30 000 FCFA</div>
                    <button class="btn btn-soft btn-sm mt-2">Retirer</button>
                </div>
            </div>

            <div class="cart-item">
                <img src="../assets/images/product-sand.svg" alt="Sable fin">
                <div>
                    <h5>Sable fin 1m3</h5>
                    <p>Prix unitaire : 25 000 FCFA</p>
                    <div class="qty-controls">
                        <button>-</button>
                        <span>1</span>
                        <button>+</button>
                    </div>
                </div>
                <div class="text-end">
                    <div class="table-strong">25 000 FCFA</div>
                    <button class="btn btn-soft btn-sm mt-2">Retirer</button>
                </div>
            </div>

            <div class="cart-item">
                <img src="../assets/images/product-concrete.svg" alt="Beton pret">
                <div>
                    <h5>Beton pret a l'emploi 1m3</h5>
                    <p>Prix unitaire : 85 000 FCFA</p>
                    <div class="qty-controls">
                        <button>-</button>
                        <span>1</span>
                        <button>+</button>
                    </div>
                </div>
                <div class="text-end">
                    <div class="table-strong">85 000 FCFA</div>
                    <button class="btn btn-soft btn-sm mt-2">Retirer</button>
                </div>
            </div>
        </article>

        <aside class="order-summary-card">
            <h4>Recapitulatif</h4>
            <div class="summary-line"><span>Sous-total</span><span>140 000 FCFA</span></div>
            <div class="summary-line"><span>Livraison</span><span>A calculer</span></div>
            <div class="summary-total"><span>Total</span><span>140 000 FCFA</span></div>
            <a href="commande.php" class="btn btn-brand w-100 py-3 mt-3">
                <i class="fa-solid fa-credit-card"></i>
                Confirmer la commande
            </a>
            <a href="catalogue.php" class="btn btn-soft w-100 py-3 mt-2">
                <i class="fa-solid fa-arrow-left"></i>
                Continuer les achats
            </a>
        </aside>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
