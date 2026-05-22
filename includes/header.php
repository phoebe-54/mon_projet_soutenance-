<?php
$headerUser = function_exists('current_user') ? current_user() : null;
$headerRole = $headerUser['role'] ?? '';
$headerRole = $headerRole === 'administrateur' ? 'admin' : $headerRole;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCIBE | <?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath) ?>assets/css/style.css?v=20260522-4">
</head>
<body class="<?= htmlspecialchars($bodyClass ?? 'site-shell') ?>">
<header class="site-header">
    <div class="site-container site-header-inner">
        <a class="brand-lockup" href="<?= htmlspecialchars($basePath) ?>index.php">
            <img src="<?= htmlspecialchars($basePath) ?>assets/images/Copilot_20260522_155511.png" alt="NOCIBE S.A Logo" class="brand-logo">
            <span class="brand-text">
                <strong>NOCIBE S.A</strong>
                <span>Nouvelle cimenterie du B&eacute;nin</span>
                <span>Bien construire commence chez nous</span>
            </span>
        </a>
        <?php if (empty($hideHeaderNav)): ?>
        <nav class="site-nav header-nav" aria-label="Navigation principale">
            <a href="<?= htmlspecialchars($basePath) ?>index.php">Accueil</a>
            <a href="<?= htmlspecialchars($basePath) ?>index.php#how-it-works">Comment &ccedil;a marche</a>
            <a href="<?= htmlspecialchars($basePath) ?>index.php#contact-nocibe">Contact</a>
            <a href="<?= htmlspecialchars($basePath) ?>index.php#about-nocibe">A propos</a>
            <?php if ($headerUser): ?>
                <a class="btn btn-soft btn-sm" href="<?= htmlspecialchars($basePath) ?>backoffice/<?= htmlspecialchars($headerRole ?: 'client') ?>/index.php">Tableau de bord</a>
                <a class="btn btn-brand btn-sm" href="<?= htmlspecialchars($basePath) ?>logout.php">Deconnexion</a>
            <?php else: ?>
                <a class="btn btn-soft btn-sm" href="<?= htmlspecialchars($basePath) ?>login.php">Se connecter</a>
                <a class="btn btn-brand btn-sm" href="<?= htmlspecialchars($basePath) ?>register.php">S'inscrire</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
    </div>
</header>
