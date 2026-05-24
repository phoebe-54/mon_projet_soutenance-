<?php
require_once '../../includes/auth_check.php';
requireRole('client');

$catalogueTitle = 'Catalogue';
$catalogueLead = 'Découvrez les différents produits NOCIBE disponibles.';

$currentUser = currentUser();

include '../includes/header.php';
?>

<style>

    .catalogue-live{
        padding-top:14px;
    }

    /*
    |--------------------------------------------------------------------------
    | HERO
    |--------------------------------------------------------------------------
    */

    .catalogue-hero{
        display:grid;
        grid-template-columns:minmax(0,1.05fr) minmax(0,.95fr);

        gap:22px;

        align-items:stretch;

        margin-top:10px;
    }

    .catalogue-hero-copy{
        background:rgba(255,255,255,.92);

        border:1px solid rgba(226,232,240,.95);

        border-radius:22px;

        padding:28px;

        box-shadow:var(--shadow-md);
    }

    .catalogue-badge{
        display:inline-flex;

        padding:10px 16px;

        border-radius:999px;

        background:rgba(14,165,233,.12);

        color:#0ea5e9;

        font-size:.82rem;
        font-weight:800;
        letter-spacing:.08em;
    }

    .catalogue-hero-copy h2{
        margin:18px 0 12px;

        font-family:"Barlow Condensed",sans-serif;
        font-weight:800;

        color:#0ea5e9;

        font-size:clamp(2rem,4vw,2.8rem);

        line-height:1;
    }

    .catalogue-hero-copy p{
        margin:0;

        color:var(--text-soft);

        line-height:1.8;
    }

    .catalogue-actions{
        display:flex;
        gap:12px;
        flex-wrap:wrap;

        margin-top:22px;
    }

    .catalogue-btn{
        display:inline-flex;
        align-items:center;
        gap:10px;

        padding:14px 18px;

        border-radius:14px;

        text-decoration:none;

        font-weight:700;

        transition:.2s ease;
    }

    .catalogue-btn-back{
        background:#ffffff;

        border:1px solid rgba(148,163,184,.25);

        color:#0f172a;
    }

    .catalogue-btn-back:hover{
        background:#f8fafc;
    }

    .catalogue-btn-cart{
        background:#0ea5e9;
        color:#ffffff;
    }

    .catalogue-btn-cart:hover{
        background:#0284c7;
    }

    /*
    |--------------------------------------------------------------------------
    | VISUAL
    |--------------------------------------------------------------------------
    */

    .catalogue-hero-visual{
        position:relative;

        overflow:hidden;

        border-radius:22px;

        min-height:320px;

        background:rgba(255,255,255,.7);

        border:1px solid rgba(226,232,240,.95);

        box-shadow:var(--shadow-md);
    }

    .catalogue-hero-visual img{
        position:absolute;
        inset:0;

        width:100%;
        height:100%;

        object-fit:cover;

        z-index:0;
    }

    .catalogue-hero-visual::after{
        content:"";

        position:absolute;
        inset:0;

        background:
        linear-gradient(
            135deg,
            rgba(255,255,255,.75),
            rgba(255,255,255,.18)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GRID
    |--------------------------------------------------------------------------
    */

    .catalogue-products{
        display:grid;

        grid-template-columns:
        repeat(3,minmax(0,1fr));

        gap:18px;

        margin-top:24px;
    }

    /*
    |--------------------------------------------------------------------------
    | CARD
    |--------------------------------------------------------------------------
    */

    .catalogue-card{
        background:rgba(255,255,255,.92);

        border:1px solid rgba(226,232,240,.95);

        border-radius:22px;

        overflow:hidden;

        box-shadow:var(--shadow-sm);

        transition:
        transform .15s ease,
        border-color .15s ease,
        background .15s ease;
    }

    .catalogue-card:hover{
        transform:translateY(-2px);

        border-color:rgba(14,165,233,.4);

        background:rgba(14,165,233,.04);
    }

    .catalogue-card-image{
        height:220px;

        background:#f8fafc;

        overflow:hidden;
    }

    .catalogue-card-image img{
        width:100%;
        height:100%;

        object-fit:cover;
    }

    .catalogue-card-body{
        padding:20px;
    }

    .catalogue-tag{
        display:inline-flex;

        padding:8px 14px;

        border-radius:999px;

        background:rgba(14,165,233,.12);

        color:#0ea5e9;

        font-size:.75rem;
        font-weight:800;
    }

    .catalogue-card h3{
        margin:14px 0 10px;

        color:#0f172a;

        font-size:1.1rem;
        font-weight:800;
    }

    .catalogue-card p{
        margin:0;

        color:var(--text-soft);

        line-height:1.7;

        font-size:.94rem;
    }

    .catalogue-card-bottom{
        display:flex;
        align-items:center;
        justify-content:space-between;

        gap:12px;

        margin-top:20px;
    }

    .catalogue-price{
        color:#0ea5e9;

        font-size:1.2rem;
        font-weight:900;
    }

    .catalogue-add-btn{
        display:inline-flex;
        align-items:center;
        gap:8px;

        padding:12px 16px;

        border-radius:12px;

        background:#0ea5e9;
        color:#ffffff;

        text-decoration:none;

        font-weight:700;

        transition:.2s ease;
    }

    .catalogue-add-btn:hover{
        background:#0284c7;
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE
    |--------------------------------------------------------------------------
    */

    @media (max-width:992px){

        .catalogue-hero{
            grid-template-columns:1fr;
        }

        .catalogue-products{
            grid-template-columns:1fr;
        }

    }

</style>

<section class="catalogue-live">

    <!-- HERO -->
    <div class="catalogue-hero">

        <div class="catalogue-hero-copy">

            <span class="catalogue-badge">
                NOCIBE CIMENTS
            </span>

            <h2>
                Catalogue Des Produits
            </h2>

            <p>
                Consultez les différents types de ciment
                disponibles pour vos chantiers, constructions
                et projets immobiliers.
            </p>

            <div class="catalogue-actions">

                <a href="panier.php" class="catalogue-btn catalogue-btn-cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    Voir panier
                </a>

            </div>

        </div>

        <div class="catalogue-hero-visual">
            <img src="../../assets/images/home-hero-clean.png" alt="NOCIBE">
        </div>

    </div>

    <!-- PRODUCTS -->
    <div class="catalogue-products">

        <!-- CARD -->
        <div class="catalogue-card">

            <div class="catalogue-card-image">
                <img src="../../assets/images/ciment1.jpg" alt="Ciment">
            </div>

            <div class="catalogue-card-body">

                <span class="catalogue-tag">
                    Best Seller
                </span>

                <h3>
                    Ciment Portland CEM II
                </h3>

                <p>
                    Ciment haute résistance adapté aux
                    travaux de construction modernes.
                </p>

                <div class="catalogue-card-bottom">

                    <div class="catalogue-price">
                        5 800 FCFA
                    </div>

                    <a href="../includes/panier_add.php?add=1" class="catalogue-add-btn">
                        <i class="fa-solid fa-basket-shopping"></i>
                        Ajouter
                    </a>


                </div>

            </div>

        </div>

        <!-- CARD -->
        <div class="catalogue-card">

            <div class="catalogue-card-image">
                <img src="../../assets/images/ciment2.jpg" alt="Ciment">
            </div>

            <div class="catalogue-card-body">

                <span class="catalogue-tag">
                    Disponible
                </span>

                <h3>
                    Ciment Gris 32.5R
                </h3>

                <p>
                    Solution idéale pour les travaux
                    standards et les constructions simples.
                </p>

                <div class="catalogue-card-bottom">

                    <div class="catalogue-price">
                        4 900 FCFA
                    </div>

                    <a href="../includes/panier_add.php?add=2" class="catalogue-add-btn">
                        <i class="fa-solid fa-basket-shopping"></i>
                        Ajouter
                    </a>

                </div>


            </div>

        </div>

        <!-- CARD -->
        <div class="catalogue-card">

            <div class="catalogue-card-image">
                <img src="../../assets/images/ciment3.jpg" alt="Ciment">
            </div>

            <div class="catalogue-card-body">

                <span class="catalogue-tag">
                    Premium
                </span>

                <h3>
                    Ciment Premium 52.5N
                </h3>

                <p>
                    Ciment ultra performant conçu pour
                    les grands ouvrages et infrastructures.
                </p>

                <div class="catalogue-card-bottom">

                    <div class="catalogue-price">
                        7 200 FCFA
                    </div>

                    <a href="../includes/panier_add.php?add=3" class="catalogue-add-btn">
                        <i class="fa-solid fa-basket-shopping"></i>
                        Ajouter
                    </a>


                </div>


            </div>

        </div>

    </div>

</section>

</body>
</html>