-- Base de données PeaceConnect
CREATE DATABASE IF NOT EXISTS peaceconnect;
USE peaceconnect;

-- Table produits
CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table panier
CREATE TABLE IF NOT EXISTS panier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(50) UNIQUE NOT NULL,
    nom_client VARCHAR(255) NOT NULL,
    email_client VARCHAR(255) NOT NULL,
    telephone_client VARCHAR(20),
    adresse_client TEXT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'livree', 'annulee') DEFAULT 'en_attente',
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table details_commande
CREATE TABLE IF NOT EXISTS details_commande (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion de données de test
INSERT INTO produits (nom, description, prix, stock, image) VALUES
('Nourriture pour les Affamés', 'Kit alimentaire complet pour aider les familles dans le besoin', 29.99, 50, 'téléchargement.jpeg'),
('Éducation pour les Enfants', 'Fournitures scolaires et matériel éducatif pour enfants défavorisés', 5.99, 8, 'enfants-classe.jpg.jpeg'),
('Soins de Santé', 'Kit médical de première nécessité', 19.99, 35, 'téléchargement (2).jpeg'),
('Eau Pure', 'Système de purification d\'eau pour communautés', 15.99, 20, 'téléchargement (1).jpeg'),
('Soutien aux Moyens de Subsistance', 'Outils et ressources pour l\'autonomie économique', 25.99, 15, 'téléchargement (3).jpeg'),
('Logement Digne', 'Matériaux et aide pour construire des habitats décents', 39.99, 10, 'téléchargement (4).jpeg');
