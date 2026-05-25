<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/smtp_mailer.php';

$to = trim($_GET['to'] ?? '');

if ($to === '') {
    http_response_code(400);
    echo 'Ajoutez ?to=email@example.com dans l URL.';
    exit;
}

$sent = sendSmtpMail(
    $to,
    'Test email NOCIBE',
    "Bonjour,\n\nCeci est un test SMTP NOCIBE.\n\nSi vous recevez ce message, l'envoi Gmail est configure."
);

echo $sent ? 'Email envoye.' : 'Email non envoye. ' . htmlspecialchars(smtpLastError());
