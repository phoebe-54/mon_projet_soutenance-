<?php
require_once __DIR__ . '/includes/auth_check.php';
requireRole('client');
require_once __DIR__ . '/config/database.php';

$client = currentUser();
$clientId = (int)($client['id_user'] ?? ($client['id_client'] ?? 0));

$err = null;
if ($clientId <= 0) {
    $err = 'Impossible d’identifier votre compte (id client manquant).';
}

$commandes = [];
$factures = []; // ici on considère les paiements comme “factures”

if ($err === null) {
    $stmt = $pdo->prepare("SELECT id_commande, date_commande, statut_commande, montant_total
                           FROM commande
                           WHERE id_client = ?
                           ORDER BY date_commande DESC");
    $stmt->execute([$clientId]);
    $commandes = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT p.id_paiement, p.date_paiement, p.mode_paiement, p.statut_paiement,
                                   c.id_commande, c.montant_total
                             FROM paiement p
                             JOIN commande c ON c.id_commande = p.id_commande
                             WHERE c.id_client = ?
                             ORDER BY p.date_paiement DESC");
    $stmt->execute([$clientId]);
    $factures = $stmt->fetchAll();
}

$dashboardTitle = 'Historique';
$dashboardLead = 'Consultez vos achats passés et vos factures.';
include __DIR__ . '/backoffice/includes/header.php';
?>

<section style="padding-top:14px;">
    <?php if ($err): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php else: ?>
        <div class="glass-card" style="padding:22px;border-radius:22px;border:1px solid rgba(148,163,184,.25);background:rgba(255,255,255,.85);">
            <h5 style="margin:0 0 12px;font-weight:900;color:#075fc7;">Achats passés</h5>
            <?php if (empty($commandes)): ?>
                <div class="text-muted">Aucun achat trouvé.</div>
            <?php else: ?>
                <div style="overflow:auto;">
                    <table class="table table-striped" style="margin:0;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Statut commande</th>
                            <th>Montant</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($commandes as $c): ?>
                            <tr>
                                <td><?= (int)$c['id_commande'] ?></td>
                                <td><?= htmlspecialchars($c['date_commande']) ?></td>
                                <td><?= htmlspecialchars($c['statut_commande']) ?></td>
                                <td><?= number_format((float)$c['montant_total'], 2, ',', ' ') ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="glass-card" style="margin-top:18px;padding:22px;border-radius:22px;border:1px solid rgba(148,163,184,.25);background:rgba(255,255,255,.85);">
            <h5 style="margin:0 0 12px;font-weight:900;color:#075fc7;">Factures (paiements)</h5>
            <?php if (empty($factures)): ?>
                <div class="text-muted">Aucune facture trouvée.</div>
            <?php else: ?>
                <div style="overflow:auto;">
                    <table class="table table-striped" style="margin:0;">
                        <thead>
                        <tr>
                            <th>ID paiement</th>
                            <th>Date</th>
                            <th>Mode</th>
                            <th>Statut paiement</th>
                            <th>Montant</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($factures as $f): ?>
                            <tr>
                                <td><?= (int)$f['id_paiement'] ?></td>
                                <td><?= htmlspecialchars($f['date_paiement']) ?></td>
                                <td><?= htmlspecialchars($f['mode_paiement']) ?></td>
                                <td><?= htmlspecialchars($f['statut_paiement']) ?></td>
                                <td><?= number_format((float)$f['montant_total'], 2, ',', ' ') ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

</body>
</html>

