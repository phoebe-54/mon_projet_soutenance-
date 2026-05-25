<?php
require_once '../../includes/auth_check.php';
$currentUser = requireRole('client');
require_once '../../config/database.php';

$clientId = (int) $currentUser['id_user'];
$message = trim($_GET['success'] ?? '');
$error = trim($_GET['error'] ?? '');

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

ensureClientColumn($pdo, 'produit', 'image_url', 'VARCHAR(255) NULL');

$products = $pdo->query('SELECT p.*, c.nom_categorie FROM produit p LEFT JOIN categorie c ON c.id_categorie = p.id_categorie ORDER BY p.nom_produit')->fetchAll();
$productsById = [];
foreach ($products as $product) {
    $productsById[(int) $product['id_produit']] = $product;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
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

            $address = trim($_POST['adresse_livraison'] ?? '');
            if ($address !== '') {
                $pdo->prepare('UPDATE client SET adresse = :adresse WHERE id_user = :id_user')->execute([
                    'adresse' => $address,
                    'id_user' => $clientId,
                ]);
            }

            $_SESSION['cart'] = [];
            $pdo->commit();
            redirectWithFlash('success', 'Commande #' . $orderId . ' enregistree. Vous pouvez maintenant effectuer le paiement.', 'commandes');
        } elseif ($action === 'pay_order') {
            $orderId = (int) ($_POST['id_commande'] ?? 0);
            $mode = $_POST['mode_paiement'] ?? 'FedaPay - Mobile Money';

            $stmt = $pdo->prepare('SELECT id_commande FROM commande WHERE id_commande = :id_commande AND id_client = :id_client LIMIT 1');
            $stmt->execute(['id_commande' => $orderId, 'id_client' => $clientId]);
            if (!$stmt->fetchColumn()) {
                throw new RuntimeException('Commande introuvable.');
            }

            $paidStmt = $pdo->prepare("SELECT 1 FROM paiement WHERE id_commande = :id_commande AND statut_paiement = 'Paye' LIMIT 1");
            $paidStmt->execute(['id_commande' => $orderId]);
            if ($paidStmt->fetchColumn()) {
                throw new RuntimeException('Cette commande est deja payee.');
            }

            $pdo->prepare('INSERT INTO paiement (mode_paiement, statut_paiement, id_commande) VALUES (:mode, :statut, :id_commande)')->execute([
                'mode' => $mode,
                'statut' => 'Paye',
                'id_commande' => $orderId,
            ]);
            $pdo->prepare("UPDATE commande SET statut_commande = 'Confirmee' WHERE id_commande = :id_commande")->execute(['id_commande' => $orderId]);
            redirectWithFlash('success', 'Paiement FedaPay enregistre. Votre commande est confirmee.', 'paiements');
        } elseif ($action === 'update_profile') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $adresse = trim($_POST['adresse'] ?? '');

            if ($nom === '' || $email === '') {
                throw new RuntimeException('Le nom et email sont obligatoires.');
            }

            $pdo->beginTransaction();
            $pdo->prepare('UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone WHERE id_user = :id_user')->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'telephone' => $telephone,
                'id_user' => $clientId,
            ]);
            $pdo->prepare('UPDATE client SET adresse = :adresse WHERE id_user = :id_user')->execute([
                'adresse' => $adresse,
                'id_user' => $clientId,
            ]);
            $_SESSION['nom'] = trim($prenom . ' ' . $nom);
            $_SESSION['email'] = $email;
            if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
                $_SESSION['user']['nom'] = $nom;
                $_SESSION['user']['prenom'] = $prenom;
                $_SESSION['user']['email'] = $email;
            }
            $pdo->commit();
            redirectWithFlash('success', 'Informations personnelles mises a jour.', 'profil');
        }
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        redirectWithFlash('error', 'Operation impossible : ' . $exception->getMessage(), $_POST['redirect_section'] ?? '');
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
$detailsByOrder = [];
foreach ($orderDetails as $detail) {
    $detailsByOrder[(int) $detail['id_commande']][] = $detail;
}

$paymentsListStmt = $pdo->prepare('SELECT p.*, c.montant_total FROM paiement p JOIN commande c ON c.id_commande = p.id_commande WHERE c.id_client = :id_client ORDER BY p.date_paiement DESC');
$paymentsListStmt->execute(['id_client' => $clientId]);
$payments = $paymentsListStmt->fetchAll();
$paidOrders = [];
foreach ($payments as $payment) {
    if ($payment['statut_paiement'] === 'Paye') {
        $paidOrders[(int) $payment['id_commande']] = true;
    }
}

$deliveriesListStmt = $pdo->prepare('SELECT l.* FROM livraison l JOIN commande c ON c.id_commande = l.id_commande WHERE c.id_client = :id_client ORDER BY l.id_livraison DESC');
$deliveriesListStmt->execute(['id_client' => $clientId]);
$deliveries = $deliveriesListStmt->fetchAll();

