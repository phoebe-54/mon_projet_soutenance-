<?php
require_once '../../includes/auth_check.php';
requireRole('client');
require_once '../../config/database.php';

$dashboardTitle = 'Bienvenue chez NOCIBE';
$dashboardLead = 'Gérez votre catalogue, composez votre panier et suivez vos livraisons en temps réel.';

$currentUser = currentUser();
include '../includes/header.php';
?>

<style>
    .client-dashboard-live {
        padding-top: 14px;
    }

    .client-dashboard-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.06fr) minmax(0, 0.94fr);
        gap: 22px;
        align-items: stretch;
        margin-top: 10px;
    }

    .client-dashboard-hero-copy {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 22px;
        padding: 26px;
        box-shadow: var(--shadow-md);
    }

    .client-dashboard-hero-copy h2 {
        margin: 14px 0 10px;
        font-family: "Barlow Condensed", sans-serif;
        font-weight: 800;
        color: #0ea5e9;
        font-size: clamp(1.8rem, 3.2vw, 2.4rem);
        line-height: 1.05;
    }

    .client-dashboard-hero-copy p {
        margin: 0;
        color: var(--text-soft);
        line-height: 1.8;
    }

    .client-dashboard-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .client-dashboard-hero-visual {
        position: relative;
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 22px;
        padding: 14px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        min-height: 280px;
    }

    /* image en arrière-plan (moins "trop") */
    .client-dashboard-hero-visual::before {
        content: "";
        position: absolute;
        inset: 0;
        background: url('../../images/home-hero-clean.png') center/cover no-repeat;
        opacity: 0.35;
        transform: scale(1.04);
        filter: saturate(1.05) contrast(1.02);
    }

    .client-dashboard-hero-visual::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,.75) 0%, rgba(255,255,255,.25) 60%, rgba(255,255,255,.65) 100%);
    }

    /* image claire : on la rend visible mais derrière le texte/carte */
    .client-dashboard-hero-visual img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        opacity: 1;
        z-index: 0;
        /* rendu clair et net */
        image-rendering: auto;
        filter: none;
        transform: translateZ(0);
    }


    /* overlay pour que la carte/copy reste lisible */
    .client-dashboard-hero-visual::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.82) 0%, rgba(255,255,255,0.30) 55%, rgba(255,255,255,0.78) 100%);
        z-index: 1;
    }

    /* l’image de fond pseudo-élément devient inutile */
    .client-dashboard-hero-visual::before {
        opacity: 0;
    }



    .client-dashboard-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin-top: 18px;
    }

    @media (max-width: 992px) {
        .client-dashboard-hero {
            grid-template-columns: 1fr;
        }

        .client-dashboard-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="client-dashboard-live">
    <div class="client-dashboard-hero">
        <div class="client-dashboard-hero-visual">
            <img src="../../assets/images/home-hero-clean.png" alt="NOCIBE" />
        </div>
    </div>

    <div class="client-dashboard-stats"></div>

</section>

<style>
    .client-dashboard-link{
        display:block;
        transition:transform .15s ease, border-color .15s ease, background .15s ease;
    }
    .client-dashboard-link:hover{
        transform: translateY(-2px);
        border-color: rgba(0, 212, 255, .45);
        background: rgba(0, 212, 255, .08);
    }
</style>


</body>
</html>

