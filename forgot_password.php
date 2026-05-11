<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCIBE | Mot de passe oublie</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page auth-login">
    <div class="auth-shell">
        <section class="auth-brand">
            <img class="auth-visual" src="assets/images/login-hero-clean.png" alt="Illustration authentification NOCIBE">
        </section>

        <section class="auth-panel">
            <h2 class="auth-title">Mot de passe oublie</h2>
            <p class="auth-subtitle">Entrez votre adresse email pour demander une reinitialisation.</p>

            <form class="row g-3" method="post" action="#">
                <div class="col-12">
                    <label class="form-label fw-semibold">Adresse email</label>
                    <div class="input-shell">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" class="form-control" placeholder="exemple@nocibe.com" required>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-brand w-100 py-3">
                        <i class="fa-solid fa-paper-plane"></i>
                        Envoyer le lien
                    </button>
                </div>
                <div class="col-12 text-center">
                    <a class="form-note" href="login.php">Retour a la connexion</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
