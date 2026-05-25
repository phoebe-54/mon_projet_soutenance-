<?php
require_once '../../includes/auth_check.php';
requireRole('admin');
require_once '../../config/database.php';
require_once '../../includes/smtp_mailer.php';

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

function userRole(PDO $pdo, int $userId): string
{
    foreach (['administrateur' => 'admin', 'commercial' => 'commercial', 'livreur' => 'livreur', 'client' => 'client'] as $table => $role) {
        $stmt = $pdo->prepare("SELECT 1 FROM {$table} WHERE id_user = :id_user LIMIT 1");
        $stmt->execute(['id_user' => $userId]);
        if ($stmt->fetchColumn()) {
            return $role;
        }
    }

    return 'client';
}

function setUserRole(PDO $pdo, int $userId, string $role): void
{
    foreach (['client', 'administrateur', 'commercial', 'livreur'] as $table) {
        $pdo->prepare("DELETE FROM {$table} WHERE id_user = :id_user")->execute(['id_user' => $userId]);
    }

    if ($role === 'admin') {
        $pdo->prepare('INSERT INTO administrateur (id_user) VALUES (:id_user)')->execute(['id_user' => $userId]);
    } elseif ($role === 'commercial') {
        $pdo->prepare('INSERT INTO commercial (id_user, diplome) VALUES (:id_user, :diplome)')->execute([
            'id_user' => $userId,
            'diplome' => 'Commercial NOCIBE',
        ]);
    } elseif ($role === 'livreur') {
        $pdo->prepare('INSERT INTO livreur (id_user, num_permis, num_plaque) VALUES (:id_user, :num_permis, :num_plaque)')->execute([
            'id_user' => $userId,
            'num_permis' => '',
            'num_plaque' => '',
        ]);
    } else {
        $pdo->prepare('INSERT INTO client (id_user, adresse) VALUES (:id_user, :adresse)')->execute([
            'id_user' => $userId,
            'adresse' => 'A renseigner',
        ]);
    }
}

function uploadProductImage(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException("L'image du produit n'a pas pu etre envoyee.");
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException('Format image non accepte. Utilisez jpg, png, webp ou gif.');
    }

    $uploadDirectory = __DIR__ . '/../../uploads/produits';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true)) {
        throw new RuntimeException('Impossible de creer le dossier des images produits.');
    }

    $fileName = 'produit-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
    $destination = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;

    if (!move_uploaded_file((string) $file['tmp_name'], $destination)) {
        throw new RuntimeException("Impossible d'enregistrer l'image du produit.");
    }

    return '../../uploads/produits/' . $fileName;
}

function generateTemporaryPassword(int $length = 12): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#$%';
    $password = '';
    $maxIndex = strlen($alphabet) - 1;

    for ($i = 0; $i < $length; $i++) {
        $password .= $alphabet[random_int(0, $maxIndex)];
    }

    return $password;
}

