<?php
require_once __DIR__ . '/config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomComplet = trim($_POST['nom'] ?? '');
    $parts = preg_split('/\s+/', $nomComplet, 2);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($password !== $confirmPassword) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        try {
            $email = trim($_POST['email'] ?? '');
            $stmt = $pdo->prepare('SELECT id_user FROM utilisateur WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);

            if ($stmt->fetch()) {
                $error = 'Cette adresse email est deja utilisee.';
            } else {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare(
                    'INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe)
                     VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe)'
                );
                $stmt->execute([
                    'nom' => $parts[0] ?? $nomComplet,
                    'prenom' => $parts[1] ?? '',
                    'email' => $email,
                    'telephone' => trim($_POST['telephone'] ?? ''),
                    'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
                ]);

                $userId = (int) $pdo->lastInsertId();
                $stmt = $pdo->prepare('INSERT INTO client (id_user, adresse) VALUES (:id_user, :adresse)');
                $stmt->execute([
                    'id_user' => $userId,
                    'adresse' => 'A renseigner',
                ]);

                $pdo->commit();

                header('Location: login.php?registered=1');
                exit;
            }
        } catch (Throwable $exception) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $error = "Inscription impossible : " . $exception->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCIBE | Inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=20260522-4">
</head>
<body class="auth-page auth-register">
    <div class="auth-shell">
        <section class="auth-panel">
            <div class="auth-panel-logo">
                <img src="assets/images/Copilot_20260522_155511.png" alt="Logo NOCIBE S.A">
                <strong>NOCIBE S.A</strong>
            </div>
            <div class="login-panel-head">
                <span class="login-panel-icon">
                    <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="auth-title">Inscription</h2>
                    <p class="auth-subtitle">Creez votre compte pour gerer vos commandes et suivre vos livraisons NOCIBE.</p>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form class="register-form" action="register.php" method="POST">
                <div class="register-field">
                    <label class="form-label fw-semibold">Nom complet</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="nom" class="form-control" placeholder="Votre nom complet" required>
                    </div>
                </div>
                <div class="register-field">
                    <label class="form-label fw-semibold">Adresse email</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="exemple@nocibe.com" required>
                    </div>
                </div>
                <div class="register-field register-field-right">
                    <label class="form-label fw-semibold">Telephone</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-phone"></i>
                        <input type="tel" name="telephone" class="form-control" placeholder="Votre numero de telephone" required>
                    </div>
                </div>
                <div class="register-field">
                    <label class="form-label fw-semibold">Mot de passe</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Votre mot de passe" required>
                    </div>
                </div>
                <div class="register-field">
                    <label class="form-label fw-semibold">Confirmation</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirmer le mot de passe" required>
                    </div>
                </div>
                <div class="register-field register-action">
                    <button class="btn btn-brand w-100 py-3 register-submit" type="submit">
                        <i class="fa-solid fa-arrow-right"></i>
                        S'inscrire
                    </button>
                </div>
            </form>

            <div class="auth-links">
                Deja un compte ?
                <a href="login.php"><strong>Se connecter</strong></a>
            </div>
        </section>
    </div>
</body>
</html>
