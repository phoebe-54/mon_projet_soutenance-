<?php
require_once __DIR__ . '/includes/auth_check.php';
requireRole('client');

$suiviTitle = 'Suivi';
$suiviLead = 'Suivez vos livraisons NOCIBE en temps réel.';

$currentUser = currentUser();

include __DIR__ . '/backoffice/includes/header.php';
?>

<style>

    .suivi-live{
        padding-top:14px;
    }

    /*
    |--------------------------------------------------------------------------
    | HERO
    |--------------------------------------------------------------------------
    */

    .suivi-hero{
        display:grid;

        grid-template-columns:
        minmax(0,1.05fr)
        minmax(0,.95fr);

        gap:22px;

        align-items:stretch;

        margin-top:10px;
    }

    .suivi-hero-copy{
        background:rgba(255,255,255,.92);

        border:1px solid rgba(226,232,240,.95);

        border-radius:22px;

        padding:28px;

        box-shadow:var(--shadow-md);
    }

    .suivi-badge{
        display:inline-flex;

        padding:10px 16px;

        border-radius:999px;

        background:rgba(7,95,199,.12);

        color:#075fc7;

        font-size:.82rem;
        font-weight:800;
        letter-spacing:.08em;
    }

    .suivi-hero-copy h2{
        margin:18px 0 12px;

        font-family:"Barlow Condensed",sans-serif;
        font-weight:800;

        color:#075fc7;

        font-size:clamp(2rem,4vw,2.8rem);

        line-height:1;
    }

    .suivi-hero-copy p{
        margin:0;

        color:var(--text-soft);

        line-height:1.8;
    }

    .suivi-actions{
        display:flex;

        gap:12px;

        flex-wrap:wrap;

        margin-top:22px;
    }

    .suivi-btn{
        display:inline-flex;
        align-items:center;
        gap:10px;

        padding:14px 18px;

        border-radius:14px;

        text-decoration:none;

        font-weight:700;

        transition:.2s ease;
    }

    .suivi-btn-dashboard{
        background:#ffffff;

        border:1px solid rgba(148,163,184,.25);

        color:#0f172a;
    }

    .suivi-btn-dashboard:hover{
        background:#f8fafc;
    }

    .suivi-btn-refresh{
        background:#075fc7;
        color:#ffffff;
    }

    .suivi-btn-refresh:hover{
        background:#0284c7;
    }

    /*
    |--------------------------------------------------------------------------
    | VISUAL
    |--------------------------------------------------------------------------
    */

    .suivi-hero-visual{
        position:relative;

        overflow:hidden;

        border-radius:22px;

        min-height:320px;

        background:rgba(255,255,255,.7);

        border:1px solid rgba(226,232,240,.95);

        box-shadow:var(--shadow-md);
    }

    .suivi-hero-visual img{
        position:absolute;
        inset:0;

        width:100%;
        height:100%;

        object-fit:cover;
    }

    .suivi-hero-visual::after{
        content:"";

        position:absolute;
        inset:0;

        background:
        linear-gradient(
            135deg,
            rgba(255,255,255,.72),
            rgba(255,255,255,.15)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LISTE LIVRAISONS
    |--------------------------------------------------------------------------
    */

    .suivi-list{
        display:flex;

        flex-direction:column;

        gap:18px;

        margin-top:24px;
    }

    /*
    |--------------------------------------------------------------------------
    | CARD
    |--------------------------------------------------------------------------
    */

    .suivi-card{
        background:rgba(255,255,255,.92);

        border:1px solid rgba(226,232,240,.95);

        border-radius:22px;

        padding:22px;

        box-shadow:var(--shadow-sm);

        transition:
        transform .15s ease,
        border-color .15s ease,
        background .15s ease;
    }

    .suivi-card:hover{
        transform:translateY(-2px);

        border-color:rgba(7,95,199,.35);

        background:rgba(7,95,199,.03);
    }

    .suivi-top{
        display:flex;

        align-items:center;

        justify-content:space-between;

        gap:14px;

        flex-wrap:wrap;
    }

    .suivi-order{
        display:flex;
        flex-direction:column;
        gap:6px;
    }

    .suivi-order h3{
        margin:0;

        font-size:1.1rem;

        color:#0f172a;
    }

    .suivi-order span{
        color:var(--text-soft);

        font-size:.92rem;
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    .suivi-status{
        display:inline-flex;

        align-items:center;

        gap:8px;

        padding:10px 16px;

        border-radius:999px;

        font-size:.82rem;

        font-weight:800;
    }

    .status-route{
        background:rgba(7,95,199,.12);
        color:#075fc7;
    }

    .status-livree{
        background:rgba(16,185,129,.12);
        color:#10b981;
    }

    .status-attente{
        background:rgba(245,158,11,.12);
        color:#f59e0b;
    }

    /*
    |--------------------------------------------------------------------------
    | TIMELINE
    |--------------------------------------------------------------------------
    */

    .timeline{
        margin-top:22px;

        display:flex;

        flex-direction:column;

        gap:18px;
    }

    .timeline-step{
        display:flex;

        align-items:flex-start;

        gap:14px;
    }

    .timeline-dot{
        width:16px;
        height:16px;

        border-radius:50%;

        margin-top:4px;

        background:#075fc7;

        flex-shrink:0;
    }

    .timeline-content h4{
        margin:0 0 4px;

        font-size:.96rem;

        color:#0f172a;
    }

    .timeline-content p{
        margin:0;

        color:var(--text-soft);

        line-height:1.7;

        font-size:.92rem;
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE
    |--------------------------------------------------------------------------
    */

    @media (max-width:992px){

        .suivi-hero{
            grid-template-columns:1fr;
        }

    }

</style>

<section class="suivi-live">

    <!-- HERO -->
    <div class="suivi-hero">

        <div class="suivi-hero-copy">

            <span class="suivi-badge">
                NOCIBE LIVRAISON
            </span>

            <h2>
                Suivi Des Commandes
            </h2>

            <p>
                Consultez l’état de vos livraisons,
                les commandes en cours ainsi que
                les étapes de transport de vos produits.
            </p>

            <div class="suivi-actions">

                <a href="index.php" class="suivi-btn suivi-btn-dashboard">
                    <i class="fa-solid fa-arrow-left"></i>
                    Dashboard
                </a>

                <a href="suivi.php" class="suivi-btn suivi-btn-refresh">
                    <i class="fa-solid fa-rotate-right"></i>
                    Actualiser
                </a>

            </div>

        </div>

        <div class="suivi-hero-visual">
            <img src="../../assets/images/home-hero-clean.png" alt="NOCIBE">
        </div>

    </div>

    <!-- LISTE -->
    <div class="suivi-list">

        <!-- CARD -->
        <div class="suivi-card">

            <div class="suivi-top">

                <div class="suivi-order">
                    <h3>
                        Commande #NOC2026-001
                    </h3>

                    <span>
                        Livraison vers Porto-Novo
                    </span>
                </div>

                <div class="suivi-status status-route">
                    <i class="fa-solid fa-truck"></i>
                    En route
                </div>

            </div>

            <div class="timeline">

                <div class="timeline-step">

                    <div class="timeline-dot"></div>

                    <div class="timeline-content">
                        <h4>Commande validée</h4>
                        <p>
                            Votre commande a été confirmée avec succès.
                        </p>
                    </div>

                </div>

                <div class="timeline-step">

                    <div class="timeline-dot"></div>

                    <div class="timeline-content">
                        <h4>Préparation</h4>
                        <p>
                            Les produits sont en préparation dans notre dépôt.
                        </p>
                    </div>

                </div>

                <div class="timeline-step">

                    <div class="timeline-dot"></div>

                    <div class="timeline-content">
                        <h4>Transport</h4>
                        <p>
                            Le camion de livraison est actuellement en route.
                        </p>
                    </div>

                </div>

            </div>

        </div>

        <!-- CARD -->
        <div class="suivi-card">

            <div class="suivi-top">

                <div class="suivi-order">
                    <h3>
                        Commande #NOC2026-002
                    </h3>

                    <span>
                        Livraison vers Cotonou
                    </span>
                </div>

                <div class="suivi-status status-livree">
                    <i class="fa-solid fa-circle-check"></i>
                    Livrée
                </div>

            </div>

            <div class="timeline">

                <div class="timeline-step">

                    <div class="timeline-dot"></div>

                    <div class="timeline-content">
                        <h4>Commande livrée</h4>
                        <p>
                            Votre commande a été livrée avec succès.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

</body>
</html>
