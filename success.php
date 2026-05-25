<?php
require_once __DIR__ . '/includes/auth_check.php';
requireRole('client');

include __DIR__ . '/backoffice/includes/header.php';

// callback MTN dépose mtn_result_code / mtn_transaction_id
if (empty($_SESSION['mtn_result_code']) || (string)$_SESSION['mtn_result_code'] !== '0') {
    header("Location: catalogue.php");
    exit;
}
?>

<style>

.success-box {
    max-width: 600px;
    margin: 60px auto;
    text-align: center;

    background: rgba(255,255,255,.95);
    border: 1px solid rgba(226,232,240,.95);
    border-radius: 22px;
    padding: 40px;
    box-shadow: var(--shadow-md);
}

.success-title {
    font-size: 2rem;
    font-weight: 900;
    color: #10b981;
}

.success-box p {
    color: var(--text-soft);
    margin-top: 10px;
}

.success-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 14px 18px;
    background: #075fc7;
    color: white;
    border-radius: 14px;
    text-decoration: none;
    font-weight: 800;
}

</style>

<div class="success-box">

    <div class="success-title">
        Paiement effectué avec succès 🎉
    </div>

    <p>Votre commande a été confirmée et sera traitée rapidement.</p>

    <a href="catalogue.php" class="success-btn">
        Retour au catalogue
    </a>

</div>

</body>
</html>
