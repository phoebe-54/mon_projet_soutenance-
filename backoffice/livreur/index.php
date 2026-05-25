<?php
require_once '../../includes/auth_check.php';
$currentUser = requireRole('livreur');
require_once '../../config/database.php';

function ensureColumn(PDO $pdo, string $table, string $column, string $definition): void
{
    $stmt = $pdo->prepare(
        'SELECT 1
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = :table_name
           AND COLUMN_NAME = :column_name
         LIMIT 1'
    );
    $stmt->execute([
        'table_name' => $table,
        'column_name' => $column,
    ]);

    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE {$table} ADD {$column} {$definition}");
    }
}

function redirectWithFlash(string $type, string $message, string $section = ''): void
{
    $fragment = preg_replace('/[^a-zA-Z0-9_-]/', '', $section);
    header('Location: index.php?' . http_build_query([$type => $message]) . ($fragment ? '#' . $fragment : ''));
    exit;
}

function detailsFor(array $detailsByOrder, int $orderId): array
{
    return $detailsByOrder[$orderId] ?? [];
}

ensureColumn($pdo, 'livraison', 'probleme_livraison', 'TEXT NULL');
ensureColumn($pdo, 'livraison', 'date_probleme', 'DATETIME NULL');

$driverId = (int) $currentUser['id_user'];
$message = trim($_GET['success'] ?? '');
$error = trim($_GET['error'] ?? '');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'take_delivery') {
            $deliveryId = (int) ($_POST['id_livraison'] ?? 0);
            $stmt = $pdo->prepare(
                "UPDATE livraison
                 SET statut_livraison = 'En cours'
                 WHERE id_livraison = :id_livraison
                   AND id_user = :id_user
                   AND statut_livraison IN ('Expediee', 'Assignee', 'En attente')"
            );
            $stmt->execute([
                'id_livraison' => $deliveryId,
                'id_user' => $driverId,
            ]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Cette livraison ne peut pas etre prise en charge.');
            }

            $pdo->prepare(
                'UPDATE commande c
                 JOIN livraison l ON l.id_commande = c.id_commande
                 SET c.statut_commande = :statut
                 WHERE l.id_livraison = :id_livraison'
            )->execute([
                'statut' => 'En livraison',
                'id_livraison' => $deliveryId,
            ]);

            redirectWithFlash('success', 'Livraison prise en charge.', 'livraisons');
        }

        if ($action === 'finish_delivery') {
            $deliveryId = (int) ($_POST['id_livraison'] ?? 0);
            $stmt = $pdo->prepare(
                "UPDATE livraison
                 SET statut_livraison = 'Livree', date_livraison = NOW()
                 WHERE id_livraison = :id_livraison
                   AND id_user = :id_user
                   AND statut_livraison = 'En cours'"
            );
            $stmt->execute([
                'id_livraison' => $deliveryId,
                'id_user' => $driverId,
            ]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('La livraison doit etre en cours avant validation.');
            }

            $pdo->prepare(
                'UPDATE commande c
                 JOIN livraison l ON l.id_commande = c.id_commande
                 SET c.statut_commande = :statut
                 WHERE l.id_livraison = :id_livraison'
            )->execute([
                'statut' => 'Livree',
                'id_livraison' => $deliveryId,
            ]);

            redirectWithFlash('success', 'Livraison marquee comme livree.', 'livraisons');
        }

        if ($action === 'report_problem') {
            $deliveryId = (int) ($_POST['id_livraison'] ?? 0);
            $problemType = trim($_POST['type_probleme'] ?? '');
            $problemText = trim($_POST['probleme_livraison'] ?? '');

            if ($deliveryId <= 0 || $problemText === '') {
                throw new RuntimeException('Veuillez choisir une livraison et decrire le probleme.');
            }

            $stmt = $pdo->prepare(
                'UPDATE livraison
                 SET probleme_livraison = :probleme_livraison, date_probleme = NOW()
                 WHERE id_livraison = :id_livraison AND id_user = :id_user'
            );
            $stmt->execute([
                'probleme_livraison' => trim($problemType . ' - ' . $problemText, ' -'),
                'id_livraison' => $deliveryId,
                'id_user' => $driverId,
            ]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Livraison introuvable pour ce livreur.');
            }

            redirectWithFlash('success', 'Probleme signale.', 'problemes');
        }
    } catch (Throwable $exception) {
        redirectWithFlash('error', 'Operation impossible : ' . $exception->getMessage(), $_POST['redirect_section'] ?? '');
    }
}

$deliveriesStmt = $pdo->prepare(
    "SELECT l.*, c.date_commande, c.montant_total, c.statut_commande,
            u.nom, u.prenom, u.email, u.telephone, cl.adresse AS adresse_client
     FROM livraison l
     JOIN commande c ON c.id_commande = l.id_commande
     JOIN utilisateur u ON u.id_user = c.id_client
     LEFT JOIN client cl ON cl.id_user = c.id_client
     WHERE l.id_user = :id_user
     ORDER BY FIELD(l.statut_livraison, 'Expediee', 'En cours', 'Assignee', 'En attente', 'Livree', 'Annulee'), l.id_livraison DESC"
);
$deliveriesStmt->execute(['id_user' => $driverId]);
$deliveries = $deliveriesStmt->fetchAll();

$detailsRows = $pdo->query(
    'SELECT lc.id_commande, lc.quantite, p.nom_produit
     FROM ligne_commande lc
     JOIN produit p ON p.id_produit = lc.id_produit
     ORDER BY lc.id_commande DESC, p.nom_produit'
)->fetchAll();
$detailsByOrder = [];
foreach ($detailsRows as $detail) {
    $detailsByOrder[(int) $detail['id_commande']][] = $detail;
}

