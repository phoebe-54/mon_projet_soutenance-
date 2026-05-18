<?php

$currentRole = normalizeRole($_SESSION['role'] ?? ($_SESSION['user']['role'] ?? ''));
$itemsByRole = [
    'admin' => [
        ['href' => '../admin/index.php', 'icon' => 'fa-chart-line', 'label' => 'Dashboard admin'],
    ],
    'client' => [
        ['href' => '../client/index.php', 'icon' => 'fa-house', 'label' => 'Dashboard client'],
    ],
    'commercial' => [
        ['href' => '../commercial/index.php', 'icon' => 'fa-briefcase', 'label' => 'Dashboard commercial'],
    ],
    'livreur' => [
        ['href' => '../livreur/index.php', 'icon' => 'fa-truck-fast', 'label' => 'Dashboard livreur'],
    ],
];

$sidebarItems = $itemsByRole[$currentRole] ?? [];
?>
<aside class="backoffice-sidebar">
    <a class="backoffice-brand" href="../../index.php">
        <img src="../../assets/images/nocibe-logo-icon-transparent.png" alt="NOCIBE">
        <span>NOCIBE</span>
    </a>

    <nav class="backoffice-nav">
        <?php foreach ($sidebarItems as $item): ?>
            <a href="<?= htmlspecialchars($item['href']) ?>">
                <i class="fa-solid <?= htmlspecialchars($item['icon']) ?>"></i>
                <span><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <a class="backoffice-logout" href="../../logout.php">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Deconnexion</span>
    </a>
</aside>
