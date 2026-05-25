<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/session.php';

$error = '';
$notice = isset($_GET['registered']) ? 'Compte cree. Vous pouvez maintenant vous connecter.' : '';
$blockingMessage = isset($_GET['login_required'])
    ? 'Vous devez etre connecte pour commander. Veuillez vous connecter ou creer un compte.'
    : '';

function roleRedirectPath(string $role): string
{
    return match ($role) {
        'administrateur', 'admin' => 'backoffice/admin/index.php',
        'commercial' => 'backoffice/commercial/index.php',
        'livreur' => 'backoffice/livreur/index.php',
        default => 'catalogue.php',
    };
}

function detectUserRole(PDO $pdo, int $userId): string
{
    foreach (['administrateur' => 'admin', 'commercial' => 'commercial', 'livreur' => 'livreur', 'client' => 'client'] as $table => $role) {
        $stmt = $pdo->prepare("SELECT 1 FROM {$table} WHERE id_user = :id_user LIMIT 1");
        $stmt->execute(['id_user' => $userId]);

        if ($stmt->fetchColumn()) {
            return $role;
        }
    }

    return 'client';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => trim($_POST['email'] ?? '')]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($_POST['password'] ?? '', $user['mot_de_passe'])) {
            $error = 'Email ou mot de passe incorrect.';
        } else {
            $role = detectUserRole($pdo, (int) $user['id_user']);
            $displayName = trim(($user['prenom'] ?? '') . ' ' . $user['nom']);

            $_SESSION['user_id'] = (int) $user['id_user'];
            $_SESSION['nom'] = $displayName;
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $role;

            $_SESSION['user'] = [
                'id_user' => (int) $user['id_user'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'] ?? '',
                'email' => $user['email'],
                'role' => $role,
            ];

            header('Location: ' . roleRedirectPath($role));
            exit;
        }
    } catch (Throwable $exception) {
        $error = 'Connexion impossible : ' . $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCIBE | Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=20260525-6">
</head>
<body class="auth-page auth-login">
    <div class="auth-shell">
        <section class="auth-panel">
            <div class="auth-panel-logo">
                <img src="assets/images/Copilot_20260522_155511.png" alt="Logo NOCIBE S.A">
                <strong>NOCIBE S.A</strong>
            </div>
            <div class="login-panel-head">
                <span class="login-panel-icon">
                    <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="auth-title">Connexion</h2>
                    <p class="auth-subtitle">Connectez-vous pour gerer vos commandes, consulter vos paiements et suivre vos livraisons en toute securite.</p>
                </div>
            </div>

            <?php if ($notice): ?>
                <div class="alert alert-success"><?= htmlspecialchars($notice) ?></div>
            <?php endif; ?>
            <?php if ($blockingMessage): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($blockingMessage) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form class="row g-3" action="login.php" method="POST">
                <div class="col-12">
                    <label class="form-label fw-semibold">Adresse email</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="exemple@nocibe.com" required>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Mot de passe</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Votre mot de passe" required>
                        <button class="password-toggle" type="button" aria-label="Afficher le mot de passe">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <label class="login-remember">
                        <input type="checkbox">
                        <span>Se souvenir de moi</span>
                    </label>
                </div>
                <div class="col-12">
                    <button class="btn btn-brand w-100 py-3" type="submit">
                        <i class="fa-solid fa-arrow-right"></i>
                        Se connecter
                    </button>
                </div>
            </form>

            <div class="auth-links">
                Pas encore de compte ?
                <a href="register.php"><strong>S'inscrire</strong></a>
            </div>
        </section>
    </div>
    <script src="assets/js/password-toggle.js?v=20260525-2"></script>
</body>
</html>
