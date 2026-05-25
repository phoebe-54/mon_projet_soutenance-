<?php

declare(strict_types=1);

$mailConfig = require __DIR__ . '/config/mail.php';
$appUrl = rtrim((string) ($mailConfig['app_url'] ?? ''), '/');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCIBE | Test mobile</title>
    <link rel="stylesheet" href="assets/css/style.css?v=20260525-mobile">
    <style>
        body {
            margin: 0;
            padding: 18px;
            background: #f5f7fb;
            color: #0f172a;
            font-family: "Segoe UI", sans-serif;
        }

        .mobile-check {
            max-width: 620px;
            margin: 0 auto;
            display: grid;
            gap: 14px;
        }

        .mobile-check-card {
            background: #ffffff;
            border: 1px solid rgba(7, 95, 199, 0.16);
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .mobile-check h1 {
            margin: 0 0 8px;
            color: #062b8f;
            font-size: 1.7rem;
        }

        .mobile-check a {
            display: block;
            padding: 12px;
            margin-top: 8px;
            border-radius: 8px;
            background: #075fc7;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
        }

        .mobile-check code {
            display: block;
            overflow-wrap: anywhere;
            background: #eef6ff;
            padding: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <main class="mobile-check">
        <section class="mobile-check-card">
            <h1>NOCIBE mobile OK</h1>
            <p>Si cette page s'affiche sur le telephone, Apache est accessible depuis le reseau.</p>
            <code><?= htmlspecialchars($appUrl ?: 'app_url non configure') ?></code>
        </section>

        <section class="mobile-check-card">
            <a href="login.php">Connexion</a>
            <a href="backoffice/client/index.php">Espace client</a>
            <a href="backoffice/commercial/index.php">Espace commercial</a>
            <a href="backoffice/livreur/index.php">Espace livreur</a>
        </section>
    </main>
</body>
</html>