$assignedDeliveries = array_values(array_filter($deliveries, static fn (array $delivery): bool => !in_array($delivery['statut_livraison'], ['Livree', 'Annulee'], true)));
$activeDeliveries = array_values(array_filter($deliveries, static fn (array $delivery): bool => $delivery['statut_livraison'] === 'En cours'));
$deliveredDeliveries = array_values(array_filter($deliveries, static fn (array $delivery): bool => $delivery['statut_livraison'] === 'Livree'));
$problemDeliveries = array_values(array_filter($deliveries, static fn (array $delivery): bool => trim((string) ($delivery['probleme_livraison'] ?? '')) !== ''));

$dashboardTitle = 'Espace livreur';
$dashboardLead = 'Consultez vos livraisons assignees, prenez-les en charge et mettez a jour leur statut.';
include '../includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<section class="dashboard-summary-grid admin-dashboard-grid" id="dashboard">
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-truck-fast"></i><strong><?= count($assignedDeliveries) ?></strong><span>Livraisons assignees</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-route"></i><strong><?= count($activeDeliveries) ?></strong><span>En cours</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-circle-check"></i><strong><?= count($deliveredDeliveries) ?></strong><span>Livrees</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-triangle-exclamation"></i><strong><?= count($problemDeliveries) ?></strong><span>Problemes</span></article>
</section>

<section class="dashboard-section admin-section" id="livraisons">
    <article class="panel-card">
        <h2>Livraisons assignees</h2>
        <div class="table-shell">
            <table class="table-modern">
                <thead><tr><th>Livraison</th><th>Adresse</th><th>Client</th><th>Produits</th><th>Action</th></tr></thead>
                <tbody>
                <?php if (!$deliveries): ?>
                    <tr><td colspan="5"><div class="empty-state">Aucune livraison dans votre espace.</div></td></tr>
                <?php endif; ?>
                <?php foreach ($deliveries as $delivery): ?>
                    <tr>
                        <td>#<?= (int) $delivery['id_livraison'] ?><br><small>Commande #<?= (int) $delivery['id_commande'] ?></small><br><span class="status-pill status-pill-admin"><?= htmlspecialchars($delivery['statut_livraison']) ?></span></td>
                        <td><?= htmlspecialchars($delivery['adresse_livraison']) ?></td>
                        <td><?= htmlspecialchars(trim(($delivery['prenom'] ?? '') . ' ' . $delivery['nom'])) ?><br><small><?= htmlspecialchars($delivery['email']) ?></small><br><small><?= htmlspecialchars($delivery['telephone'] ?? '') ?></small></td>
                        <td>
                            <?php foreach (detailsFor($detailsByOrder, (int) $delivery['id_commande']) as $detail): ?>
                                <div><?= htmlspecialchars($detail['nom_produit']) ?> x <?= (int) $detail['quantite'] ?></div>
                            <?php endforeach; ?>
                        </td>
                        <td class="row-actions">
                            <?php if (in_array($delivery['statut_livraison'], ['Expediee', 'Assignee', 'En attente'], true)): ?>
                                <form method="POST">
                                    <input type="hidden" name="action" value="take_delivery">
                                    <input type="hidden" name="redirect_section" value="livraisons">
                                    <input type="hidden" name="id_livraison" value="<?= (int) $delivery['id_livraison'] ?>">
                                    <button class="btn btn-brand" type="submit">Prendre en charge</button>
                                </form>
                            <?php elseif ($delivery['statut_livraison'] === 'En cours'): ?>
                                <form method="POST">
                                    <input type="hidden" name="action" value="finish_delivery">
                                    <input type="hidden" name="redirect_section" value="livraisons">
                                    <input type="hidden" name="id_livraison" value="<?= (int) $delivery['id_livraison'] ?>">
                                    <button class="btn btn-brand" type="submit">Marquer livree</button>
                                </form>
                            <?php else: ?>
                                <span class="status-pill status-pill-admin">Terminee</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<section class="dashboard-section admin-section admin-stack-section" id="problemes">
    <article class="panel-card">
        <h2>Signaler un probleme</h2>
        <form class="compact-form" method="POST">
            <input type="hidden" name="action" value="report_problem">
            <input type="hidden" name="redirect_section" value="problemes">
            <select class="form-select" name="id_livraison" required>
                <option value="">Livraison</option>
                <?php foreach ($assignedDeliveries as $delivery): ?>
                    <option value="<?= (int) $delivery['id_livraison'] ?>">#<?= (int) $delivery['id_livraison'] ?> - Commande #<?= (int) $delivery['id_commande'] ?></option>
                <?php endforeach; ?>
            </select>
            <select class="form-select" name="type_probleme" required>
                <option value="Retard">Retard</option>
                <option value="Client absent">Client absent</option>
                <option value="Mauvaise adresse">Mauvaise adresse</option>
                <option value="Autre">Autre</option>
            </select>
            <textarea class="form-control" name="probleme_livraison" placeholder="Description du probleme" required></textarea>
            <button class="btn btn-brand" type="submit">Signaler</button>
        </form>
    </article>

    <article class="panel-card">
        <h2>Problemes signales</h2>
        <?php if (!$problemDeliveries): ?>
            <div class="empty-state">Aucun probleme signale.</div>
        <?php else: ?>
            <ul class="mini-list">
                <?php foreach ($problemDeliveries as $delivery): ?>
                    <li>
                        <span>#<?= (int) $delivery['id_livraison'] ?> - <?= htmlspecialchars($delivery['probleme_livraison']) ?></span>
                        <strong><?= htmlspecialchars($delivery['date_probleme'] ?? '') ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>
</section>

        </main>
    </div>
</body>
</html>
