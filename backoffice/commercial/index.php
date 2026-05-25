<?php
require_once '../../includes/auth_check.php';
requireRole('commercial');
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

$message = trim($_GET['success'] ?? '');
$error = trim($_GET['error'] ?? '');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create_delivery') {
            $orderId = (int) ($_POST['id_commande'] ?? 0);
            $driverId = (int) ($_POST['id_user'] ?? 0);
            $address = trim($_POST['adresse_livraison'] ?? '');

            if ($orderId <= 0 || $driverId <= 0 || $address === '') {
                throw new RuntimeException('Commande, livreur ou adresse invalide.');
            }

            $exists = $pdo->prepare('SELECT id_livraison FROM livraison WHERE id_commande = :id_commande LIMIT 1');
            $exists->execute(['id_commande' => $orderId]);

            if ($exists->fetchColumn()) {
                throw new RuntimeException('Une livraison existe deja pour cette commande.');
            }

            $pdo->beginTransaction();
            $pdo->prepare(
                'INSERT INTO livraison (adresse_livraison, statut_livraison, id_commande, id_user)
                 VALUES (:adresse_livraison, :statut_livraison, :id_commande, :id_user)'
            )->execute([
                'adresse_livraison' => $address,
                'statut_livraison' => 'En preparation',
                'id_commande' => $orderId,
                'id_user' => $driverId,
            ]);
            $pdo->prepare('UPDATE commande SET statut_commande = :statut WHERE id_commande = :id_commande')->execute([
                'statut' => 'En preparation',
                'id_commande' => $orderId,
            ]);
            $pdo->commit();

            redirectWithFlash('success', 'Livraison creee et assignee au livreur.', 'commandes');
        }

        if ($action === 'update_delivery_status') {
            $deliveryId = (int) ($_POST['id_livraison'] ?? 0);
            $status = $_POST['statut_livraison'] ?? 'En preparation';

            if (!in_array($status, ['En preparation', 'Expediee'], true)) {
                throw new RuntimeException('Statut non autorise.');
            }

            $pdo->beginTransaction();
            $stmt = $pdo->prepare('UPDATE livraison SET statut_livraison = :statut WHERE id_livraison = :id_livraison');
            $stmt->execute([
                'statut' => $status,
                'id_livraison' => $deliveryId,
            ]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Livraison introuvable.');
            }

            $orderStatus = $status === 'Expediee' ? 'En livraison' : 'En preparation';
            $pdo->prepare(
                'UPDATE commande c
                 JOIN livraison l ON l.id_commande = c.id_commande
                 SET c.statut_commande = :statut
                 WHERE l.id_livraison = :id_livraison'
            )->execute([
                'statut' => $orderStatus,
                'id_livraison' => $deliveryId,
            ]);
            $pdo->commit();

            redirectWithFlash('success', 'Statut de livraison mis a jour.', 'preparation');
        }

        if ($action === 'report_problem') {
            $deliveryId = (int) ($_POST['id_livraison'] ?? 0);
            $problemType = trim($_POST['type_probleme'] ?? '');
            $problemText = trim($_POST['probleme_livraison'] ?? '');

            if ($deliveryId <= 0 || $problemText === '') {
                throw new RuntimeException('Veuillez choisir une livraison et decrire le probleme.');
            }

            $pdo->prepare(
                'UPDATE livraison
                 SET probleme_livraison = :probleme_livraison, date_probleme = NOW()
                 WHERE id_livraison = :id_livraison'
            )->execute([
                'probleme_livraison' => trim($problemType . ' - ' . $problemText, ' -'),
                'id_livraison' => $deliveryId,
            ]);

            redirectWithFlash('success', 'Probleme signale.', 'problemes');
        }
    } catch (Throwable $exception) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        redirectWithFlash('error', 'Operation impossible : ' . $exception->getMessage(), $_POST['redirect_section'] ?? '');
    }
}

