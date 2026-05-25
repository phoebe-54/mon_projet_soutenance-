<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

$accounts = [
    [
        'table' => 'administrateur',
        'nom' => 'Admin',
        'prenom' => '',
        'email' => 'admin@gmail.com',
        'password' => 'admin123?',
        'extraSql' => null,
        'extraParams' => [],
    ],
];

foreach ($accounts as $account) {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('SELECT id_user FROM utilisateur WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $account['email']]);
    $userId = $stmt->fetchColumn();

    if ($userId) {
        $stmt = $pdo->prepare(
            'UPDATE utilisateur
             SET nom = :nom, prenom = :prenom, mot_de_passe = :mot_de_passe, telephone = :telephone
             WHERE id_user = :id_user'
        );
        $stmt->execute([
            'nom' => $account['nom'],
            'prenom' => $account['prenom'],
            'mot_de_passe' => password_hash($account['password'], PASSWORD_DEFAULT),
            'telephone' => '',
            'id_user' => (int) $userId,
        ]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe)
             VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe)'
        );
        $stmt->execute([
            'nom' => $account['nom'],
            'prenom' => $account['prenom'],
            'email' => $account['email'],
            'telephone' => '',
            'mot_de_passe' => password_hash($account['password'], PASSWORD_DEFAULT),
        ]);
        $userId = (int) $pdo->lastInsertId();
    }

    $pdo->prepare("DELETE FROM client WHERE id_user = :id_user")->execute(['id_user' => (int) $userId]);
    $pdo->prepare("DELETE FROM administrateur WHERE id_user = :id_user")->execute(['id_user' => (int) $userId]);
    $pdo->prepare("DELETE FROM commercial WHERE id_user = :id_user")->execute(['id_user' => (int) $userId]);
    $pdo->prepare("DELETE FROM livreur WHERE id_user = :id_user")->execute(['id_user' => (int) $userId]);

    if ($account['table'] === 'administrateur') {
        $stmt = $pdo->prepare('INSERT INTO administrateur (id_user) VALUES (:id_user)');
        $stmt->execute(['id_user' => (int) $userId]);
    } elseif ($account['table'] === 'commercial') {
        $stmt = $pdo->prepare('INSERT INTO commercial (id_user, diplome) VALUES (:id_user, :diplome)');
        $stmt->execute(['id_user' => (int) $userId, 'diplome' => $account['extraParams']['diplome']]);
    } elseif ($account['table'] === 'livreur') {
        $stmt = $pdo->prepare('INSERT INTO livreur (id_user, num_permis, num_plaque) VALUES (:id_user, :num_permis, :num_plaque)');
        $stmt->execute([
            'id_user' => (int) $userId,
            'num_permis' => $account['extraParams']['num_permis'],
            'num_plaque' => $account['extraParams']['num_plaque'],
        ]);
    }

    $pdo->commit();
}

echo "Compte administrateur cree ou mis a jour. Les comptes commercial et livreur doivent etre crees depuis l'espace admin.\n";
