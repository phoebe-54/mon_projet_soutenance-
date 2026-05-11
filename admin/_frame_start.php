<?php
$adminTitle = $adminTitle ?? 'Administration';
$adminLead = $adminLead ?? 'Pilotage des operations ciment, commandes et livraisons NOCIBE.';
$adminPage = $adminPage ?? '';
$adminBadge = $adminBadge ?? 'Back-office';

$adminLinks = [
    ['file' => 'dashboard.php', 'icon' => 'fa-grid-2', 'label' => 'Dashboard'],
    ['file' => 'produits.php', 'icon' => 'fa-box-open', 'label' => 'Produits'],
    ['file' => 'categories.php', 'icon' => 'fa-tags', 'label' => 'Categories'],
    ['file' => 'commandes.php', 'icon' => 'fa-cart-shopping', 'label' => 'Commandes'],
    ['file' => 'livraisons.php', 'icon' => 'fa-truck-fast', 'label' => 'Livraisons'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCIBE Admin | <?= htmlspecialchars($adminTitle) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-page">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="brand-lockup">
            <img src="../assets/images/nocibe-logo-icon-transparent.png" alt="Logo NOCIBE" class="brand-logo">
            <span class="brand-text">
                <strong>NOCIBE</strong>
                <span>Interface d'administration cimenterie</span>
            </span>
        </div>

        <div class="admin-visual glass-card">
            <img class="admin-visual-photo" src="../assets/images/Copilot_20260427_183521.png" alt="Equipe logistique NOCIBE en entrepot">
            <p>Un back-office plus robuste pour piloter les stocks, les commandes, les categories et les livraisons.</p>
        </div>

        <div class="admin-nav">
            <div class="admin-nav-title">Pilotage</div>
            <?php foreach ($adminLinks as $link): ?>
                <a href="<?= htmlspecialchars($link['file']) ?>" class="<?= $adminPage === $link['file'] ? 'active' : '' ?>">
                    <i class="fa-solid <?= htmlspecialchars($link['icon']) ?>"></i>
                    <?= htmlspecialchars($link['label']) ?>
                </a>
            <?php endforeach; ?>

            <div class="admin-nav-title">Raccourcis</div>
            <a href="../index.php">
                <i class="fa-solid fa-house"></i>
                Retour au site
            </a>
            <a href="../login.php">
                <i class="fa-solid fa-right-from-bracket"></i>
                Deconnexion
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="glass-card admin-topbar">
            <div>
                <span class="eyebrow"><i class="fa-solid fa-sparkles"></i> <?= htmlspecialchars($adminBadge) ?></span>
                <h1 class="admin-title"><?= htmlspecialchars($adminTitle) ?></h1>
                <p><?= htmlspecialchars($adminLead) ?></p>
            </div>
            <div class="admin-actions">
                <div class="notification-menu notification-menu-admin">
                    <button class="chip chip-notification notification-toggle" type="button" aria-expanded="false">
                        <i class="fa-solid fa-bell"></i>
                        <span>Alertes</span>
                        <span class="chip-pill notification-badge">3</span>
                    </button>
                    <div class="notification-panel" aria-label="Alertes administration">
                        <div class="notification-panel-head">
                            <strong>Points a suivre</strong>
                            <span>3 alertes</span>
                        </div>
                        <a href="commandes.php" class="notification-item">
                            <i class="fa-solid fa-clock"></i>
                            <span>
                                <strong>Commandes en attente</strong>
                                2 commandes doivent etre confirmees.
                            </span>
                        </a>
                        <a href="livraisons.php" class="notification-item">
                            <i class="fa-solid fa-truck-ramp-box"></i>
                            <span>
                                <strong>Livraisons a surveiller</strong>
                                Une expedition est encore en preparation.
                            </span>
                        </a>
                        <a href="produits.php" class="notification-item">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <span>
                                <strong>Stocks a verifier</strong>
                                Controlez les produits avant validation.
                            </span>
                        </a>
                    </div>
                </div>
                <span class="chip"><span class="avatar-dot">AD</span> Admin NOCIBE</span>
            </div>
        </header>
