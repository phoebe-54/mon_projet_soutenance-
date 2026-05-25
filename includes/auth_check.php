<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';

if (!function_exists('normalizeRole')) {
    function normalizeRole(string $role): string
    {
        return match ($role) {
            'administrateur' => 'admin',
            default => $role,
        };
    }
}

if (!function_exists('currentUser')) {
    function currentUser(): ?array
    {
        if (!empty($_SESSION['user_id'])) {
            return [
                'id_user' => $_SESSION['user_id'],
                'nom' => $_SESSION['nom'] ?? '',
                'email' => $_SESSION['email'] ?? '',
                'role' => normalizeRole($_SESSION['role'] ?? ''),
            ];
        }

        if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
            $user = $_SESSION['user'];

            return [
                'id_user' => $user['id_user'] ?? null,
                'nom' => trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')),
                'email' => $user['email'] ?? '',
                'role' => normalizeRole($user['role'] ?? ''),
            ];
        }

        return null;
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin(): array
    {
        $user = currentUser();

        if ($user === null) {
            $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
            $loginPath = str_contains($scriptPath, '/backoffice/') ? '../../login.php' : 'login.php';
            header('Location: ' . $loginPath);
            exit;
        }

        return $user;
    }
}

if (!function_exists('requireRole')) {
    function requireRole(string|array $roles): array
    {
        $user = requireLogin();
        $allowedRoles = array_map('normalizeRole', (array) $roles);

        if (!in_array($user['role'], $allowedRoles, true)) {
            http_response_code(403);
            exit('Acces refuse.');
        }

        return $user;
    }
}
