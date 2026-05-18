<?php

$dashboardTitle = $dashboardTitle ?? 'Tableau de bord';
$dashboardLead = $dashboardLead ?? 'Bienvenue dans votre espace NOCIBE.';
$currentUser = currentUser();
$baseBackofficePath = '../';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCIBE | <?= htmlspecialchars($dashboardTitle) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="backoffice-page">
    <div class="backoffice-shell">
        <?php include __DIR__ . '/sidebar.php'; ?>

        <main class="backoffice-main">
            <header class="backoffice-header">
                <div>
                    <span class="eyebrow"><i class="fa-solid fa-gauge-high"></i> Backoffice</span>
                    <h1><?= htmlspecialchars($dashboardTitle) ?></h1>
                    <p><?= htmlspecialchars($dashboardLead) ?></p>
                </div>
                <div class="backoffice-user">
                    <strong><?= htmlspecialchars($currentUser['nom'] ?? 'Utilisateur') ?></strong>
                    <span><?= htmlspecialchars($currentUser['role'] ?? '') ?></span>
                </div>
            </header>
