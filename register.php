<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCIBE | Inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=20260505-1">
</head>
<body class="auth-page auth-register">
    <div class="auth-shell">
        <section class="auth-panel">
            <div class="auth-panel-logo">
                <img src="assets/images/nocibe-logo-transparent.png" alt="Logo NOCIBE">
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

            <form class="row g-3" action="register.php" method="POST">
                <div class="col-12">
                    <label class="form-label fw-semibold">Nom complet</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="nom" class="form-control" placeholder="Votre nom complet" required>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Adresse email</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="exemple@nocibe.com" required>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Telephone</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-phone"></i>
                        <input type="tel" name="telephone" class="form-control" placeholder="Votre numero de telephone" required>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Mot de passe</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Votre mot de passe" required>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Confirmation</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirmer le mot de passe" required>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-brand w-100 py-3" type="submit">
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
