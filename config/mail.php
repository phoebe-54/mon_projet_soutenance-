<?php

declare(strict_types=1);

return [
    'host' => getenv('NOCIBE_MAIL_HOST') ?: 'smtp.gmail.com',
    'port' => (int) (getenv('NOCIBE_MAIL_PORT') ?: 465),
    'secure' => getenv('NOCIBE_MAIL_SECURE') ?: 'ssl',
    'username' => getenv('NOCIBE_MAIL_USERNAME') ?: 'phoebehougni@gmail.com',
    'password' => getenv('NOCIBE_MAIL_PASSWORD') ?: 'vvrs pdqi pnmy ufuv',
    'from_email' => getenv('NOCIBE_MAIL_FROM') ?: (getenv('NOCIBE_MAIL_USERNAME') ?: 'phoebehougni@gmail.com'),
    'from_name' => getenv('NOCIBE_MAIL_FROM_NAME') ?: 'NOCIBE S.A',
    'app_url' => getenv('NOCIBE_APP_URL') ?: 'http://192.168.43.55/nocibe',
];
