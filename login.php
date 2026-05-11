<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCIBE | Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=20260505-1">
</head>
<body class="auth-page auth-login">
    <div class="auth-shell">
        <section class="auth-panel">
            <div class="auth-panel-logo">
                <img src="assets/images/nocibe-logo-transparent.png" alt="Logo NOCIBE">
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

            <form class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Adresse email</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" class="form-control" placeholder="exemple@nocibe.com">
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Mot de passe</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" class="form-control" placeholder="Votre mot de passe">
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <label class="login-remember">
                        <input type="checkbox">
                        <span>Se souvenir de moi</span>
                    </label>
                    <a class="form-note" href="forgot_password.php">Mot de passe oublie ?</a>
                </div>
                <div class="col-12">
                    <a href="admin/dashboard.php" class="btn btn-brand w-100 py-3">
                        <i class="fa-solid fa-arrow-right"></i>
                        Se connecter
                    </a>
                </div>
            </form>

            <div class="auth-links">
                Pas encore de compte ?
                <a href="register.php"><strong>S'inscrire</strong></a>
            </div>
        </section>
    </div>
</body>
</html>
