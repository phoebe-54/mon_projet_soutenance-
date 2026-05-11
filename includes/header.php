<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCIBE | <?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath) ?>assets/css/style.css?v=20260506-1">
</head>
<body class="<?= htmlspecialchars($bodyClass ?? 'site-shell') ?>">
<header class="site-header">
    <div class="site-container site-header-inner">
        <a class="brand-lockup" href="<?= htmlspecialchars($basePath) ?>index.php">
            <img src="<?= htmlspecialchars($basePath) ?>assets/images/nocibe-logo-icon-transparent.png" alt="NOCIBE Logo" class="brand-logo">
            <span class="brand-text">
                <strong>NOCIBE</strong>
                <span>Production, commande et livraison de ciment</span>
            </span>
        </a>
    </div>
</header>
