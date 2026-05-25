<?php
require_once __DIR__ . '/includes/auth_check.php';
requireRole('client');
require_once __DIR__ . '/config/database.php';
$client = currentUser();

// $pdo est initialisé dans config/database.php via Database::getConnection()
// (si votre IDE le signale comme indéfini, c'est uniquement un souci de type statique).

// config/database.php expose une connexion PDO via Database::getConnection()
$pdo = $pdo ?? null;
if (!$pdo) {
    $pdo = Database::getConnection();
}


$clientId = (int)($client['id_user'] ?? ($client['id_client'] ?? 0));

// Si l'id client n'est pas trouvé en session, on affiche un message propre.
$statsError = null;
if ($clientId <= 0) {
    $statsError = 'Impossible d’identifier votre compte (id client manquant).';
}

$stats = [
    'nb_commandes' => 0,
    'nb_livraisons' => 0,
    'depenses' => 0.0,
    'commande_encours' => 0,
    'livraison_encours' => 0,
    'dernieres_commandes' => [],
];

if ($statsError === null) {
    // Commandes du client
    $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM commande WHERE id_client = ?");
    $stmt->execute([$clientId]);
    $stats['nb_commandes'] = (int)($stmt->fetch()['c'] ?? 0);

    // Livraisons du client (table livraison contient id_user)
    $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM livraison WHERE id_user = ?");
    $stmt->execute([$clientId]);
    $stats['nb_livraisons'] = (int)($stmt->fetch()['c'] ?? 0);

    // Dépenses: somme montant_total des commandes
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(montant_total),0) AS s FROM commande WHERE id_client = ?");
    $stmt->execute([$clientId]);
    $stats['depenses'] = (float)($stmt->fetch()['s'] ?? 0);

    // Encours: commandes pas terminées (statut variable selon ton application)
    $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM commande WHERE id_client = ? AND statut_commande NOT IN ('Livrée','Terminee','Terminée')");
    $stmt->execute([$clientId]);
    $stats['commande_encours'] = (int)($stmt->fetch()['c'] ?? 0);

    // Encours livraison
    $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM livraison WHERE id_user = ? AND statut_livraison NOT IN ('Livrée','Terminee','Terminée','Livree')");
    $stmt->execute([$clientId]);
    $stats['livraison_encours'] = (int)($stmt->fetch()['c'] ?? 0);

    // Dernières commandes
    $stmt = $pdo->prepare("SELECT id_commande, date_commande, statut_commande, montant_total
                             FROM commande
                             WHERE id_client = ?
                             ORDER BY date_commande DESC
                             LIMIT 5");
    $stmt->execute([$clientId]);
    $stats['dernieres_commandes'] = $stmt->fetchAll();
}

$dashboardTitle = 'Statistiques';
$dashboardLead = 'Visualisez vos commandes, livraisons et dépenses en temps réel.';
include __DIR__ . '/backoffice/includes/header.php';
?>

<section style="padding-top:14px;">
    <?php if ($statsError): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($statsError) ?></div>
    <?php else: ?>
        <div class="client-dashboard-stats" style="margin-top:0;">
            <div class="glass-card" style="padding:22px;border-radius:22px;border:1px solid rgba(148,163,184,.25);background:rgba(255,255,255,.85);">
                <h5 style="margin:0 0 8px;font-weight:800;color:#075fc7;">Commandes</h5>
                <div style="font-size:1.8rem;font-weight:900;">#<?= (int)$stats['nb_commandes'] ?></div>
                <p style="margin:8px 0 0;color:var(--text-soft);">Encours: <?= (int)$stats['commande_encours'] ?></p>
            </div>

            <div class="glass-card" style="padding:22px;border-radius:22px;border:1px solid rgba(148,163,184,.25);background:rgba(255,255,255,.85);">
                <h5 style="margin:0 0 8px;font-weight:800;color:#075fc7;">Livraisons</h5>
                <div style="font-size:1.8rem;font-weight:900;">#<?= (int)$stats['nb_livraisons'] ?></div>
                <p style="margin:8px 0 0;color:var(--text-soft);">Encours: <?= (int)$stats['livraison_encours'] ?></p>
            </div>

            <div class="glass-card" style="padding:22px;border-radius:22px;border:1px solid rgba(148,163,184,.25);background:rgba(255,255,255,.85);">
                <h5 style="margin:0 0 8px;font-weight:800;color:#075fc7;">Dépenses</h5>
                <div style="font-size:1.8rem;font-weight:900;"><?= number_format($stats['depenses'], 2, ',', ' ') ?> €</div>
                <p style="margin:8px 0 0;color:var(--text-soft);">Somme des montants totaux</p>
            </div>
        </div>

        <div class="glass-card" style="margin-top:18px;padding:22px;border-radius:22px;border:1px solid rgba(148,163,184,.25);background:rgba(255,255,255,.85);">
            <h5 style="margin:0 0 12px;font-weight:900;color:#075fc7;">Dernières commandes</h5>
            <?php if (empty($stats['dernieres_commandes'])): ?>
                <div class="text-muted">Aucune commande trouvée.</div>
            <?php else: ?>
                <div style="overflow:auto;">
                    <table class="table table-striped" style="margin:0;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Montant</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($stats['dernieres_commandes'] as $c): ?>
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

    <?php endif; ?>
</section>

</body>
</html>

