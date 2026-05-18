SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS detail_livraison;
DROP TABLE IF EXISTS livraison;
DROP TABLE IF EXISTS paiement;
DROP TABLE IF EXISTS ligne_commande;
DROP TABLE IF EXISTS commande;
DROP TABLE IF EXISTS produit;
DROP TABLE IF EXISTS categorie;
DROP TABLE IF EXISTS livreur;
DROP TABLE IF EXISTS commercial;
DROP TABLE IF EXISTS administrateur;
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS utilisateur;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE utilisateur (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(30) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE client (
    id_user INT PRIMARY KEY,
    adresse VARCHAR(255) NULL,
    CONSTRAINT fk_client_user
        FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE administrateur (
    id_user INT PRIMARY KEY,
    CONSTRAINT fk_administrateur_user
        FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE commercial (
    id_user INT PRIMARY KEY,
    diplome VARCHAR(150) NULL,
    CONSTRAINT fk_commercial_user
        FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE livreur (
    id_user INT PRIMARY KEY,
    num_permis VARCHAR(80) NULL,
    num_plaque VARCHAR(80) NULL,
    CONSTRAINT fk_livreur_user
        FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categorie (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(120) NOT NULL,
    description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE produit (
    id_produit INT AUTO_INCREMENT PRIMARY KEY,
    nom_produit VARCHAR(150) NOT NULL,
    description TEXT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    quantite_stock INT NOT NULL DEFAULT 0,
    id_categorie INT NULL,
    CONSTRAINT fk_produit_categorie
        FOREIGN KEY (id_categorie) REFERENCES categorie(id_categorie)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE commande (
    id_commande INT AUTO_INCREMENT PRIMARY KEY,
    date_commande DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut_commande VARCHAR(60) NOT NULL DEFAULT 'En attente',
    montant_total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    id_client INT NOT NULL,
    CONSTRAINT fk_commande_client
        FOREIGN KEY (id_client) REFERENCES client(id_user)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ligne_commande (
    id_ligne INT AUTO_INCREMENT PRIMARY KEY,
    quantite INT NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    sous_total DECIMAL(10, 2) NOT NULL,
    id_commande INT NOT NULL,
    id_produit INT NOT NULL,
    CONSTRAINT fk_ligne_commande
        FOREIGN KEY (id_commande) REFERENCES commande(id_commande)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_ligne_produit
        FOREIGN KEY (id_produit) REFERENCES produit(id_produit)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE paiement (
    id_paiement INT AUTO_INCREMENT PRIMARY KEY,
    date_paiement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    mode_paiement VARCHAR(80) NOT NULL,
    statut_paiement VARCHAR(60) NOT NULL DEFAULT 'En attente',
    id_commande INT NOT NULL,
    CONSTRAINT fk_paiement_commande
        FOREIGN KEY (id_commande) REFERENCES commande(id_commande)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE livraison (
    id_livraison INT AUTO_INCREMENT PRIMARY KEY,
    date_livraison DATETIME NULL,
    adresse_livraison VARCHAR(255) NOT NULL,
    statut_livraison VARCHAR(60) NOT NULL DEFAULT 'En attente',
    id_commande INT NOT NULL,
    id_user INT NOT NULL,
    CONSTRAINT fk_livraison_commande
        FOREIGN KEY (id_commande) REFERENCES commande(id_commande)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_livraison_user
        FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE detail_livraison (
    id_detail_livraison INT AUTO_INCREMENT PRIMARY KEY,
    quantite_livree INT NOT NULL,
    id_livraison INT NOT NULL,
    id_produit INT NOT NULL,
    CONSTRAINT fk_detail_livraison
        FOREIGN KEY (id_livraison) REFERENCES livraison(id_livraison)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_detail_produit
        FOREIGN KEY (id_produit) REFERENCES produit(id_produit)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
