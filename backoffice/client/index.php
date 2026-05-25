<?php
require_once '../../includes/auth_check.php';
$currentUser = requireRole('client');
require_once '../../config/database.php';

$clientId = (int) $currentUser['id_user'];
$message = '';
$error = '';

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function ensureClientColumn(PDO $pdo, string $table, string $column, string $definition): void
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

function cartTotal(array $cart, array $productsById): float
{
    $total = 0;
    foreach ($cart as $productId => $quantity) {
        if (isset($productsById[$productId])) {
            $total += (float) $productsById[$productId]['prix'] * (int) $quantity;
        }
    }

    return $total;
}

ensureClientColumn($pdo, 'produit', 'image_url', 'VARCHAR(255) NULL');

$products = $pdo->query('SELECT p.*, c.nom_categorie FROM produit p LEFT JOIN categorie c ON c.id_categorie = p.id_categorie ORDER BY p.nom_produit')->fetchAll();
$productsById = [];
foreach ($products as $product) {
    $productsById[(int) $product['id_produit']] = $product;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add_to_cart') {
            $productId = (int) ($_POST['id_produit'] ?? 0);
            $quantity = max(1, (int) ($_POST['quantite'] ?? 1));

            if (!isset($productsById[$productId])) {
                throw new RuntimeException('Produit introuvable.');
            }

            $_SESSION['cart'][$productId] = (int) ($_SESSION['cart'][$productId] ?? 0) + $quantity;
            $message = 'Produit ajoute au panier.';
        } elseif ($action === 'update_cart') {
            foreach ($_POST['quantites'] ?? [] as $productId => $quantity) {
                $productId = (int) $productId;
                $quantity = (int) $quantity;

                if ($quantity <= 0) {
                    unset($_SESSION['cart'][$productId]);
                } else {
                    $_SESSION['cart'][$productId] = $quantity;
                }
            }
            $message = 'Panier mis a jour.';
        } elseif ($action === 'remove_cart') {
            unset($_SESSION['cart'][(int) ($_POST['id_produit'] ?? 0)]);
            $message = 'Article supprime du panier.';
        } elseif ($action === 'checkout') {
            if (empty($_SESSION['cart'])) {
                throw new RuntimeException('Votre panier est vide.');
            }

            $pdo->beginTransaction();
            $total = cartTotal($_SESSION['cart'], $productsById);

            $stmt = $pdo->prepare('INSERT INTO commande (statut_commande, montant_total, id_client) VALUES (:statut, :montant_total, :id_client)');
            $stmt->execute([
                'statut' => 'En attente',
                'montant_total' => $total,
                'id_client' => $clientId,
            ]);
            $orderId = (int) $pdo->lastInsertId();

            $lineStmt = $pdo->prepare('INSERT INTO ligne_commande (quantite, prix, sous_total, id_commande, id_produit) VALUES (:quantite, :prix, :sous_total, :id_commande, :id_produit)');
            $stockStmt = $pdo->prepare('UPDATE produit SET quantite_stock = GREATEST(quantite_stock - :quantite, 0) WHERE id_produit = :id_produit');

            foreach ($_SESSION['cart'] as $productId => $quantity) {
                if (!isset($productsById[$productId])) {
                    continue;
                }

                $price = (float) $productsById[$productId]['prix'];
                $lineStmt->execute([
                    'quantite' => (int) $quantity,
                    'prix' => $price,
                    'sous_total' => $price * (int) $quantity,
                    'id_commande' => $orderId,
                    'id_produit' => (int) $productId,
                ]);
                $stockStmt->execute([
                    'quantite' => (int) $quantity,
                    'id_produit' => (int) $productId,
                ]);
            }

            $deliveryUserId = (int) ($pdo->query('SELECT id_user FROM livreur LIMIT 1')->fetchColumn() ?: $clientId);
            $address = trim($_POST['adresse_livraison'] ?? '') ?: 'Adresse a confirmer';
            $pdo->prepare('INSERT INTO livraison (adresse_livraison, statut_livraison, id_commande, id_user) VALUES (:adresse, :statut, :id_commande, :id_user)')->execute([
                'adresse' => $address,
                'statut' => 'En attente',
                'id_commande' => $orderId,
                'id_user' => $deliveryUserId,
            ]);

            $_SESSION['cart'] = [];
            $pdo->commit();
            $message = 'Commande #' . $orderId . ' enregistree.';
        } elseif ($action === 'pay_order') {
            $orderId = (int) ($_POST['id_commande'] ?? 0);
            $mode = $_POST['mode_paiement'] ?? 'FedaPay - Mobile Money';

            $stmt = $pdo->prepare('SELECT id_commande FROM commande WHERE id_commande = :id_commande AND id_client = :id_client LIMIT 1');
            $stmt->execute(['id_commande' => $orderId, 'id_client' => $clientId]);
            if (!$stmt->fetchColumn()) {
                throw new RuntimeException('Commande introuvable.');
            }

            $pdo->prepare('INSERT INTO paiement (mode_paiement, statut_paiement, id_commande) VALUES (:mode, :statut, :id_commande)')->execute([
                'mode' => $mode,
                'statut' => 'Paye',
                'id_commande' => $orderId,
            ]);
            $pdo->prepare("UPDATE commande SET statut_commande = 'Confirmee' WHERE id_commande = :id_commande")->execute(['id_commande' => $orderId]);
            $message = 'Paiement FedaPay enregistre.';
        }
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = 'Operation impossible : ' . $exception->getMessage();
    }
}