$confirmedStatuses = ['Confirmee', 'Validee'];
$placeholders = implode(',', array_fill(0, count($confirmedStatuses), '?'));
$confirmedStmt = $pdo->prepare(
    "SELECT c.*, u.nom, u.prenom, u.email, u.telephone, cl.adresse AS adresse_client,
            l.id_livraison, l.statut_livraison, l.adresse_livraison,
            du.nom AS livreur_nom, du.prenom AS livreur_prenom
     FROM commande c
     JOIN utilisateur u ON u.id_user = c.id_client
     LEFT JOIN client cl ON cl.id_user = c.id_client
     LEFT JOIN livraison l ON l.id_commande = c.id_commande
     LEFT JOIN utilisateur du ON du.id_user = l.id_user
     WHERE c.statut_commande IN ({$placeholders})
     ORDER BY c.date_commande DESC"
);
$confirmedStmt->execute($confirmedStatuses);
$confirmedOrders = $confirmedStmt->fetchAll();

$deliveries = $pdo->query(
    "SELECT l.*, c.montant_total, c.statut_commande,
            u.nom, u.prenom, u.email, u.telephone,
            du.nom AS livreur_nom, du.prenom AS livreur_prenom
     FROM livraison l
     JOIN commande c ON c.id_commande = l.id_commande
     JOIN utilisateur u ON u.id_user = c.id_client
     JOIN utilisateur du ON du.id_user = l.id_user
     ORDER BY FIELD(l.statut_livraison, 'En preparation', 'Expediee', 'En cours', 'Livree', 'Annulee'), l.id_livraison DESC"
)->fetchAll();

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

$drivers = $pdo->query('SELECT u.id_user, u.nom, u.prenom FROM livreur l JOIN utilisateur u ON u.id_user = l.id_user ORDER BY u.nom')->fetchAll();
$inPreparation = array_values(array_filter($deliveries, static fn (array $delivery): bool => in_array($delivery['statut_livraison'], ['En preparation', 'Expediee'], true)));
$inProgress = array_values(array_filter($deliveries, static fn (array $delivery): bool => $delivery['statut_livraison'] === 'En cours'));
$problemDeliveries = array_values(array_filter($deliveries, static fn (array $delivery): bool => trim((string) ($delivery['probleme_livraison'] ?? '')) !== ''));

$dashboardTitle = 'Espace commercial';
$dashboardLead = 'Traitez les commandes confirmees, preparez les expeditions et suivez les livraisons.';
include '../includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<section class="dashboard-summary-grid admin-dashboard-grid" id="dashboard">
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-receipt"></i><strong><?= count($confirmedOrders) ?></strong><span>Commandes confirmees</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-box-open"></i><strong><?= count($inPreparation) ?></strong><span>En preparation / expediees</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-route"></i><strong><?= count($inProgress) ?></strong><span>Livraisons en cours</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-triangle-exclamation"></i><strong><?= count($problemDeliveries) ?></strong><span>Problemes signales</span></article>
</section>