$profileStmt = $pdo->prepare('SELECT u.*, cl.adresse FROM utilisateur u JOIN client cl ON cl.id_user = u.id_user WHERE u.id_user = :id_user LIMIT 1');
$profileStmt->execute(['id_user' => $clientId]);
$profile = $profileStmt->fetch() ?: [];

$cartTotal = cartTotal($_SESSION['cart'], $productsById);

$dashboardTitle = 'Espace client';
$dashboardLead = 'Commandes, panier, paiements FedaPay et suivi de livraison.';
include '../includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<section class="dashboard-summary-grid admin-dashboard-grid" id="dashboard">
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-receipt"></i><strong><?= (int) $stats['orders_count'] ?></strong><span>Commandes</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-coins"></i><strong><?= number_format((float) $stats['orders_total'], 0, ',', ' ') ?> FCFA</strong><span>Total commande</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-credit-card"></i><strong><?= $paymentCount ?></strong><span>Paiements</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-truck-fast"></i><strong><?= $deliveryCount ?></strong><span>Livraisons actives</span></article>
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
                    <input type="hidden" name="redirect_section" value="panier">
                    <input class="form-control" name="adresse_livraison" value="<?= htmlspecialchars($profile['adresse'] ?? '') ?>" placeholder="Adresse de livraison">
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
                            <details class="admin-edit-panel">
                                <summary class="btn btn-soft">Voir detail</summary>
                                <div class="compact-form mt-2">
                                    <?php foreach (detailsFor($detailsByOrder, (int) $order['id_commande']) as $detail): ?>
                                        <div><?= htmlspecialchars($detail['nom_produit']) ?> x <?= (int) $detail['quantite'] ?> - <?= number_format((float) $detail['sous_total'], 0, ',', ' ') ?> FCFA</div>
                                    <?php endforeach; ?>
                                </div>
                            </details>
                        </td>
                        <td>
                            <?php if (!isset($paidOrders[(int) $order['id_commande']])): ?>
                                <form class="inline-form" method="POST">
                                    <input type="hidden" name="action" value="pay_order">
                                    <input type="hidden" name="redirect_section" value="paiements">
                                    <input type="hidden" name="id_commande" value="<?= (int) $order['id_commande'] ?>">
                                    <select class="form-select" name="mode_paiement">
                                        <option>FedaPay - MTN MoMo</option>
                                        <option>FedaPay - Moov Money</option>
                                        <option>FedaPay - Carte bancaire</option>
                                    </select>
                                    <button class="icon-btn" title="Payer"><i class="fa-solid fa-credit-card"></i></button>
                                </form>
                            <?php else: ?>
                                <span class="status-pill status-pill-admin">Paye</span>
                            <?php endif; ?>
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
            <?php if (!$payments): ?>
                <div class="empty-state">Aucun paiement enregistre.</div>
            <?php else: ?>
                <ul class="mini-list">
                    <?php foreach ($payments as $payment): ?>
                        <li><span>#<?= (int) $payment['id_commande'] ?> <?= htmlspecialchars($payment['mode_paiement']) ?><br><small><?= number_format((float) $payment['montant_total'], 0, ',', ' ') ?> FCFA</small></span><strong><?= htmlspecialchars($payment['statut_paiement']) ?></strong></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
        <article class="panel-card" id="livraisons">
            <h2>Suivi livraison</h2>
            <?php if (!$deliveries): ?>
                <div class="empty-state">Aucune livraison creee pour le moment. Elle apparaitra apres traitement commercial.</div>
            <?php else: ?>
                <ul class="mini-list">
                    <?php foreach ($deliveries as $delivery): ?>
                        <li><span>#<?= (int) $delivery['id_commande'] ?> <?= htmlspecialchars($delivery['adresse_livraison']) ?><br><small>Derniere mise a jour : <?= htmlspecialchars($delivery['date_livraison'] ?? 'En cours') ?></small></span><strong><?= htmlspecialchars($delivery['statut_livraison']) ?></strong></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
    </aside>
</section>

<section class="dashboard-section admin-section" id="profil">
    <article class="panel-card">
        <h2>Informations personnelles</h2>
        <form class="compact-form" method="POST">
            <input type="hidden" name="action" value="update_profile">
            <input type="hidden" name="redirect_section" value="profil">
            <input class="form-control" name="nom" value="<?= htmlspecialchars($profile['nom'] ?? '') ?>" placeholder="Nom" required>
            <input class="form-control" name="prenom" value="<?= htmlspecialchars($profile['prenom'] ?? '') ?>" placeholder="Prenom">
            <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" placeholder="Email" required>
            <input class="form-control" name="telephone" value="<?= htmlspecialchars($profile['telephone'] ?? '') ?>" placeholder="Telephone">
            <textarea class="form-control" name="adresse" placeholder="Adresse"><?= htmlspecialchars($profile['adresse'] ?? '') ?></textarea>
            <button class="btn btn-brand" type="submit">Mettre a jour</button>
        </form>
    </article>
</section>

        </main>
    </div>
</body>
</html>