$statsStmt = $pdo->prepare("SELECT COUNT(*) orders_count, COALESCE(SUM(montant_total), 0) orders_total FROM commande WHERE id_client = :id_client");
$statsStmt->execute(['id_client' => $clientId]);
$stats = $statsStmt->fetch() ?: ['orders_count' => 0, 'orders_total' => 0];

$paymentsStmt = $pdo->prepare('SELECT COUNT(*) FROM paiement p JOIN commande c ON c.id_commande = p.id_commande WHERE c.id_client = :id_client');
$paymentsStmt->execute(['id_client' => $clientId]);
$paymentCount = (int) $paymentsStmt->fetchColumn();

$deliveriesStmt = $pdo->prepare('SELECT COUNT(*) FROM livraison l JOIN commande c ON c.id_commande = l.id_commande WHERE c.id_client = :id_client AND l.statut_livraison NOT IN ("Livree", "Annulee")');
$deliveriesStmt->execute(['id_client' => $clientId]);
$deliveryCount = (int) $deliveriesStmt->fetchColumn();

$ordersStmt = $pdo->prepare('SELECT * FROM commande WHERE id_client = :id_client ORDER BY date_commande DESC');
$ordersStmt->execute(['id_client' => $clientId]);
$orders = $ordersStmt->fetchAll();

$detailsStmt = $pdo->prepare('SELECT lc.*, p.nom_produit FROM ligne_commande lc JOIN produit p ON p.id_produit = lc.id_produit JOIN commande c ON c.id_commande = lc.id_commande WHERE c.id_client = :id_client ORDER BY lc.id_commande DESC');
$detailsStmt->execute(['id_client' => $clientId]);
$orderDetails = $detailsStmt->fetchAll();

$paymentsListStmt = $pdo->prepare('SELECT p.* FROM paiement p JOIN commande c ON c.id_commande = p.id_commande WHERE c.id_client = :id_client ORDER BY p.date_paiement DESC');
$paymentsListStmt->execute(['id_client' => $clientId]);
$payments = $paymentsListStmt->fetchAll();

$deliveriesListStmt = $pdo->prepare('SELECT l.* FROM livraison l JOIN commande c ON c.id_commande = l.id_commande WHERE c.id_client = :id_client ORDER BY l.id_livraison DESC');
$deliveriesListStmt->execute(['id_client' => $clientId]);
$deliveries = $deliveriesListStmt->fetchAll();

$cartTotal = cartTotal($_SESSION['cart'], $productsById);

$dashboardTitle = 'Dashboard client';
$dashboardLead = 'Commandes, panier, paiements FedaPay et suivi de livraison.';
include '../includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<section class="dashboard-summary-grid">
    <article class="metric-card"><strong><?= (int) $stats['orders_count'] ?></strong><span>Commandes</span></article>
    <article class="metric-card"><strong><?= number_format((float) $stats['orders_total'], 0, ',', ' ') ?> FCFA</strong><span>Total commande</span></article>
    <article class="metric-card"><strong><?= $paymentCount ?></strong><span>Paiements</span></article>
    <article class="metric-card"><strong><?= $deliveryCount ?></strong><span>Livraisons actives</span></article>
</section>