function loginUrl(): string
{
    $mailConfig = require __DIR__ . '/../../config/mail.php';
    $configuredAppUrl = rtrim((string) ($mailConfig['app_url'] ?? ''), '/');

    if ($configuredAppUrl !== '') {
        return $configuredAppUrl . '/login.php';
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = dirname(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/nocibe/backoffice/admin/index.php')));
    $basePath = rtrim(str_replace('\\', '/', $basePath), '/');

    return $scheme . '://' . $host . $basePath . '/login.php';
}

function dashboardUrlForRole(string $role): string
{
    $path = match ($role) {
        'commercial' => 'backoffice/commercial/index.php',
        'livreur' => 'backoffice/livreur/index.php',
        'admin' => 'backoffice/admin/index.php',
        default => 'backoffice/client/index.php',
    };

    return rtrim(dirname(loginUrl()), '/') . '/' . $path;
}

function sendAccountPasswordEmail(string $email, string $fullName, string $role, string $password): bool
{
    $subject = 'Votre compte NOCIBE';
    $message = "Bonjour {$fullName},\n\n"
        . "Votre compte {$role} NOCIBE a ete cree.\n\n"
        . "Email : {$email}\n"
        . "Mot de passe temporaire : {$password}\n"
        . "Connexion : " . loginUrl() . "\n\n"
        . "Tableau de bord : " . dashboardUrlForRole($role) . "\n\n"
        . "Connectez-vous puis conservez ce mot de passe en lieu sur.\n\n"
        . "NOCIBE S.A";
    return sendSmtpMail($email, $subject, $message);
}

function redirectWithFlash(string $type, string $message, string $section = ''): void
{
    $params = $_GET;
    unset($params['success'], $params['error']);
    $params[$type] = $message;
    $fragment = preg_replace('/[^a-zA-Z0-9_-]/', '', $section);

    header('Location: index.php?' . http_build_query($params) . ($fragment ? '#' . $fragment : ''));
    exit;
}

ensureColumn($pdo, 'produit', 'image_url', 'VARCHAR(255) NULL');
ensureColumn($pdo, 'utilisateur', 'is_active', 'TINYINT(1) NOT NULL DEFAULT 1');

$message = trim($_GET['success'] ?? '');
$error = trim($_GET['error'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'save_product') {
            $id = (int) ($_POST['id_produit'] ?? 0);
            $uploadedImage = isset($_FILES['image_file']) ? uploadProductImage($_FILES['image_file']) : null;
            $imageUrl = trim($_POST['image_url'] ?? '');
            if ($uploadedImage !== null) {
                $imageUrl = $uploadedImage;
            } elseif ($id > 0 && $imageUrl === '') {
                $imageUrl = trim($_POST['existing_image_url'] ?? '');
            }

            $params = [
                'nom_produit' => trim($_POST['nom_produit'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'prix' => (float) ($_POST['prix'] ?? 0),
                'quantite_stock' => (int) ($_POST['quantite_stock'] ?? 0),
                'id_categorie' => ($_POST['id_categorie'] ?? '') !== '' ? (int) $_POST['id_categorie'] : null,
                'image_url' => $imageUrl,
            ];

            if ($id > 0) {
                $params['id_produit'] = $id;
                $stmt = $pdo->prepare('UPDATE produit SET nom_produit = :nom_produit, description = :description, prix = :prix, quantite_stock = :quantite_stock, id_categorie = :id_categorie, image_url = :image_url WHERE id_produit = :id_produit');
                $stmt->execute($params);
                $message = 'Produit modifie.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO produit (nom_produit, description, prix, quantite_stock, id_categorie, image_url) VALUES (:nom_produit, :description, :prix, :quantite_stock, :id_categorie, :image_url)');
                $stmt->execute($params);
                $message = 'Produit ajoute.';
            }
        } elseif ($action === 'delete_product') {
            $pdo->prepare('DELETE FROM produit WHERE id_produit = :id_produit')->execute(['id_produit' => (int) $_POST['id_produit']]);
            $message = 'Produit supprime.';
        } elseif ($action === 'save_category') {
            $id = (int) ($_POST['id_categorie'] ?? 0);
            $params = [
                'nom_categorie' => trim($_POST['nom_categorie'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
            ];
            if ($id > 0) {
                $params['id_categorie'] = $id;
                $pdo->prepare('UPDATE categorie SET nom_categorie = :nom_categorie, description = :description WHERE id_categorie = :id_categorie')->execute($params);
                $message = 'Categorie modifiee.';
            } else {
                $pdo->prepare('INSERT INTO categorie (nom_categorie, description) VALUES (:nom_categorie, :description)')->execute($params);
                $message = 'Categorie ajoutee.';
            }
        } elseif ($action === 'delete_category') {
            $pdo->prepare('DELETE FROM categorie WHERE id_categorie = :id_categorie')->execute(['id_categorie' => (int) $_POST['id_categorie']]);
            $message = 'Categorie supprimee.';
        } elseif ($action === 'order_status') {
            $pdo->prepare('UPDATE commande SET statut_commande = :statut WHERE id_commande = :id_commande')->execute([
                'statut' => $_POST['statut_commande'] ?? 'En attente',
                'id_commande' => (int) $_POST['id_commande'],
            ]);
            $message = 'Statut de commande mis a jour.';
        } elseif ($action === 'manual_payment') {
            $pdo->prepare('INSERT INTO paiement (mode_paiement, statut_paiement, id_commande) VALUES (:mode, :statut, :id_commande)')->execute([
                'mode' => $_POST['mode_paiement'] ?? 'Especes',
                'statut' => $_POST['statut_paiement'] ?? 'Paye',
                'id_commande' => (int) $_POST['id_commande'],
            ]);
            $message = 'Paiement manuel enregistre.';
        } elseif ($action === 'delivery_assign') {
            $pdo->prepare('UPDATE livraison SET id_user = :id_user, statut_livraison = :statut WHERE id_livraison = :id_livraison')->execute([
                'id_user' => (int) $_POST['id_user'],
                'statut' => $_POST['statut_livraison'] ?? 'Assignee',
                'id_livraison' => (int) $_POST['id_livraison'],
            ]);
            $message = 'Livraison mise a jour.';
        } elseif ($action === 'save_user') {
            $id = (int) ($_POST['id_user'] ?? 0);
            $role = $_POST['role'] ?? 'client';
            if ($id > 0) {
                $pdo->prepare('UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone WHERE id_user = :id_user')->execute([
                    'nom' => trim($_POST['nom'] ?? ''),
                    'prenom' => trim($_POST['prenom'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'telephone' => trim($_POST['telephone'] ?? ''),
                    'id_user' => $id,
                ]);
                setUserRole($pdo, $id, $role);
                $message = 'Utilisateur modifie.';
            } else {
                $email = trim($_POST['email'] ?? '');
                $fullName = trim((trim($_POST['prenom'] ?? '') . ' ' . trim($_POST['nom'] ?? ''))) ?: $email;
                $password = in_array($role, ['commercial', 'livreur'], true)
                    ? generateTemporaryPassword()
                    : ($_POST['password'] ?: 'nocibe123');

                $pdo->beginTransaction();
                $pdo->prepare('INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe) VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe)')->execute([
                    'nom' => trim($_POST['nom'] ?? ''),
                    'prenom' => trim($_POST['prenom'] ?? ''),
                    'email' => $email,
                    'telephone' => trim($_POST['telephone'] ?? ''),
                    'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
                ]);
                setUserRole($pdo, (int) $pdo->lastInsertId(), $role);

                if (in_array($role, ['commercial', 'livreur'], true)) {
                    $emailSent = sendAccountPasswordEmail($email, $fullName, $role, $password);
                    $message = $emailSent
                        ? 'Utilisateur ajoute. Le mot de passe automatique a ete envoye par email.'
                        : 'Utilisateur ajoute, mais l\'email n\'a pas pu etre envoye par XAMPP. Mot de passe temporaire a transmettre manuellement : ' . $password;
                } else {
                    $message = 'Utilisateur ajoute.';
                }

                $pdo->commit();
            }
        } elseif ($action === 'toggle_user') {
            $pdo->prepare('UPDATE utilisateur SET is_active = 1 - is_active WHERE id_user = :id_user')->execute(['id_user' => (int) $_POST['id_user']]);
            $message = 'Statut utilisateur modifie.';
        } elseif ($action === 'delete_user') {
            $currentUserId = (int) ($_SESSION['user_id'] ?? 0);
            $deleteUserId = (int) $_POST['id_user'];
            if ($deleteUserId === $currentUserId) {
                throw new RuntimeException('Vous ne pouvez pas supprimer votre propre compte.');
            }
            $pdo->prepare('DELETE FROM utilisateur WHERE id_user = :id_user')->execute(['id_user' => $deleteUserId]);
            $message = 'Utilisateur supprime.';
        }
        redirectWithFlash('success', $message ?: 'Operation effectuee.', $_POST['redirect_section'] ?? '');
    } catch (Throwable $exception) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        redirectWithFlash('error', 'Operation impossible : ' . $exception->getMessage(), $_POST['redirect_section'] ?? '');
    }
}

$orderFilter = $_GET['statut'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$clientFilter = trim($_GET['client'] ?? '');
$where = [];
$params = [];
if ($orderFilter !== '') {
    $where[] = 'c.statut_commande = :statut';
    $params['statut'] = $orderFilter;
}
if ($dateFilter !== '') {
    $where[] = 'DATE(c.date_commande) = :date_commande';
    $params['date_commande'] = $dateFilter;
}
if ($clientFilter !== '') {
    $where[] = '(u.nom LIKE :client OR u.prenom LIKE :client OR u.email LIKE :client)';
    $params['client'] = '%' . $clientFilter . '%';
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$defaultOrderStatuses = ['En attente', 'Confirmee', 'En livraison', 'Livree', 'Annulee'];
$orderStatuses = $pdo->query('SELECT DISTINCT statut_commande FROM commande WHERE statut_commande <> "" ORDER BY statut_commande')->fetchAll(PDO::FETCH_COLUMN);
$orderStatuses = array_values(array_unique(array_merge($defaultOrderStatuses, $orderStatuses ?: [])));

$stats = [
    'orders' => (int) $pdo->query('SELECT COUNT(*) FROM commande')->fetchColumn(),
    'revenue' => (float) $pdo->query('SELECT COALESCE(SUM(montant_total), 0) FROM commande')->fetchColumn(),
    'deliveries' => (int) $pdo->query("SELECT COUNT(*) FROM livraison WHERE statut_livraison NOT IN ('Livree', 'Annulee')")->fetchColumn(),
    'clients' => (int) $pdo->query('SELECT COUNT(*) FROM client')->fetchColumn(),
];
$categories = $pdo->query('SELECT * FROM categorie ORDER BY nom_categorie')->fetchAll();
$products = $pdo->query('SELECT p.*, c.nom_categorie FROM produit p LEFT JOIN categorie c ON c.id_categorie = p.id_categorie ORDER BY p.id_produit DESC')->fetchAll();
$lowStock = $pdo->query('SELECT nom_produit, quantite_stock FROM produit WHERE quantite_stock <= 0 ORDER BY quantite_stock ASC LIMIT 8')->fetchAll();
$latestOrders = $pdo->query('SELECT c.*, u.nom, u.prenom FROM commande c JOIN utilisateur u ON u.id_user = c.id_client ORDER BY c.date_commande DESC LIMIT 6')->fetchAll();
$stmt = $pdo->prepare("SELECT c.*, u.nom, u.prenom, u.email FROM commande c JOIN utilisateur u ON u.id_user = c.id_client {$whereSql} ORDER BY c.date_commande DESC");
$stmt->execute($params);
$orders = $stmt->fetchAll();
$orderDetails = $pdo->query('SELECT lc.*, p.nom_produit FROM ligne_commande lc JOIN produit p ON p.id_produit = lc.id_produit ORDER BY lc.id_commande DESC')->fetchAll();
$payments = $pdo->query('SELECT p.*, c.montant_total FROM paiement p JOIN commande c ON c.id_commande = p.id_commande ORDER BY p.date_paiement DESC')->fetchAll();
$deliveries = $pdo->query('SELECT l.*, u.nom AS livreur_nom, u.prenom AS livreur_prenom FROM livraison l LEFT JOIN utilisateur u ON u.id_user = l.id_user ORDER BY l.id_livraison DESC')->fetchAll();
$drivers = $pdo->query('SELECT u.id_user, u.nom, u.prenom FROM livreur l JOIN utilisateur u ON u.id_user = l.id_user ORDER BY u.nom')->fetchAll();
$users = $pdo->query('SELECT * FROM utilisateur ORDER BY id_user DESC')->fetchAll();

$dashboardTitle = 'Tableau de bord admin';
$dashboardLead = 'Pilotez les commandes, produits, paiements, livraisons et utilisateurs depuis un seul espace.';
include '../includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<section class="dashboard-summary-grid admin-dashboard-grid" id="dashboard">
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-receipt"></i><strong><?= $stats['orders'] ?></strong><span>Commandes</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-coins"></i><strong><?= number_format($stats['revenue'], 0, ',', ' ') ?> FCFA</strong><span>Chiffre d'affaires</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-truck-fast"></i><strong><?= $stats['deliveries'] ?></strong><span>Livraisons en cours</span></article>
    <article class="metric-card admin-metric-card"><i class="fa-solid fa-users"></i><strong><?= $stats['clients'] ?></strong><span>Clients</span></article>
</section>

<section class="dashboard-section admin-section admin-dashboard-overview">
    <article class="panel-card">
        <h2>Dernieres commandes</h2>
        <div class="table-shell">
            <table class="table-modern">
                <thead><tr><th>Commande</th><th>Client</th><th>Date</th><th>Montant</th><th>Statut</th></tr></thead>
                <tbody>
                <?php foreach ($latestOrders as $order): ?>
                    <tr>
                        <td>#<?= (int) $order['id_commande'] ?></td>
                        <td><?= htmlspecialchars(trim(($order['prenom'] ?? '') . ' ' . $order['nom'])) ?></td>
                        <td><?= htmlspecialchars($order['date_commande']) ?></td>
                        <td class="table-strong"><?= number_format((float) $order['montant_total'], 0, ',', ' ') ?> FCFA</td>
                        <td><span class="status-pill status-pill-admin"><?= htmlspecialchars($order['statut_commande']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="panel-card">
        <h2>Produits en rupture</h2>
        <?php if (!$lowStock): ?>
            <div class="empty-state">Aucun produit en rupture.</div>
        <?php else: ?>
            <ul class="mini-list">
                <?php foreach ($lowStock as $product): ?>
                    <li><span><?= htmlspecialchars($product['nom_produit']) ?></span><strong><?= (int) $product['quantite_stock'] ?></strong></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>
</section>

<section class="dashboard-section admin-section" id="commandes">
    <article class="panel-card">
        <div class="toolbar">
            <h2>Gestion des commandes</h2>
            <form class="inline-form" method="GET" action="index.php#commandes">
                <select class="form-select" name="statut">
                    <option value="">Tous statuts</option>
                    <?php foreach ($orderStatuses as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= $orderFilter === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
                    <?php endforeach; ?>
                </select>
                <input class="form-control" type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>">
                <input class="form-control" type="search" name="client" placeholder="Client" value="<?= htmlspecialchars($clientFilter) ?>">
                <button class="btn btn-brand" type="submit">Filtrer</button>
                <a class="btn btn-soft" href="index.php#commandes">Reinitialiser</a>
            </form>
        </div>
        <?php if ($orderFilter !== '' || $dateFilter !== '' || $clientFilter !== ''): ?>
            <p class="admin-filter-note"><?= count($orders) ?> commande(s) trouvee(s) avec les filtres actifs.</p>
        <?php endif; ?>
        <div class="table-shell">
            <table class="table-modern">
                <thead><tr><th>Commande</th><th>Client</th><th>Montant</th><th>Details</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                <?php if (!$orders): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">Aucune commande ne correspond a ces filtres.</div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= (int) $order['id_commande'] ?><br><small><?= htmlspecialchars($order['date_commande']) ?></small></td>
                        <td><?= htmlspecialchars(trim(($order['prenom'] ?? '') . ' ' . $order['nom'])) ?><br><small><?= htmlspecialchars($order['email']) ?></small></td>
                        <td class="table-strong"><?= number_format((float) $order['montant_total'], 0, ',', ' ') ?> FCFA</td>
                        <td>
                            <?php foreach ($orderDetails as $detail): ?>
                                <?php if ((int) $detail['id_commande'] === (int) $order['id_commande']): ?>
                                    <div><?= htmlspecialchars($detail['nom_produit']) ?> x <?= (int) $detail['quantite'] ?></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                        <td><span class="status-pill status-pill-admin"><?= htmlspecialchars($order['statut_commande']) ?></span></td>
                        <td>
                            <form class="inline-form" method="POST">
                                <input type="hidden" name="action" value="order_status">
                                <input type="hidden" name="redirect_section" value="commandes">
                                <input type="hidden" name="id_commande" value="<?= (int) $order['id_commande'] ?>">
                                <select class="form-select" name="statut_commande">
                                    <?php foreach ($orderStatuses as $status): ?>
                                        <option value="<?= htmlspecialchars($status) ?>" <?= $order['statut_commande'] === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="icon-btn" type="submit" title="Changer le statut"><i class="fa-solid fa-check"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<section class="dashboard-section admin-section admin-products-section" id="produits">
    <div class="admin-product-actions">
        <article class="panel-card product-form-card">
            <h2>Ajouter / modifier un produit</h2>
            <form class="compact-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_product">
                <input type="hidden" name="redirect_section" value="produits">
                <input class="form-control" name="nom_produit" placeholder="Nom du produit" required>
                <textarea class="form-control" name="description" placeholder="Description"></textarea>
                <input class="form-control" type="number" step="0.01" name="prix" placeholder="Prix" required>
                <input class="form-control" type="number" name="quantite_stock" placeholder="Stock" required>
                <select class="form-select" name="id_categorie">
                    <option value="">Categorie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id_categorie'] ?>"><?= htmlspecialchars($category['nom_categorie']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input class="form-control" name="image_url" placeholder="URL de l'image">
                <input class="form-control" type="file" name="image_file" accept="image/*">
                <button class="btn btn-brand" type="submit">Enregistrer</button>
            </form>
        </article>

        <article class="panel-card">
            <h2>Categories</h2>
            <form class="compact-form" method="POST">
                <input type="hidden" name="action" value="save_category">
                <input type="hidden" name="redirect_section" value="produits">
                <input class="form-control" name="id_categorie" placeholder="ID a modifier">
                <input class="form-control" name="nom_categorie" placeholder="Nom de categorie" required>
                <textarea class="form-control" name="description" placeholder="Description"></textarea>
                <button class="btn btn-brand" type="submit">Enregistrer</button>
            </form>
            <ul class="mini-list mt-3">
                <?php foreach ($categories as $category): ?>
                    <li>
                        <span>#<?= (int) $category['id_categorie'] ?> <?= htmlspecialchars($category['nom_categorie']) ?></span>
                        <details class="admin-edit-panel">
                            <summary class="icon-btn" title="Modifier"><i class="fa-solid fa-pen"></i></summary>
                            <form class="compact-form admin-inline-editor" method="POST">
                                <input type="hidden" name="action" value="save_category">
                                <input type="hidden" name="redirect_section" value="produits">
                                <input type="hidden" name="id_categorie" value="<?= (int) $category['id_categorie'] ?>">
                                <input class="form-control" name="nom_categorie" value="<?= htmlspecialchars($category['nom_categorie']) ?>" placeholder="Nom de categorie" required>
                                <textarea class="form-control" name="description" placeholder="Description"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                                <button class="btn btn-brand" type="submit">Mettre a jour</button>
                            </form>
                        </details>
                        <form method="POST">
                            <input type="hidden" name="action" value="delete_category">
                            <input type="hidden" name="redirect_section" value="produits">
                            <input type="hidden" name="id_categorie" value="<?= (int) $category['id_categorie'] ?>">
                            <button class="icon-btn icon-btn-danger" type="submit" onclick="return confirm('Confirmer la suppression de cette categorie ? Cette action est definitive.')"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </article>
    </div>

    <article class="panel-card">
        <h2>Gestion des produits</h2>
        <div class="table-shell">
            <table class="table-modern product-management-table">
                <thead><tr><th>Produit</th><th>Categorie</th><th>Prix</th><th>Stock</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td class="table-product">
                            <img class="table-thumb" src="<?= htmlspecialchars($product['image_url'] ?: '../../assets/images/product-cement.svg') ?>" alt="">
                            <span><?= htmlspecialchars($product['nom_produit']) ?><br><small><?= htmlspecialchars($product['description'] ?? '') ?></small></span>
                        </td>
                        <td><?= htmlspecialchars($product['nom_categorie'] ?? 'Sans categorie') ?></td>
                        <td class="table-strong"><?= number_format((float) $product['prix'], 0, ',', ' ') ?> FCFA</td>
                        <td><?= (int) $product['quantite_stock'] ?></td>
                        <td>
                            <details class="admin-edit-panel">
                                <summary class="icon-btn" title="Modifier"><i class="fa-solid fa-pen"></i></summary>
                                <form class="compact-form admin-inline-editor" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="save_product">
                                    <input type="hidden" name="redirect_section" value="produits">
                                    <input type="hidden" name="id_produit" value="<?= (int) $product['id_produit'] ?>">
                                    <input type="hidden" name="existing_image_url" value="<?= htmlspecialchars($product['image_url'] ?? '') ?>">
                                    <input class="form-control" name="nom_produit" value="<?= htmlspecialchars($product['nom_produit']) ?>" placeholder="Nom du produit" required>
                                    <textarea class="form-control" name="description" placeholder="Description"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                                    <input class="form-control" type="number" step="0.01" name="prix" value="<?= htmlspecialchars((string) $product['prix']) ?>" placeholder="Prix" required>
                                    <input class="form-control" type="number" name="quantite_stock" value="<?= (int) $product['quantite_stock'] ?>" placeholder="Stock" required>
                                    <select class="form-select" name="id_categorie">
                                        <option value="">Sans categorie</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= (int) $category['id_categorie'] ?>" <?= (int) ($product['id_categorie'] ?? 0) === (int) $category['id_categorie'] ? 'selected' : '' ?>><?= htmlspecialchars($category['nom_categorie']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input class="form-control" name="image_url" value="<?= htmlspecialchars($product['image_url'] ?? '') ?>" placeholder="URL de l'image">
                                    <input class="form-control" type="file" name="image_file" accept="image/*">
                                    <button class="btn btn-brand" type="submit">Mettre a jour</button>
                                </form>
                            </details>
                            <form class="inline-form" method="POST">
                                <input type="hidden" name="action" value="delete_product">
                                <input type="hidden" name="redirect_section" value="produits">
                                <input type="hidden" name="id_produit" value="<?= (int) $product['id_produit'] ?>">
                                <button class="icon-btn icon-btn-danger" type="submit" title="Supprimer" onclick="return confirm('Confirmer la suppression de ce produit ? Cette action est definitive.')"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<section class="dashboard-section admin-section admin-stack-section" id="paiements">
    <article class="panel-card">
        <h2>Gestion des paiements</h2>
        <form class="inline-form mb-3" method="POST">
            <input type="hidden" name="action" value="manual_payment">
            <input type="hidden" name="redirect_section" value="paiements">
            <select class="form-select" name="id_commande" required>
                <option value="">Commande</option>
                <?php foreach ($orders as $order): ?><option value="<?= (int) $order['id_commande'] ?>">#<?= (int) $order['id_commande'] ?></option><?php endforeach; ?>
            </select>
            <select class="form-select" name="mode_paiement"><option>Especes</option><option>Virement</option></select>
            <select class="form-select" name="statut_paiement"><option>Paye</option><option>En attente</option><option>Echoue</option></select>
            <button class="btn btn-brand" type="submit">Enregistrer</button>
        </form>
        <div class="table-shell">
            <table class="table-modern">
                <thead><tr><th>Paiement</th><th>Commande</th><th>Mode</th><th>Date</th><th>Statut</th></tr></thead>
                <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr><td>#<?= (int) $payment['id_paiement'] ?></td><td>#<?= (int) $payment['id_commande'] ?></td><td><?= htmlspecialchars($payment['mode_paiement']) ?></td><td><?= htmlspecialchars($payment['date_paiement']) ?></td><td><span class="status-pill status-pill-admin"><?= htmlspecialchars($payment['statut_paiement']) ?></span></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="panel-card" id="livraisons">
        <h2>Gestion des livraisons</h2>
        <div class="table-shell">
            <table class="table-modern">
                <thead><tr><th>Livraison</th><th>Adresse</th><th>Livreur</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($deliveries as $delivery): ?>
                    <tr>
                        <td>#<?= (int) $delivery['id_livraison'] ?><br><small>Commande #<?= (int) $delivery['id_commande'] ?></small></td>
                        <td><?= htmlspecialchars($delivery['adresse_livraison']) ?></td>
                        <td><?= htmlspecialchars(trim(($delivery['livreur_prenom'] ?? '') . ' ' . ($delivery['livreur_nom'] ?? ''))) ?></td>
                        <td><span class="status-pill status-pill-admin"><?= htmlspecialchars($delivery['statut_livraison']) ?></span></td>
                        <td>
                            <form class="inline-form" method="POST">
                                <input type="hidden" name="action" value="delivery_assign">
                                <input type="hidden" name="redirect_section" value="livraisons">
                                <input type="hidden" name="id_livraison" value="<?= (int) $delivery['id_livraison'] ?>">
                                <select class="form-select" name="id_user">
                                    <?php foreach ($drivers as $driver): ?><option value="<?= (int) $driver['id_user'] ?>" <?= (int) $driver['id_user'] === (int) $delivery['id_user'] ? 'selected' : '' ?>><?= htmlspecialchars(trim(($driver['prenom'] ?? '') . ' ' . $driver['nom'])) ?></option><?php endforeach; ?>
                                </select>
                                <select class="form-select" name="statut_livraison">
                                    <?php foreach (['En attente', 'Assignee', 'En cours', 'Livree', 'Annulee'] as $status): ?><option <?= $delivery['statut_livraison'] === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option><?php endforeach; ?>
                                </select>
                                <button class="icon-btn" type="submit" title="Mettre a jour la livraison"><i class="fa-solid fa-check"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<section class="dashboard-section admin-section admin-stack-section" id="utilisateurs">
    <article class="panel-card">
        <h2>Gestion des utilisateurs</h2>
        <div class="table-shell">
            <table class="table-modern">
                <thead><tr><th>Utilisateur</th><th>Telephone</th><th>Role</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?= (int) $user['id_user'] ?> <?= htmlspecialchars(trim(($user['prenom'] ?? '') . ' ' . $user['nom'])) ?><br><small><?= htmlspecialchars($user['email']) ?></small></td>
                        <td><?= htmlspecialchars($user['telephone'] ?? '') ?></td>
                        <?php $role = userRole($pdo, (int) $user['id_user']); ?>
                        <td><span class="status-pill status-pill-admin"><?= htmlspecialchars($role) ?></span></td>
                        <td><span class="status-pill status-pill-admin"><?= ((int) ($user['is_active'] ?? 1)) === 1 ? 'Actif' : 'Desactive' ?></span></td>
                        <td class="row-actions">
                            <details class="admin-edit-panel">
                                <summary class="icon-btn" title="Modifier"><i class="fa-solid fa-pen"></i></summary>
                                <form class="compact-form admin-inline-editor" method="POST">
                                    <input type="hidden" name="action" value="save_user">
                                    <input type="hidden" name="redirect_section" value="utilisateurs">
                                    <input type="hidden" name="id_user" value="<?= (int) $user['id_user'] ?>">
                                    <input class="form-control" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" placeholder="Nom" required>
                                    <input class="form-control" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" placeholder="Prenom">
                                    <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" required>
                                    <input class="form-control" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" placeholder="Telephone">
                                    <select class="form-select" name="role">
                                        <option value="client" <?= $role === 'client' ? 'selected' : '' ?>>Client</option>
                                        <option value="commercial" <?= $role === 'commercial' ? 'selected' : '' ?>>Commercial</option>
                                        <option value="livreur" <?= $role === 'livreur' ? 'selected' : '' ?>>Livreur</option>
                                        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button class="btn btn-brand" type="submit">Mettre a jour</button>
                                </form>
                            </details>
                            <form method="POST"><input type="hidden" name="action" value="toggle_user"><input type="hidden" name="redirect_section" value="utilisateurs"><input type="hidden" name="id_user" value="<?= (int) $user['id_user'] ?>"><button class="icon-btn" type="submit" title="Activer/desactiver" onclick="return confirm('Confirmer le changement de statut de ce compte ?')"><i class="fa-solid fa-power-off"></i></button></form>
                            <form method="POST"><input type="hidden" name="action" value="delete_user"><input type="hidden" name="redirect_section" value="utilisateurs"><input type="hidden" name="id_user" value="<?= (int) $user['id_user'] ?>"><button class="icon-btn icon-btn-danger" type="submit" title="Supprimer" onclick="return confirm('Confirmer la suppression de ce compte ? Cette action est definitive.')"><i class="fa-solid fa-trash"></i></button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <aside class="panel-card">
        <h2>Ajouter / modifier un utilisateur</h2>
        <form class="compact-form" method="POST">
            <input type="hidden" name="action" value="save_user">
            <input type="hidden" name="redirect_section" value="utilisateurs">
            <input class="form-control" name="id_user" placeholder="ID a modifier">
            <input class="form-control" name="nom" placeholder="Nom" required>
            <input class="form-control" name="prenom" placeholder="Prenom">
            <input class="form-control" type="email" name="email" placeholder="Email" required>
            <input class="form-control" name="telephone" placeholder="Telephone">
            <input class="form-control" type="password" name="password" placeholder="Mot de passe client/admin uniquement">
            <select class="form-select" name="role">
                <option value="client">Client</option>
                <option value="commercial">Commercial</option>
                <option value="livreur">Livreur</option>
                <option value="admin">Admin</option>
            </select>
            <small>Pour Commercial et Livreur, le mot de passe est genere automatiquement et envoye a l'adresse email renseignee.</small>
            <button class="btn btn-brand" type="submit">Enregistrer</button>
        </form>
    </aside>
</section>

        </main>
    </div>
</body>
</html>
