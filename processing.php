<?php
require_once __DIR__ . '/includes/auth_check.php';
requireRole('client');

include __DIR__ . '/backoffice/includes/header.php';

$method = $_SESSION['paiement_method'] ?? 'inconnu';
?>

<style>
.box{
    max-width:600px;
    margin:80px auto;
    text-align:center;
    padding:40px;
    background:#fff;
    border-radius:22px;
    box-shadow:var(--shadow-md);
}
.loader{
    width:40px;
    height:40px;
    border:4px solid #ddd;
    border-top:4px solid #075fc7;
    border-radius:50%;
    margin:20px auto;
    animation:spin 1s linear infinite;
}

@keyframes spin{
    100%{transform:rotate(360deg);}
}
</style>

<div class="box">

<h2>Traitement paiement <?= strtoupper($method) ?></h2>

<div class="loader"></div>

<p>Veuillez patienter...</p>

</div>

<script>
// Ne pas rediriger vers success sans confirmation MTN.
// La page success.php valide le callback via $_SESSION.
</script>
