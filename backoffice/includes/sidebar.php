<?php

$currentRole = normalizeRole($_SESSION['role'] ?? ($_SESSION['user']['role'] ?? ''));
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isBackofficePage = str_contains($scriptPath, '/backoffice/');
$appRootPath = $isBackofficePage ? '../../' : '';
$clientPath = $isBackofficePage ? '../../' : '';
$adminPath = $isBackofficePage ? '../admin/' : 'backoffice/admin/';
$commercialPath = $isBackofficePage ? '../commercial/' : 'backoffice/commercial/';
$driverPath = $isBackofficePage ? '../livreur/' : 'backoffice/livreur/';

$itemsByRole = [
    'admin' => [
        ['href' => $adminPath . 'index.php', 'icon' => 'fa-chart-line', 'label' => 'Dashboard admin'],
        ['href' => $adminPath . 'index.php#produits', 'icon' => 'fa-boxes-stacked', 'label' => 'Produits'],
        ['href' => $adminPath . 'index.php#commandes', 'icon' => 'fa-receipt', 'label' => 'Commandes'],
        ['href' => $adminPath . 'index.php#paiements', 'icon' => 'fa-credit-card', 'label' => 'Paiements'],
        ['href' => $adminPath . 'index.php#livraisons', 'icon' => 'fa-truck-fast', 'label' => 'Livraisons'],
        ['href' => $adminPath . 'index.php#utilisateurs', 'icon' => 'fa-users', 'label' => 'Utilisateurs'],
    ],
    'client' => [
        ['href' => $clientPath . 'index.php', 'icon' => 'fa-house', 'label' => 'Dashboard client'],
        ['href' => $clientPath . 'catalogue.php', 'icon' => 'fa-boxes-stacked', 'label' => 'Catalogue'],
        ['href' => $clientPath . 'panier.php', 'icon' => 'fa-cart-shopping', 'label' => 'Panier'],
        ['href' => $clientPath . 'historique.php', 'icon' => 'fa-receipt', 'label' => 'Commandes'],
        ['href' => $clientPath . 'suivi.php', 'icon' => 'fa-truck-fast', 'label' => 'Livraisons'],
        ['href' => $clientPath . 'support.php', 'icon' => 'fa-headset', 'label' => 'Support'],
    ],
    'commercial' => [
        ['href' => $commercialPath . 'index.php', 'icon' => 'fa-briefcase', 'label' => 'Dashboard commercial'],
    ],
    'livreur' => [
        ['href' => $driverPath . 'index.php', 'icon' => 'fa-truck-fast', 'label' => 'Dashboard livreur'],
    ],
];

$sidebarItems = $itemsByRole[$currentRole] ?? [];
?>
<aside class="backoffice-sidebar">
    <a class="backoffice-brand" href="<?= htmlspecialchars($appRootPath) ?>index.php">
        <img src="<?= htmlspecialchars($appRootPath) ?>assets/images/Copilot_20260522_155511.png" alt="NOCIBE S.A">
        <span class="backoffice-brand-text">
            <strong>NOCIBE S.A</strong>
            <small>Nouvelle cimenterie du B&eacute;nin</small>
            <small>Bien construire commence chez nous</small>
        </span>
    </a>

    <nav class="backoffice-nav">
        <?php foreach ($sidebarItems as $item): ?>
            <a href="<?= htmlspecialchars($item['href']) ?>">
                <i class="fa-solid <?= htmlspecialchars($item['icon']) ?>"></i>
                <span><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <a class="backoffice-logout" href="<?= htmlspecialchars($appRootPath) ?>logout.php">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Deconnexion</span>
    </a>
</aside>
