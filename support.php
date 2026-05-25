<?php
require_once __DIR__ . '/includes/auth_check.php';
requireRole('client');
require_once __DIR__ . '/config/database.php';

$client = currentUser();
$clientNom = $client['nom'] ?? 'Client';

$dashboardTitle = 'Support';
$dashboardLead = 'Contactez notre équipe pour toute assistance.';
include __DIR__ . '/backoffice/includes/header.php';
?>

<section style="padding-top:14px;">
    <div class="glass-card" style="padding:22px;border-radius:22px;border:1px solid rgba(148,163,184,.25);background:rgba(255,255,255,.85);">
        <h5 style="margin:0 0 10px;font-weight:900;color:#075fc7;">FAQ</h5>
        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="p-3" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.10);border-radius:16px;">
                    <strong>Comment suivre ma livraison ?</strong>
                    <p style="margin:6px 0 0;color:var(--text-soft);">Rendez-vous dans <a href="suivi.php" style="color:#075fc7;text-decoration:none;"><b>Suivi</b></a> depuis votre espace client.</p>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="p-3" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.10);border-radius:16px;">
                    <strong>Comment récupérer une facture ?</strong>
<p style="margin:6px 0 0;color:var(--text-soft);">Allez dans <a href="historique.php" style="color:#075fc7;text-decoration:none;"><b>Historique</b></a> → section <b>Factures (paiements)</b>.</p>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="p-3" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.10);border-radius:16px;">
                    <strong>Je n’ai pas reçu mon colis</strong>
<p style="margin:6px 0 0;color:var(--text-soft);">Vérifiez d’abord le statut dans <a href="suivi.php" style="color:#075fc7;text-decoration:none;"><b>Suivi</b></a>, puis contactez-nous via le formulaire.</p>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="p-3" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.10);border-radius:16px;">
                    <strong>Paiement refusé</strong>
<p style="margin:6px 0 0;color:var(--text-soft);">Consultez le statut dans <a href="historique.php" style="color:#075fc7;text-decoration:none;"><b>Historique</b></a> puis réessayez ou contactez-nous.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-card" style="margin-top:18px;padding:22px;border-radius:22px;border:1px solid rgba(148,163,184,.25);background:rgba(255,255,255,.85);">
        <h5 style="margin:0 0 10px;font-weight:900;color:#075fc7;">Contact</h5>
        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="p-3" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.10);border-radius:16px;">
                    <strong>Téléphone</strong>
                    <p style="margin:6px 0 0;color:var(--text-soft);">+33 6 12 34 56 78</p>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="p-3" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.10);border-radius:16px;">
                    <strong>Email</strong>
                    <p style="margin:6px 0 0;color:var(--text-soft);">support@nocibe.fr</p>
                </div>
            </div>
        </div>

    </div>
</section>

</body>
</html>

