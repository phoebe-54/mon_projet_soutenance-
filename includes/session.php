<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'sessions';

    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0775, true);
    }

    session_save_path($sessionPath);
    session_start();
}