<section class="workspace-grid dashboard-section" id="panier">
    <article class="panel-card">
        <h2>Catalogue</h2>
        <div class="client-product-grid">
            <?php foreach ($products as $product): ?>
                <form class="client-product-card" method="POST">
                    <input type="hidden" name="action" value="add_to_cart">
                    <input type="hidden" name="id_produit" value="<?= (int) $product['id_produit'] ?>">
                    <img src="<?= htmlspecialchars($product['image_url'] ?: '../../assets/images/product-cement.svg') ?>" alt="">
                    <h3><?= htmlspecialchars($product['nom_produit']) ?></h3>
                    <p><?= htmlspecialchars($product['nom_categorie'] ?? 'Produit NOCIBE') ?></p>
                    <strong><?= number_format((float) $product['prix'], 0, ',', ' ') ?> FCFA</strong>
                    <div class="inline-form">
                        <input class="form-control" type="number" name="quantite" min="1" max="<?= max(1, (int) $product['quantite_stock']) ?>" value="1">
                        <button class="btn btn-brand" type="submit"><i class="fa-solid fa-cart-plus"></i> Ajouter</button>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>
    </article>

    <aside class="panel-stack">
        <article class="panel-card">
            <h2>Panier</h2>
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="empty-state">Votre panier est vide.</div>
            <?php else: ?>
                <form class="compact-form" method="POST">
                    <input type="hidden" name="action" value="update_cart">
                    <?php foreach ($_SESSION['cart'] as $productId => $quantity): ?>
                        <?php if (!isset($productsById[$productId])) { continue; } ?>
                        <div class="cart-line">
                            <span><?= htmlspecialchars($productsById[$productId]['nom_produit']) ?></span>
                            <input class="form-control" type="number" name="quantites[<?= (int) $productId ?>]" min="0" value="<?= (int) $quantity ?>">
                            <button class="icon-btn" type="submit"><i class="fa-solid fa-rotate"></i></button>
                        </div>
                    <?php endforeach; ?>
                    <strong class="cart-total"><?= number_format($cartTotal, 0, ',', ' ') ?> FCFA</strong>
                </form>
                <?php foreach ($_SESSION['cart'] as $productId => $quantity): ?>
                    <form method="POST" class="cart-remove-form">
                        <input type="hidden" name="action" value="remove_cart">
                        <input type="hidden" name="id_produit" value="<?= (int) $productId ?>">
                        <button class="btn btn-soft w-100" type="submit">Supprimer <?= htmlspecialchars($productsById[$productId]['nom_produit'] ?? 'article') ?></button>
                    </form>
                <?php endforeach; ?>
                <form class="compact-form mt-3" method="POST">
                    <input type="hidden" name="action" value="checkout">
                    <input class="form-control" name="adresse_livraison" placeholder="Adresse de livraison">
                    <button class="btn btn-brand" type="submit"><i class="fa-solid fa-check"></i> Passer la commande</button>
                </form>
            <?php endif; ?>
        </article>
    </aside>
</section>

<section class="workspace-grid dashboard-section" id="commandes">
    <article class="panel-card">
        <h2>Historique des commandes</h2>
        <div class="table-shell">
            <table class="table-modern">
                <thead><tr><th>Commande</th><th>Date</th><th>Montant</th><th>Statut</th><th>Detail</th><th>Paiement</th></tr></thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= (int) $order['id_commande'] ?></td>
                        <td><?= htmlspecialchars($order['date_commande']) ?></td>
                        <td class="table-strong"><?= number_format((float) $order['montant_total'], 0, ',', ' ') ?> FCFA</td>
                        <td><span class="status-pill status-pending"><?= htmlspecialchars($order['statut_commande']) ?></span></td>
                        <td>
                            <?php foreach ($orderDetails as $detail): ?>
                                <?php if ((int) $detail['id_commande'] === (int) $order['id_commande']): ?>
                                    <div><?= htmlspecialchars($detail['nom_produit']) ?> x <?= (int) $detail['quantite'] ?></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <form class="inline-form" method="POST">
                                <input type="hidden" name="action" value="pay_order">
                                <input type="hidden" name="id_commande" value="<?= (int) $order['id_commande'] ?>">
                                <select class="form-select" name="mode_paiement">
                                    <option>FedaPay - MTN MoMo</option>
                                    <option>FedaPay - Moov Money</option>
                                    <option>FedaPay - Carte bancaire</option>
                                </select>
                                <button class="icon-btn" title="Payer"><i class="fa-solid fa-credit-card"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <aside class="panel-stack">
        <article class="panel-card" id="paiements">
            <h2>Paiements</h2>
            <ul class="mini-list">
                <?php foreach ($payments as $payment): ?>
                    <li><span>#<?= (int) $payment['id_commande'] ?> <?= htmlspecialchars($payment['mode_paiement']) ?></span><strong><?= htmlspecialchars($payment['statut_paiement']) ?></strong></li>
                <?php endforeach; ?>
            </ul>
        </article>
        <article class="panel-card" id="livraisons">
            <h2>Livraisons</h2>
            <ul class="mini-list">
                <?php foreach ($deliveries as $delivery): ?>
                    <li><span>#<?= (int) $delivery['id_commande'] ?> <?= htmlspecialchars($delivery['adresse_livraison']) ?></span><strong><?= htmlspecialchars($delivery['statut_livraison']) ?></strong></li>
                <?php endforeach; ?>
            </ul>
        </article>
    </aside>
</section>

        </main>
    </div>
</body>
</html>