<section class="dashboard-section admin-section" id="commandes">
    <article class="panel-card">
        <h2>Commandes confirmees a traiter</h2>
        <div class="table-shell">
            <table class="table-modern">
                <thead><tr><th>Commande</th><th>Client</th><th>Produits</th><th>Livraison</th><th>Action</th></tr></thead>
                <tbody>
                <?php if (!$confirmedOrders): ?>
                    <tr><td colspan="5"><div class="empty-state">Aucune commande confirmee a traiter.</div></td></tr>
                <?php endif; ?>
                <?php foreach ($confirmedOrders as $order): ?>
                    <?php $address = trim((string) ($order['adresse_livraison'] ?: ($order['adresse_client'] ?? ''))); ?>
                    <tr>
                        <td>#<?= (int) $order['id_commande'] ?><br><small><?= htmlspecialchars($order['date_commande']) ?></small><br><strong><?= number_format((float) $order['montant_total'], 0, ',', ' ') ?> FCFA</strong></td>
                        <td><?= htmlspecialchars(trim(($order['prenom'] ?? '') . ' ' . $order['nom'])) ?><br><small><?= htmlspecialchars($order['email']) ?></small><br><small><?= htmlspecialchars($order['telephone'] ?? '') ?></small></td>
                        <td>
                            <?php foreach (detailsFor($detailsByOrder, (int) $order['id_commande']) as $detail): ?>
                                <div><?= htmlspecialchars($detail['nom_produit']) ?> x <?= (int) $detail['quantite'] ?></div>
                            <?php endforeach; ?>
                        </td>
                        <td><span class="status-pill status-pill-admin"><?= htmlspecialchars($order['statut_livraison'] ?? 'A creer') ?></span><br><small><?= htmlspecialchars($address ?: 'Adresse non renseignee') ?></small></td>
                        <td>
                            <?php if (empty($order['id_livraison'])): ?>
                                <form class="compact-form" method="POST">
                                    <input type="hidden" name="action" value="create_delivery">
                                    <input type="hidden" name="redirect_section" value="commandes">
                                    <input type="hidden" name="id_commande" value="<?= (int) $order['id_commande'] ?>">
                                    <input class="form-control" name="adresse_livraison" value="<?= htmlspecialchars($address) ?>" placeholder="Adresse de livraison" required>
                                    <select class="form-select" name="id_user" required>
                                        <option value="">Livreur</option>
                                        <?php foreach ($drivers as $driver): ?>
                                            <option value="<?= (int) $driver['id_user'] ?>"><?= htmlspecialchars(trim(($driver['prenom'] ?? '') . ' ' . $driver['nom'])) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-brand" type="submit">Creer livraison</button>
                                </form>
                            <?php else: ?>
                                <span><?= htmlspecialchars(trim(($order['livreur_prenom'] ?? '') . ' ' . ($order['livreur_nom'] ?? ''))) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<section class="dashboard-section admin-section" id="preparation">
    <article class="panel-card">
        <h2>Preparation et expedition</h2>
        <div class="table-shell">
            <table class="table-modern">
                <thead><tr><th>Livraison</th><th>Client</th><th>Livreur</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                <?php if (!$inPreparation): ?>
                    <tr><td colspan="5"><div class="empty-state">Aucune livraison en preparation.</div></td></tr>
                <?php endif; ?>
                <?php foreach ($inPreparation as $delivery): ?>
                    <tr>
                        <td>#<?= (int) $delivery['id_livraison'] ?><br><small>Commande #<?= (int) $delivery['id_commande'] ?></small><br><small><?= htmlspecialchars($delivery['adresse_livraison']) ?></small></td>
                        <td><?= htmlspecialchars(trim(($delivery['prenom'] ?? '') . ' ' . $delivery['nom'])) ?><br><small><?= htmlspecialchars($delivery['telephone'] ?? '') ?></small></td>
                        <td><?= htmlspecialchars(trim(($delivery['livreur_prenom'] ?? '') . ' ' . ($delivery['livreur_nom'] ?? ''))) ?></td>
                        <td><span class="status-pill status-pill-admin"><?= htmlspecialchars($delivery['statut_livraison']) ?></span></td>
                        <td>
                            <form class="inline-form" method="POST">
                                <input type="hidden" name="action" value="update_delivery_status">
                                <input type="hidden" name="redirect_section" value="preparation">
                                <input type="hidden" name="id_livraison" value="<?= (int) $delivery['id_livraison'] ?>">
                                <select class="form-select" name="statut_livraison">
                                    <?php foreach (['En preparation', 'Expediee'] as $status): ?>
                                        <option value="<?= htmlspecialchars($status) ?>" <?= $delivery['statut_livraison'] === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="icon-btn" type="submit" title="Mettre a jour"><i class="fa-solid fa-check"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<section class="dashboard-section admin-section admin-stack-section" id="suivi">
    <article class="panel-card">
        <h2>Livraisons en cours</h2>
        <?php if (!$inProgress): ?>
            <div class="empty-state">Aucune livraison en cours.</div>
        <?php else: ?>
            <ul class="mini-list">
                <?php foreach ($inProgress as $delivery): ?>
                    <li>
                        <span>#<?= (int) $delivery['id_livraison'] ?> - <?= htmlspecialchars($delivery['adresse_livraison']) ?></span>
                        <strong><?= htmlspecialchars(trim(($delivery['livreur_prenom'] ?? '') . ' ' . ($delivery['livreur_nom'] ?? ''))) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>

    <article class="panel-card" id="problemes">
        <h2>Signaler un probleme</h2>
        <form class="compact-form" method="POST">
            <input type="hidden" name="action" value="report_problem">
            <input type="hidden" name="redirect_section" value="problemes">
            <select class="form-select" name="id_livraison" required>
                <option value="">Livraison</option>
                <?php foreach ($deliveries as $delivery): ?>
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
</section>

        </main>
    </div>
</body>
</html>
